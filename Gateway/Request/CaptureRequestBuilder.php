<?php

namespace SysPay\Payment\Gateway\Request;

use SysPay\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;


class CaptureRequestBuilder implements BuilderInterface
{

    private const PAYMENT_ID_KEY = 'payment_id';

    /**
     * @var SubjectReader
     */
    private $subjectReader;

    /**
     * @param SubjectReader $subjectReader
     */
    public function __construct(SubjectReader $subjectReader)
    {
        $this->subjectReader = $subjectReader;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        return [
            'body' => [
                self::PAYMENT_ID_KEY => $this->subjectReader->readPayment($buildSubject)->getPayment()->getCcTransId()
            ]
        ];
    }
}
