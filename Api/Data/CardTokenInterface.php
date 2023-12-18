<?php

namespace SysPay\Payment\Api\Data;

interface CardTokenInterface
{
    /**
     * String constants for property names
     */
    public const ID = "id";
    public const CUSTOMER_ID = "customer_id";
    public const TOKEN = "token";
    public const STATUS = "status";
    public const CARD_TYPE = "card_type";
    public const CARD_DISPLAY = "card_display";
    public const CREATING_DATE = "creating_date";
    public const EXPIRATION_DATE = "expiration_date";
    public const MANDATE_STATUS = "mandate_status";
    public const MANDATE_CURRENCY = "mandate_currency";
    public const MANDATE_START_DATE = "mandate_start_date";
    public const MANDATE_END_DATE = "mandate_end_date";

    /**
     * Getter for Id.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Setter for Id.
     *
     * @param int|null $id
     *
     * @return void
     */
    public function setId(?int $id): void;

    /**
     * Getter for CustomerId.
     *
     * @return int|null
     */
    public function getCustomerId(): ?int;

    /**
     * Setter for CustomerId.
     *
     * @param int|null $customerId
     *
     * @return void
     */
    public function setCustomerId(?int $customerId): void;

    /**
     * Getter for Token.
     *
     * @return int|null
     */
    public function getToken(): ?int;

    /**
     * Setter for Token.
     *
     * @param int|null $token
     *
     * @return void
     */
    public function setToken(?int $token): void;

    /**
     * Getter for card display.
     *
     * @return string|null
     */
    public function getCardDisplay(): ?string;

    /**
     * Setter for card dispaly.
     *
     * @return void
     */
    public function setCardDisplay(string $cardDisplay): void;

    /**
     * Getter for card type.
     *
     * @return string|null
     */
    public function getCardType(): ?string;

    /**
     * Setter for card type.
     *
     * @return void
     */
    public function setCardType(string $cardType): void;

    /**
     * Getter for Status.
     *
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * Setter for Status.
     *
     * @param string|null $status
     *
     * @return void
     */
    public function setStatus(?string $status): void;

    /**
     * Getter for CreatingDate.
     *
     * @return string|null
     */
    public function getCreatingDate(): ?string;

    /**
     * Setter for CreatingDate.
     *
     * @param string|null $creatingDate
     *
     * @return void
     */
    public function setCreatingDate(?string $creatingDate): void;

    /**
     * Getter for ExpirationDate.
     *
     * @return string|null
     */
    public function getExpirationDate(): ?string;

    /**
     * Setter for ExpirationDate.
     *
     * @param string|null $expirationDate
     *
     * @return void
     */
    public function setExpirationDate(?string $expirationDate): void;

    /**
     * Getter for MandateStatus.
     *
     * @return string|null
     */
    public function getMandateStatus(): ?string;

    /**
     * Setter for MandateStatus.
     *
     * @param string|null $mandateStatus
     *
     * @return void
     */
    public function setMandateStatus(?string $mandateStatus): void;

    /**
     * Getter for MandateCurrency.
     *
     * @return string|null
     */
    public function getMandateCurrency(): ?string;

    /**
     * Setter for MandateCurrency.
     *
     * @param string|null $mandateCurrency
     *
     * @return void
     */
    public function setMandateCurrency(?string $mandateCurrency): void;

    /**
     * Getter for MandateStartDate.
     *
     * @return string|null
     */
    public function getMandateStartDate(): ?string;

    /**
     * Setter for MandateStartDate.
     *
     * @param string|null $mandateStartDate
     *
     * @return void
     */
    public function setMandateStartDate(?string $mandateStartDate): void;

    /**
     * Getter for MandateEndDate.
     *
     * @return string|null
     */
    public function getMandateEndDate(): ?string;

    /**
     * Setter for MandateEndDate.
     *
     * @param string|null $mandateEndDate
     *
     * @return void
     */
    public function setMandateEndDate(?string $mandateEndDate): void;
}
