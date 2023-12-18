<?php

namespace SysPay\Payment\Gateway\Request\PaymentRequestType;

use SysPay\Payment\Gateway\Config\Config;
use SysPay\Payment\Gateway\Helper\SubjectReader;
use SysPay\Payment\Api\CardTokenRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;

class WithSavedCardToken extends AbstractPaymentRequestTypeBuilder
{
    protected const TOKEN_ID_KEY = 'token_id';

    /**
     * @var CardTokenRepositoryInterface
     */
    protected $cardTokenRepository;

    public function __construct(
        bool                         $isAuthorize,
        array                        $buildSubject,
        SubjectReader                $subjectReader,
        Config                       $config,
        CardTokenRepositoryInterface $cardTokenRepository,
        StoreManagerInterface        $storeManager
    )
    {
        $this->cardTokenRepository = $cardTokenRepository;
        parent::__construct($isAuthorize, $buildSubject, $subjectReader, $storeManager, $config);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        $data = [
            self::FLOW_KEY => self::FLOW_CODE,
            self::AMOUNT_KEY => $this->getAmountInCents(),
            self::REFERENCE_KEY => $this->getReferenceId(),
            self::CURRENCY_KEY => $this->getCurrencyCode(),
            self::PREAUTH_KEY => $this->isAuthorize,
            self::INTERACTIVE_KEY => $this->getIsInteractive(),
            self::EMS_URL_KEY => $this->getEmsUrl(),
            self::RETURN_URL_KEY => $this->getReturnUrl(),
        ];
        if ($this->getCcCid()) {
            $data[self::PAYMENT_METHOD_KEY] = [
                self::PAYMENT_METHOD_TYPE_KEY => self::PAYMENT_METHOD_TYPE_CARD,
                self::TOKEN_ID_KEY => $this->getCardToken(),
                self::CVC_KEY => $this->getCcCid()
            ];
            $data[self::CUSTOMER_KEY] = $this->getCustomerData();
        } else {
            $data[self::TOKEN_ID_KEY] = $this->getCardToken();
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return 'merchant/payment';
    }

    /**
     * @return string|null
     */
    protected function getCcCid(): ?string
    {
        return $this->subjectReader->readCcCid($this->buildSubject);
    }

    /**
     * @return string
     */
    protected function getCardToken(): string
    {
        return $this->cardTokenRepository->getById(
            $this->subjectReader->readSavedCardTokenId($this->buildSubject)
        )->getToken();
    }
}
