<?php

namespace SysPay\Payment\Api\Customer;

use SysPay\Payment\Api\Data\CardTokenSearchResultsInterface;

interface CardTokenManagementInterface
{
    /**
     * @param int $entityId
     * @return void
     */
    public function deleteTokenById(int $entityId): void;

    /**
     * @return CardTokenSearchResultsInterface
     */
    public function getCardTokenList(): CardTokenSearchResultsInterface;

    /**
     * @param int $entityId
     * @return bool
     */
    public function isTokenAvailable(int $entityId): bool;
}
