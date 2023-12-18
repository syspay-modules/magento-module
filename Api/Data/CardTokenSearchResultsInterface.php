<?php

namespace SysPay\Payment\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * CardToken entity search result.
 */
interface CardTokenSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Set items.
     *
     * @param \SysPay\Payment\Api\Data\CardTokenInterface[] $items
     *
     * @return CardTokenSearchResultsInterface
     */
    public function setItems(array $items): CardTokenSearchResultsInterface;

    /**
     * Get items.
     *
     * @return \SysPay\Payment\Api\Data\CardTokenInterface[]
     */
    public function getItems(): array;
}
