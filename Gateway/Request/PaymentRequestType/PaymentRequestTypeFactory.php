<?php

namespace SysPay\Payment\Gateway\Request\PaymentRequestType;


use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\ObjectManager;

class PaymentRequestTypeFactory
{
    /**
     * @var array
     */
    private $types = [];

    /**
     * @param array $types
     */
    public function __construct(
        array         $types = []
    )
    {
        $this->types = $types;
    }

    /**
     * @param string $type
     * @param array $arguments
     * @return mixed
     * @throws LocalizedException
     */
    public function create(string $type, array $arguments = [])
    {
        if (!$this->types[$type]) {
            throw new LocalizedException(__('Payment request type not found.'));
        }
        return ObjectManager::getInstance()->create($this->types[$type], $arguments);
    }
}
