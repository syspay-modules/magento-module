<?php

namespace SysPay\Payment\Gateway\Validator;

use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;

class RefundResponseValidator extends GeneralResponseValidator
{
    public const STATUS_SUCCESS = 'SUCCESS';
    public const STATUS_ERROR = 'ERROR';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_WAITING = 'WAITING';

    /**
     * @var array
     */
    protected array $statusDescription = [];

    /**
     * @param array $statusDescription
     * @param ResultInterfaceFactory $resultFactory
     */
    public function __construct(array $statusDescription, ResultInterfaceFactory $resultFactory)
    {
        $this->statusDescription = $statusDescription;
        parent::__construct($resultFactory);
    }

    /**
     * @param array $validationSubject
     * @return array
     */
    protected function validation(array $validationSubject): array
    {
        $result = parent::validation($validationSubject);
        if ($result['isValid']) {
            $status = $validationSubject['status'];
            if ($status !== self::STATUS_SUCCESS) {
                $result['isValid'] = false;
                $result['errorMessages'][] = !empty($this->statusDescription[$status])
                    ? __($this->statusDescription[$status])
                    : __('Undefined status code from SysPay');
                $result['errorCodes'][] = self::ERROR_CODE_404;
            }
        }

        return $result;
    }
}
