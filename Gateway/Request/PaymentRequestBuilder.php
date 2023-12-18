<?php

namespace SysPay\Payment\Gateway\Request;

use SysPay\Payment\Gateway\Helper\SubjectReader;
use SysPay\Payment\Gateway\Request\PaymentRequestType\AbstractPaymentRequestTypeBuilder;
use SysPay\Payment\Gateway\Request\PaymentRequestType\PaymentRequestTypeFactory;
use SysPay\Payment\Gateway\Config\Config;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Framework\Exception\LocalizedException;

class PaymentRequestBuilder implements BuilderInterface
{
    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var PaymentRequestTypeFactory
     */
    private $paymentRequestTypeFactory;

    /**
     * @var bool
     */
    private $isAuthorize;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param SubjectReader $subjectReader
     * @param PaymentRequestTypeFactory $paymentRequestTypeFactory
     * @param Config $config
     * @param bool $isAuthorize
     */
    public function __construct(
        SubjectReader             $subjectReader,
        PaymentRequestTypeFactory $paymentRequestTypeFactory,
        Config                    $config,
        bool                      $isAuthorize = false
    )
    {
        $this->subjectReader = $subjectReader;
        $this->isAuthorize = $isAuthorize;
        $this->config = $config;
        $this->paymentRequestTypeFactory = $paymentRequestTypeFactory;
    }

    /**
     * @param array $buildSubject
     * @return array
     * @throws LocalizedException
     */
    public function build(array $buildSubject)
    {
        $paymentRequest = $this->getPaymentRequestObject($buildSubject);


        return ['body' => $paymentRequest->getBody(), 'uri' => $paymentRequest->getUri()];
    }

    /**
     * @param array $buildSubject
     * @return AbstractPaymentRequestTypeBuilder
     * @throws LocalizedException
     */
    protected function getPaymentRequestObject(array $buildSubject): AbstractPaymentRequestTypeBuilder
    {
        $tmPaymentToken = $this->subjectReader->readTmpPaymentToken($buildSubject);
        $isSaveCard = $this->subjectReader->readIsSaveCard($buildSubject);
        $savedCardToken = $this->subjectReader->readSavedCardTokenId($buildSubject);
        $isHostedPage = $this->config->isHostedPageFlow();

        $arguments = [
            'isAuthorize' => $this->isAuthorize,
            'buildSubject' => $buildSubject
        ];

        /** @var  $paymentRequest AbstractPaymentRequestTypeBuilder */
        if ($isHostedPage) {
            if ($savedCardToken) {
                $paymentRequest = $this->paymentRequestTypeFactory->create('hostedPageRedirectWithToken', $arguments);
            } else {
            if($isSaveCard){}
                $paymentRequest = $this->paymentRequestTypeFactory->create('hostedPageRedirect', $arguments);
            }
        } elseif ($tmPaymentToken && !$isSaveCard) {
            $paymentRequest = $this->paymentRequestTypeFactory->create('withTemporaryToken', $arguments);
        } elseif ($tmPaymentToken && $isSaveCard) {
            $paymentRequest = $this->paymentRequestTypeFactory->create('withTemporaryTokenAndSaveCard', $arguments);
        } elseif ($savedCardToken) {
            $paymentRequest = $this->paymentRequestTypeFactory->create('withSavedCardToken', $arguments);
        } else {
            throw new LocalizedException(__('Payment request type not found'));
        }

        return $paymentRequest;
    }
}
