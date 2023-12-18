<?php

namespace SysPay\Payment\Gateway\Request\MerchantToken;

use SysPay\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class RetrieveRequestBuilder implements BuilderInterface
{

    private const TOKEN = 'token';

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
        $tokenDO = $this->subjectReader->readTokenDo($buildSubject);
        $token = $tokenDO->getToken();
        return [
            'uri' => "merchant/token/$token",
        ];
    }
}
