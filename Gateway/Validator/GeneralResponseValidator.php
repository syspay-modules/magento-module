<?php

namespace SysPay\Payment\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

class GeneralResponseValidator extends AbstractValidator
{
    protected const ERROR_CODE_404 = 404;

    /**
     * @param array $validationSubject
     * @return \Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject)
    {
        $validation = $this->validation($validationSubject);
        return $this->createResult($validation['isValid'], $validation['errorMessages'], $validation['errorCodes']);
    }

    /**
     * @param array $validationSubject
     * @return array
     */
    protected function validation(array $validationSubject): array
    {
        $result = ['isValid' => true, 'errorMessages' => [], 'errorCodes' => []];
        if (isset($validationSubject['error_code']) && !empty($validationSubject['errors'])) {
            $result['isValid'] = false;
            foreach ($validationSubject['errors'] as $error) {
                $result['errorMessages'][] = $error['message'];
                $result['errorCodes'][] = $error['code'];
            }
        }
        return $result;
    }
}
