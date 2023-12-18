<?php

namespace SysPay\Payment\Gateway\Request\PaymentRequestType\HostedPage;


use SysPay\Payment\Gateway\Request\PaymentRequestType\AbstractPaymentRequestTypeBuilder;

class Redirect extends AbstractPaymentRequestTypeBuilder
{

    const FLOW_CODE = 'BUYER';

    /**
     * @return array
     */
    public function getBody(): array
    {
        return [
            self::FLOW_KEY => self::FLOW_CODE,
            self::MANDATE_KEY => $this->isMandate(),
            self::EMS_URL_KEY => $this->getEmsUrl(),
            self::RETURN_URL_KEY => $this->getReturnUrl(),
            self::CUSTOMER_KEY => $this->getCustomerData(),
            self::REFERENCE_KEY => $this->getReferenceId(),
            self::AMOUNT_KEY => $this->getAmountInCents(),
            self::CURRENCY_KEY => $this->getCurrencyCode(),
            self::PREAUTH_KEY => $this->isAuthorize,
            self::NOTIFY_KEY => 'EMAIL'
        ];
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return 'merchant/payment';
    }

    /**
     * @return bool
     */
    protected function isMandate(): bool
    {
        $isMandate = parent::isMandate();
        if ($isMandate) {
            return $this->subjectReader->readIsSaveCard($this->buildSubject);
        }
        return false;
    }
}
