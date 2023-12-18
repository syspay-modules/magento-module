<?php

namespace SysPay\Payment\Gateway\Response\Data;

use Magento\Framework\DataObject;

/**
 * @method string getClass()
 */
class Resolver extends DataObject
{
    const PAYMENT_STATUS_OPEN = 'OPEN';
    const PAYMENT_STATUS_SUCCESS = 'SUCCESS';
    const PAYMENT_STATUS_FAILED = 'FAILED';
    const PAYMENT_STATUS_CANCELLED = 'CANCELLED';
    const PAYMENT_STATUS_AUTHORIZED = 'AUTHORIZED';
    const PAYMENT_STATUS_VOIDED = 'VOIDED';
    const PAYMENT_STATUS_WAITING = 'WAITING';
    const PAYMENT_STATUS_ERROR = 'ERROR';
    const PAYMENT_STATUS_TIMED_OUT = 'TIMED_OUT';
    const PAYMENT_STATUS_EXPIRED = 'EXPIRED';

    const RESPONSE_CLASS_PAYMENT = 'payment';
    const RESPONSE_CLASS_PAYMENT_METHOD = 'payment_method';

    const RESPONSE_CLASS_PAYMENT_METHOD_DETAILS = 'payment_method_details';

    const RESPONSE_CLASS_MANDATE = 'mandate';

    const RESPONSE_CLASS_TOKEN = 'token';

    const RESPONSE_CLASS_REFUND = 'refund';

    private array $initialResponseArray;

    /**
     * @param array $response
     */
    public function __construct(array $response = [])
    {
        parent::__construct();
        $this->initialResponseArray = $response;
        $this->setClass($response['class']);
        $this->build($response);
    }

    public function getClassData(): DataObject
    {
        return $this->getData($this->getClass());
    }

    /**
     * @return DataObject|null
     */
    public function getPayment(): ?DataObject
    {
        return $this->getData(self::RESPONSE_CLASS_PAYMENT);
    }

    /**
     * @return DataObject|null
     */
    public function getRefund(): ?DataObject
    {
        return $this->getData(self::RESPONSE_CLASS_REFUND);
    }

    /**
     * @return DataObject|null
     */
    public function getPaymentMethod(): ?DataObject
    {
        return $this->getData(self::RESPONSE_CLASS_PAYMENT_METHOD);
    }

    /**
     * @return DataObject|null
     */
    public function getPaymentMethodDetails(): ?DataObject
    {
        return $this->getData(self::RESPONSE_CLASS_PAYMENT_METHOD_DETAILS);
    }

    /**
     * @return DataObject|null
     */
    public function getToken(): ?DataObject
    {
        return $this->getData(self::RESPONSE_CLASS_TOKEN);
    }

    /**
     * @return DataObject|null
     */
    public function getMandate(): ?DataObject
    {
        return $this->getData(self::RESPONSE_CLASS_MANDATE);
    }

    /**
     * @param array $responseClassData
     * @return void
     */
    private function build(array $responseClassData)
    {
        foreach ($responseClassData as $key => $value) {
            if (is_array($value)) {
                $this->build($value);
            } else {
                if (!$this->hasData($responseClassData['class'])) {
                    $this->setData($responseClassData['class'], new DataObject());
                }
                $this->getData($responseClassData['class'])->setData($key, $value);
            }
        }
    }

    public function getInitialResponseArray(): array
    {
        return $this->initialResponseArray;
    }
}
