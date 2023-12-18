<?php

namespace SysPay\Payment\Gateway\Response;

use SysPay\Payment\Gateway\Config\Config;
use SysPay\Payment\Gateway\Helper\SubjectReader;
use SysPay\Payment\Gateway\Response\Data\ResolverFactory;
use SysPay\Payment\Gateway\Response\Data\Resolver;
use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;

class PaymentTransactionHandler extends GeneralTransactionHandler
{
    protected array $failureCategories;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param SubjectReader $subjectReader
     * @param ManagerInterface $eventManager
     * @param ResolverFactory $responseFactory
     * @param string $transactionType
     * @param array $failureCategories
     * @param array $transactionAdditionalInformationMapping
     */
    public function __construct(
        SubjectReader    $subjectReader,
        ManagerInterface $eventManager,
        ResolverFactory  $responseFactory,
        Config           $config,
        string           $transactionType,
        array            $failureCategories = [],
        array            $transactionAdditionalInformationMapping = [])
    {
        $this->failureCategories = $failureCategories;
        $transactionAdditionalInformationMapping['payment']['failure_category'] = 'failure_category';
        $this->config = $config;
        parent::__construct(
            $subjectReader,
            $eventManager,
            $responseFactory,
            $transactionType,
            $transactionAdditionalInformationMapping
        );
    }

    /**
     * @param array $handlingSubject
     * @param Resolver $response
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function handleTransaction(array $handlingSubject, Resolver $response): void
    {
        /** @var OrderPaymentInterface $payment */
        $payment = $this->subjectReader->readPayment($handlingSubject)->getPayment();

        $paymentResponseData = $response->getPayment();
        $responsePaymentStatus = $paymentResponseData->getStatus();
        $transactionId = $paymentResponseData->getId();

        if ($response->getPaymentMethod()) {
            $payment->setCcLast4($response->getPaymentMethod()->getDisplay());
        }

        $payment->setCcTransId($transactionId);
        $payment->setTransactionId($transactionId);
        $payment->setAdditionalInformation('status', $responsePaymentStatus);

        if ($responsePaymentStatus === Resolver::PAYMENT_STATUS_OPEN && $response->getPayment()->getActionUrl()) {
            $this->setActionUrlRedirect($response, $payment);
        } elseif ($responsePaymentStatus === Resolver::PAYMENT_STATUS_SUCCESS) {
            $payment->setIsTransactionPending(false);
            $payment->setIsTransactionClosed(true);
        } elseif ($responsePaymentStatus === Resolver::PAYMENT_STATUS_AUTHORIZED || $responsePaymentStatus === Resolver::PAYMENT_STATUS_WAITING) {
            $payment->setIsTransactionPending(false);
            $payment->setIsTransactionClosed(false);
        } else {
            $this->handleTransactionFail($response, $payment);
        }
    }

    protected function setActionUrlRedirect(Resolver $responseDO, OrderPaymentInterface $payment): void
    {
        $isHostedPageFlow = $this->config->isHostedPageFlow();
        $orderComment =
            $isHostedPageFlow
                ? __('Redirecting to payment page.')
                : __('3DSecure authentication required. Redirecting to 3DSecure page.');

        $payment->getOrder()
            ->addCommentToStatusHistory($orderComment);

        $payment->setIsTransactionPending(true);
        $payment->setIsTransactionClosed(false);
        $payment->setAdditionalInformation('redirect_url', $responseDO->getPayment()->getActionUrl());
    }

    protected function handleTransactionFail(Resolver $responseDO, OrderPaymentInterface $payment): void
    {
        $paymentResponseData = $responseDO->getPayment();
        $responsePaymentStatus = $paymentResponseData->getStatus();

        $payment->setIsTransactionPending(true);

        $responseFailureCategory = $paymentResponseData->getFailureCategory() ?? null;
        if ($responsePaymentStatus === Resolver::PAYMENT_STATUS_FAILED) {
            $errorMessage = $this->getErrorMessage($responseFailureCategory);
            if ($responseFailureCategory == 'fraud_suspicious') {
                $payment->setIsFraudDetected(true);
            }
        } elseif ($responsePaymentStatus === Resolver::PAYMENT_STATUS_ERROR) {
            $errorMessage = 'An error occurred while processing the payment';
        } elseif ($responsePaymentStatus === Resolver::PAYMENT_STATUS_EXPIRED) {
            $errorMessage = 'The authorized payment has expired because it was not captured or voided.';
        } else {
            $errorMessage = 'Undefined status code from SysPay';
        }

        $errorMessage = __($errorMessage);

        $order = $payment->getOrder();
        $order->addCommentToStatusHistory(__('Payment failed: %1', $errorMessage));

        $payment->setAdditionalInformation('failure_category', $responseFailureCategory);
        $payment->setAdditionalInformation('failure_message', $errorMessage->__toString());
    }

    protected function getErrorMessage(string $responseFailureCategory): string
    {
        return !empty($this->failureCategories[$responseFailureCategory])
            ? $this->failureCategories[$responseFailureCategory]
            : 'Payment failed';
    }
}
