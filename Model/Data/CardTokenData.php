<?php

namespace SysPay\Payment\Model\Data;

use Magento\Framework\DataObject;
use SysPay\Payment\Api\Data\CardTokenInterface;

class CardTokenData extends DataObject implements CardTokenInterface
{
    /**
     * Getter for Id.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->getData(self::ID) === null ? null
            : (int)$this->getData(self::ID);
    }

    /**
     * Setter for Id.
     *
     * @param int|null $id
     *
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->setData(self::ID, $id);
    }

    /**
     * Getter for CustomerId.
     *
     * @return int|null
     */
    public function getCustomerId(): ?int
    {
        return $this->getData(self::CUSTOMER_ID) === null ? null
            : (int)$this->getData(self::CUSTOMER_ID);
    }

    /**
     * Setter for CustomerId.
     *
     * @param int|null $customerId
     *
     * @return void
     */
    public function setCustomerId(?int $customerId): void
    {
        $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Getter for Token.
     *
     * @return int|null
     */
    public function getToken(): ?int
    {
        return $this->getData(self::TOKEN) === null ? null
            : (int)$this->getData(self::TOKEN);
    }

    /**
     * Setter for Token.
     *
     * @param int|null $token
     *
     * @return void
     */
    public function setToken(?int $token): void
    {
        $this->setData(self::TOKEN, $token);
    }

    /**
     * Getter for Card display.
     *
     * @return string|null
     */
    public function getCardDisplay(): ?string
    {
        return $this->getData(self::CARD_DISPLAY) === null ? null
            : $this->getData(self::CARD_DISPLAY);
    }

    /**
     * Getter for Card display.
     *
     * @param string $cardDisplay
     * @return void
     */
    public function setCardDisplay(string $cardDisplay): void
    {
        $this->setData(self::CARD_DISPLAY, $cardDisplay);
    }

    /**
     * Getter for Card type.
     *
     * @return string|null
     */
    public function getCardType(): ?string
    {
        return $this->getData(self::CARD_TYPE) === null ? null
            : $this->getData(self::CARD_TYPE);
    }

    /**
     * Getter for Card type.
     *
     * @param string $cardType
     * @return void
     */
    public function setCardType(string $cardType): void
    {
        $this->setData(self::CARD_TYPE, $cardType);
    }


    /**
     * Getter for Status.
     *
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Setter for Status.
     *
     * @param string|null $status
     *
     * @return void
     */
    public function setStatus(?string $status): void
    {
        $this->setData(self::STATUS, $status);
    }

    /**
     * Getter for CreatingDate.
     *
     * @return string|null
     */
    public function getCreatingDate(): ?string
    {
        return $this->getData(self::CREATING_DATE);
    }

    /**
     * Setter for CreatingDate.
     *
     * @param string|null $creatingDate
     *
     * @return void
     */
    public function setCreatingDate(?string $creatingDate): void
    {
        $this->setData(self::CREATING_DATE, $creatingDate);
    }

    /**
     * Getter for ExpirationDate.
     *
     * @return string|null
     */
    public function getExpirationDate(): ?string
    {
        return $this->getData(self::EXPIRATION_DATE);
    }

    /**
     * Setter for ExpirationDate.
     *
     * @param string|null $expirationDate
     *
     * @return void
     */
    public function setExpirationDate(?string $expirationDate): void
    {
        $this->setData(self::EXPIRATION_DATE, $expirationDate);
    }

    /**
     * Getter for MandateStatus.
     *
     * @return string|null
     */
    public function getMandateStatus(): ?string
    {
        return $this->getData(self::MANDATE_STATUS);
    }

    /**
     * Setter for MandateStatus.
     *
     * @param string|null $mandateStatus
     *
     * @return void
     */
    public function setMandateStatus(?string $mandateStatus): void
    {
        $this->setData(self::MANDATE_STATUS, $mandateStatus);
    }

    /**
     * Getter for MandateCurrency.
     *
     * @return string|null
     */
    public function getMandateCurrency(): ?string
    {
        return $this->getData(self::MANDATE_CURRENCY);
    }

    /**
     * Setter for MandateCurrency.
     *
     * @param string|null $mandateCurrency
     *
     * @return void
     */
    public function setMandateCurrency(?string $mandateCurrency): void
    {
        $this->setData(self::MANDATE_CURRENCY, $mandateCurrency);
    }

    /**
     * Getter for MandateStartDate.
     *
     * @return string|null
     */
    public function getMandateStartDate(): ?string
    {
        return $this->getData(self::MANDATE_START_DATE);
    }

    /**
     * Setter for MandateStartDate.
     *
     * @param string|null $mandateStartDate
     *
     * @return void
     */
    public function setMandateStartDate(?string $mandateStartDate): void
    {
        $this->setData(self::MANDATE_START_DATE, $mandateStartDate);
    }

    /**
     * Getter for MandateEndDate.
     *
     * @return string|null
     */
    public function getMandateEndDate(): ?string
    {
        return $this->getData(self::MANDATE_END_DATE);
    }

    /**
     * Setter for MandateEndDate.
     *
     * @param string|null $mandateEndDate
     *
     * @return void
     */
    public function setMandateEndDate(?string $mandateEndDate): void
    {
        $this->setData(self::MANDATE_END_DATE, $mandateEndDate);
    }
}
