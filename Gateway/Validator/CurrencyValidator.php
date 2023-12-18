<?php

namespace SysPay\Payment\Gateway\Validator;

use SysPay\Payment\Gateway\Config\Config;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class CurrencyValidator extends AbstractValidator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param ResultInterfaceFactory $resultFactory
     * @param Config $config
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        Config                 $config
    )
    {
        $this->config = $config;
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $isValid = true;
        $storeId = $validationSubject['storeId'];

        $availableCurrency = explode(
            ',',
            $this->config->getValue(Config::ALLOWED_CURRENCY, $storeId) ?? ''
        );

        if (!in_array($validationSubject['currency'], $availableCurrency)) {
            $isValid = false;
        }

        return $this->createResult($isValid);
    }
}
