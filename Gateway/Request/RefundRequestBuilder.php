<?php

namespace SysPay\Payment\Gateway\Request;

use SysPay\Payment\Gateway\Helper\SubjectReader;
use SysPay\Payment\Gateway\Config\Config;
use Magento\Payment\Gateway\Request\BuilderInterface;

class RefundRequestBuilder implements BuilderInterface
{
    private const PAYMENT_ID_KEY = 'payment_id';
    private const REFERENCE_KEY = 'reference';
    private const AMOUNT_KEY = 'amount';
    private const CURRENCY_KEY = 'currency';
    private const EMS_URL_KEY = 'ems_url';


    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param SubjectReader $subjectReader
     * @param Config $config
     */
    public function __construct(
        SubjectReader $subjectReader,
        Config        $config,
    )
    {
        $this->config = $config;
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $paymentDO = $this->subjectReader->readPayment($buildSubject);
        $payment = $paymentDO->getPayment();
        return [
            'body' => [
                self::PAYMENT_ID_KEY => $payment->getCcTransId(),
                self::REFERENCE_KEY => $payment->getTransactionId(),
                self::AMOUNT_KEY => $this->subjectReader->readAmountInCents($buildSubject),
                self::CURRENCY_KEY => $this->subjectReader->readCurrencyCode($buildSubject),
                //self::EMS_URL_KEY => $this->config->getEmsUrl()
            ]
        ];
    }
}
