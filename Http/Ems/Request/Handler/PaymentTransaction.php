<?php

namespace SysPay\Payment\Http\Ems\Request\Handler;

use SysPay\Payment\Exception\SysPayEmsCanNotUpdatePaymentException;
use SysPay\Payment\Gateway\Response\Data\Resolver;
use SysPay\Payment\Gateway\Response\Data\Resolver as DataResolver;
use SysPay\Payment\Gateway\Response\GeneralTransactionHandler;
use SysPay\Payment\Gateway\Response\PaymentTransactionHandler;
use SysPay\Payment\Http\Ems\Request\AbstractHandler;
use SysPay\Payment\Api\Data\TransactionInterface;
use SysPay\Payment\Gateway\Config\Config;
use SysPay\Payment\Mapper\OrderStatusMapper;
use Magento\Framework\Model\AbstractModel;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Sales\Api\OrderPaymentRepositoryInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Payment\State\AuthorizeCommand;
use Magento\Sales\Model\Order\Payment\State\CaptureCommand;
use Magento\Framework\Exception\InputException;
use Magento\Sales\Model\Order;

class PaymentTransaction extends AbstractHandler
{

    const GATEWAY_RESPONSE_TRANSACTION_TYPE = 'ems_update_payment_status';

    protected GeneralTransactionHandler $transactionHandler;
    protected Config $config;
    protected PaymentDataObjectFactory $paymentDataObjectFactory;
    protected TransactionRepositoryInterface $transactionRepository;
    protected OrderPaymentRepositoryInterface $orderPaymentRepository;
    protected OrderRepositoryInterface $orderRepository;
    protected OrderCollectionFactory $orderCollectionFactory;
    protected OrderStatusMapper $orderStatusMapper;
    protected AuthorizeCommand $authorizeCommand;
    protected CaptureCommand $captureCommand;

    public function __construct(
        string                          $type,
        ManagerInterface                $eventManager,
        GeneralTransactionHandler       $transactionHandler,
        Config                          $config,
        PaymentDataObjectFactory        $paymentDataObjectFactory,
        TransactionRepositoryInterface  $transactionRepository,
        OrderPaymentRepositoryInterface $orderPaymentRepository,
        OrderRepositoryInterface        $orderRepository,
        OrderCollectionFactory          $orderCollectionFactory,
        OrderStatusMapper               $orderStatusMapper,
        AuthorizeCommand                $authorizeCommand,
        CaptureCommand                  $captureCommand
    )
    {
        $this->transactionHandler = $transactionHandler;
        $this->config = $config;
        $this->paymentDataObjectFactory = $paymentDataObjectFactory;
        $this->transactionRepository = $transactionRepository;
        $this->orderPaymentRepository = $orderPaymentRepository;
        $this->orderRepository = $orderRepository;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderStatusMapper = $orderStatusMapper;
        $this->authorizeCommand = $authorizeCommand;
        $this->captureCommand = $captureCommand;
        parent::__construct($type, $eventManager);
    }

    /**
     * @param DataResolver $requestDO
     * @return array
     * @throws InputException
     * @throws LocalizedException
     * @throws \Exception
     */
    protected function resolve(DataResolver $requestDO): array
    {
        $paymentDO = $this->getPaymentDO($requestDO);
        /** @var InfoInterface|OrderPaymentInterface $payment */
        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();
        /** @var AbstractModel|TransactionInterface $transaction */
        $transaction = $this->getTransaction($payment, $requestDO);

        if ($order->getState() === Order::STATE_CLOSED) {
            throw new SysPayEmsCanNotUpdatePaymentException(__('Order is closed'), $payment);
        }

        if ($payment->getAdditionalInformation('status') === $requestDO->getPayment()->getStatus()) {
            throw new SysPayEmsCanNotUpdatePaymentException(__('Payment status is already updated'), $payment);
        }

        $this->handleTransaction($paymentDO, $requestDO);

        $transactionAdditionalInfo = $payment->getTransactionAdditionalInfo();
        if ($transactionAdditionalInfo) {
            $transactionAdditionalInfo = $transactionAdditionalInfo[Transaction::RAW_DETAILS];
            $transaction->unsAdditionalInformation(Transaction::RAW_DETAILS);
            $transaction->setAdditionalInformation(Transaction::RAW_DETAILS, $transactionAdditionalInfo);
        }
        $requestPaymentStatus = $requestDO->getPayment()->getStatus();

        if ($requestPaymentStatus === Resolver::PAYMENT_STATUS_AUTHORIZED) {
            $stateMessage = $this->authorizeCommand->execute($payment, $payment->getAmountAuthorized(), $order);
        } else if ($requestPaymentStatus === Resolver::PAYMENT_STATUS_SUCCESS) {
            $stateMessage = $this->captureCommand->execute($payment, $payment->getAmountAuthorized(), $order);
        } else {
            $order->setStatus($this->orderStatusMapper->getOrderStatusByPaymentStatus($requestPaymentStatus));
            $order->setState($this->orderStatusMapper->getOrderStateByPaymentStatus($requestPaymentStatus));
            $stateMessage = null;
        }

        if ($stateMessage) {
            $order->addCommentToStatusHistory($stateMessage);
        }

        $this->orderRepository->save($order);
        $this->transactionRepository->save($transaction);
        $this->orderPaymentRepository->save($payment);

        return ['order' => $order, 'payment_do' => $paymentDO, 'transaction' => $transaction];
    }

    /**
     * @param PaymentDataObjectInterface $paymentDO
     * @param DataResolver $requestDO
     * @return PaymentDataObjectInterface
     * @throws \Exception
     */
    protected function handleTransaction(PaymentDataObjectInterface $paymentDO, DataResolver $requestDO): PaymentDataObjectInterface
    {
        $this->getTransactionHandler($requestDO)
            ->handle(
                ['payment' => $paymentDO],
                $requestDO->getInitialResponseArray()
            );
        return $paymentDO;
    }

    /**
     * @param DataResolver $requestDO
     * @return PaymentDataObjectInterface
     * @throws LocalizedException
     */
    protected function getPaymentDO(DataResolver $requestDO): PaymentDataObjectInterface
    {
        return $this->paymentDataObjectFactory->create($this->getPayment($requestDO));
    }

    /**
     * @param DataResolver $requestDO
     * @return GeneralTransactionHandler
     * @throws \Exception
     */
    protected function getTransactionHandler(DataResolver $requestDO): GeneralTransactionHandler
    {
        $paymentStatus = $requestDO->getPayment()->getStatus();

        if ($this->transactionHandler instanceof PaymentTransactionHandler) {
            $this->transactionHandler->setTransactionType(self::GATEWAY_RESPONSE_TRANSACTION_TYPE);
            if ($paymentStatus === Resolver::PAYMENT_STATUS_AUTHORIZED) {
                $this->transactionHandler
                    ->addTransactionAdditionalInformationMapping('payment', 'preauth_expiration_date');
            }
            return $this->transactionHandler;
        } else {
            throw new \Exception('Transaction handler is not an instance of PaymentTransactionHandler');
        }
    }

    /**
     * @param InfoInterface $payment
     * @param DataResolver $requestDO
     * @return AbstractModel
     * @throws InputException
     */
    protected function getTransaction(InfoInterface $payment, DataResolver $requestDO): AbstractModel
    {
        return $this->transactionRepository->getByTransactionId(
            $payment->getLastTransId(),
            $payment->getId(),
            $payment->getOrder()->getId()
        );
    }

    /**
     * @param DataResolver $requestDO
     * @return InfoInterface
     * @throws LocalizedException
     */
    protected function getPayment(DataResolver $requestDO): InfoInterface
    {
        if (!$requestDO->getPayment()->getReference()) {
            throw new LocalizedException(__('Invalid order reference'));
        }
        $reference = explode('_', $requestDO->getPayment()->getReference())[0];
        if (!$reference) {
            throw new LocalizedException(__('Invalid order reference'));
        }

        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter('increment_id', $reference);
        $order = $orderCollection->getFirstItem();

        if (!$order->getId()) {
            throw new LocalizedException(__('Order not found'));
        }

        return $order->getPayment();
    }
}
