<?php

namespace SysPay\Payment\Gateway\Request\PaymentRequestType\HostedPage;

use SysPay\Payment\Gateway\Request\PaymentRequestType\WithSavedCardToken;

class RedirectWithToken extends WithSavedCardToken
{
    const FLOW_CODE = Redirect::FLOW_CODE;

    /**
     * @return array
     */
    public function getBody(): array
    {
        return [
            self::FLOW_KEY => self::FLOW_CODE,
            self::AMOUNT_KEY => $this->getAmountInCents(),
            self::REFERENCE_KEY => $this->getReferenceId(),
            self::CURRENCY_KEY => $this->getCurrencyCode(),
            self::PREAUTH_KEY => $this->isAuthorize,
            self::INTERACTIVE_KEY => $this->getIsInteractive(),
            self::EMS_URL_KEY => $this->getEmsUrl(),
            self::RETURN_URL_KEY => $this->getReturnUrl(),
            self::NOTIFY_KEY => 'EMAIL',
            self::CUSTOMER_KEY => $this->getCustomerData(),
            self::PAYMENT_METHOD_KEY => [
                self::PAYMENT_METHOD_TYPE_KEY => self::PAYMENT_METHOD_TYPE_CARD,
                self::TOKEN_ID_KEY => $this->getCardToken(),
            ],
        ];
    }


    protected function getIsInteractive(): int
    {
        return 0;
    }
}
