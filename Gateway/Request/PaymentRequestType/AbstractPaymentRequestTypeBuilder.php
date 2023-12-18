<?php

namespace SysPay\Payment\Gateway\Request\PaymentRequestType;

use Magento\Store\Api\Data\StoreInterface;
use SysPay\Payment\Gateway\Helper\SubjectReader;
use SysPay\Payment\Gateway\Config\Config;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\AddressAdapterInterface;
use Magento\Store\Model\StoreManagerInterface;

abstract class AbstractPaymentRequestTypeBuilder
{
    protected const FLOW_KEY = 'flow';
    protected const MANDATE_KEY = 'mandate';
    protected const PREAUTH_KEY = 'preauth';
    protected const REFERENCE_KEY = 'reference';
    protected const INTERACTIVE_KEY = 'interactive';
    protected const AMOUNT_KEY = 'amount';
    protected const CURRENCY_KEY = 'currency';
    protected const EMS_URL_KEY = 'ems_url';
    protected const RETURN_URL_KEY = 'return_url';
    protected const CUSTOMER_KEY = 'customer';
    protected const FIRSTNAME_KEY = 'firstname';
    protected const LASTNAME_KEY = 'lastname';
    protected const EMAIL_KEY = 'email';
    protected const IP_KEY = 'ip';
    protected const ADDRESS_COUNTRY_KEY = 'address_country';
    protected const BILLING_ADDRESS_KEY = 'billing_address';
    protected const ADDRESS1_KEY = 'address1';
    protected const ADDRESS2_KEY = 'address2';
    protected const CITY_KEY = 'city';
    protected const COUNTRY_KEY = 'country';
    protected const POSTAL_CODE_KEY = 'postal_code';
    protected const PAYMENT_METHOD_KEY = 'payment_method';
    protected const PAYMENT_KEY = 'payment';
    protected const TOKEN_KEY_KEY = 'token_key';
    protected const CVC_KEY = 'cvc';
    protected const NOTIFY_KEY = 'notify';
    protected const FLOW_CODE = 'API';

    protected const PAYMENT_METHOD_TYPE_KEY = 'type';
    protected const PAYMENT_METHOD_TYPE_CARD = 'CREDITCARD';

    protected $subjectReader;
    protected $config;
    protected $storeManager;

    protected array $buildSubject;
    protected bool $isAuthorize;

    /**
     * @param bool $isAuthorize
     * @param array $buildSubject
     * @param SubjectReader $subjectReader
     * @param Config $config
     */
    public function __construct(
        bool                  $isAuthorize,
        array                 $buildSubject,
        SubjectReader         $subjectReader,
        StoreManagerInterface $storeManager,
        Config                $config
    )
    {
        $this->isAuthorize = $isAuthorize;
        $this->buildSubject = $buildSubject;
        $this->subjectReader = $subjectReader;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @return array
     */
    abstract public function getBody(): array;

    /**
     * @return string
     */
    abstract public function getUri(): string;

    /**
     * @return string
     */
    protected function getClientIp(): string
    {
        return $this->subjectReader->readIpAddress($this->buildSubject);
    }

    /**
     * @return OrderAdapterInterface
     */
    protected function getOrderDA(): OrderAdapterInterface
    {
        return $this->subjectReader->readOrderDataAdapter($this->buildSubject);
    }

    /**
     * @return AddressAdapterInterface
     */
    protected function getBillingAddress(): AddressAdapterInterface
    {
        return $this->getOrderDA()->getBillingAddress();
    }

    /**
     * @return float
     */
    protected function getAmountInCents(): float
    {
        return $this->subjectReader->readAmountInCents($this->buildSubject);
    }

    /**
     * @return string
     */
    protected function getCurrencyCode(): string
    {
        return $this->subjectReader->readCurrencyCode($this->buildSubject);
    }

    /**
     * @return string
     */
    protected function getReferenceId(): string
    {
        return $this->getOrderDA()->getOrderIncrementId() . '_' . time();
    }

    /**
     * @return int
     */
    protected function getIsInteractive(): int
    {
        return 1;
    }

    /**
     * @return array
     */
    protected function getCustomerData(): array
    {
        return [
            self::FIRSTNAME_KEY => $this->getBillingAddress()->getFirstname(),
            self::LASTNAME_KEY => $this->getBillingAddress()->getLastname(),
            self::EMAIL_KEY => $this->getBillingAddress()->getEmail(),
            self::IP_KEY => $this->getClientIp(),
            self::ADDRESS_COUNTRY_KEY => $this->getBillingAddress()->getCountryId(),
            self::BILLING_ADDRESS_KEY => [
                self::ADDRESS1_KEY => $this->getBillingAddress()->getStreetLine1(),
                self::ADDRESS2_KEY => $this->getBillingAddress()->getStreetLine2(),
                self::CITY_KEY => $this->getBillingAddress()->getCity(),
                self::COUNTRY_KEY => $this->getBillingAddress()->getCountryId(),
                self::POSTAL_CODE_KEY => $this->getBillingAddress()->getPostcode(),
            ],
        ];
    }

    /**
     * @return StoreInterface|null
     */
    protected function getDefaultStoreView(): ?StoreInterface
    {
        return $this->storeManager->getDefaultStoreView();
    }

    /**
     * @return string
     */
    protected function getEmsUrl(): string
    {
        return $this->getBaseUrl() . 'syspay/ems/handler';
    }

    /**
     * @return string
     */
    protected function getReturnUrl(): string
    {
        return $this->getBaseUrl() . 'syspay/redirect/handler';
    }

    /**
     * @return bool
     */
    protected function isMandate(): bool
    {
        return (bool)$this->getOrderDA()->getCustomerId();
    }

    /**
     * @return string
     */
    protected function getBaseUrl(): string
    {
        return $this->getDefaultStoreView()->getBaseUrl();
    }
}
