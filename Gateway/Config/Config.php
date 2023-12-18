<?php

namespace SysPay\Payment\Gateway\Config;

use SysPay\Payment\Model\Adminhtml\Source\Environment;
use SysPay\Payment\Model\Adminhtml\Source\PaymentFlow;

class Config extends \Magento\Payment\Gateway\Config\Config
{
    private const API_URL = 'https://app.syspay.com/api/';
    private const API_URL_SANDBOX = 'https://app-sandbox.syspay.com/api/';
    private const API_VERSION = 'v2';
    public const MERCHANT_ID = 'merchant_id';
    public const PUBLIC_KEY = 'public_key';
    public const PRIVATE_KEY = 'private_key';
    public const SANDBOX_MERCHANT_ID = 'sandbox_merchant_id';
    public const SANDBOX_PUBLIC_KEY = 'sandbox_public_key';
    public const SANDBOX_PRIVATE_KEY = 'sandbox_private_key';
    public const ENVIRONMENT = 'environment';
    public const CLIENT_TOKENIZATION_SCRIPT = 'client_side_tokenization_script';
    public const ALLOWED_CURRENCY = 'allowedcurrency';
    public const IS_SAVE_CARD_ENABLED = 'is_save_card_enabled';

    public const PAYMENT_ACTION = 'payment_action';

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return (bool)$this->getValue('active');
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        if ($this->isSandboxMode()) {
            return self::API_URL_SANDBOX . self::API_VERSION . '/';
        }
        return self::API_URL . self::API_VERSION . '/';
    }

    /**
     * @return bool
     */
    public function isSandboxMode(): bool
    {
        return $this->getValue(self::ENVIRONMENT) === Environment::ENVIRONMENT_SANDBOX;
    }

    /**
     * @return string|null
     */
    public function getMerchantId(): ?string
    {
        return $this->isSandboxMode()
            ? $this->getValue(self::SANDBOX_MERCHANT_ID)
            : $this->getValue(self::MERCHANT_ID);
    }

    /**
     * @return string|null
     */
    public function getPublicKey(): ?string
    {
        return $this->isSandboxMode()
            ? $this->getValue(self::SANDBOX_PUBLIC_KEY)
            : $this->getValue(self::PUBLIC_KEY);
    }

    /**
     * @return string|null
     */
    public function getPrivateKey(): ?string
    {
        return $this->isSandboxMode()
            ? $this->getValue(self::SANDBOX_PRIVATE_KEY)
            : $this->getValue(self::PRIVATE_KEY);
    }

    /**
     * @return bool
     */
    public function isSaveCardEnabled(): bool
    {
        return (bool)$this->getValue(self::IS_SAVE_CARD_ENABLED);
    }

    /**
     * @return string
     */
    public function getPaymentAction(): string
    {
        return $this->getValue(self::PAYMENT_ACTION);
    }

    /**
     * @return bool
     */
    public function isDebugEnable(): bool
    {
        return (bool)$this->getValue('debug');
    }

    /**
     * @return bool
     */
    public function isHostedPageFlow(): bool
    {
        return $this->getValue('payment_flow') === PaymentFlow::HOSTED_PAGE;
    }

    /**
     * @return bool
     */
    public function isServerToServerFlow(): bool
    {
        return $this->getValue('payment_flow') === PaymentFlow::SERVER_TO_SERVER;
    }
}
