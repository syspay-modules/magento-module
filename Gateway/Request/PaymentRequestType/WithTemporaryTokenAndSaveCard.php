<?php

namespace SysPay\Payment\Gateway\Request\PaymentRequestType;

class WithTemporaryTokenAndSaveCard extends AbstractPaymentRequestTypeBuilder
{
    /**
     * @return array
     */
    public function getBody(): array
    {
        return [
            self::FLOW_KEY => self::FLOW_CODE,
            self::INTERACTIVE_KEY => $this->getIsInteractive(),
            self::EMS_URL_KEY => $this->getEmsUrl(),
            self::RETURN_URL_KEY => $this->getReturnUrl(),
            self::MANDATE_KEY => true,
            self::CUSTOMER_KEY => $this->getCustomerData(),
            self::PAYMENT_METHOD_KEY => [
                self::TOKEN_KEY_KEY => $this->getTmpPaymentToken(),
            ],
            self::PAYMENT_KEY => [
                self::REFERENCE_KEY => $this->getReferenceId(),
                self::AMOUNT_KEY => $this->getAmountInCents(),
                self::CURRENCY_KEY => $this->getCurrencyCode(),
                self::PREAUTH_KEY => $this->isAuthorize,
            ]
        ];
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return 'merchant/token';
    }

    /**
     * @return string
     */
    protected function getTmpPaymentToken(): string
    {
        return $this->subjectReader->readTmpPaymentToken($this->buildSubject);
    }
}
