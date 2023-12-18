<?php

namespace SysPay\Payment\Model;

use Magento\Framework\Api\SearchResults;
use SysPay\Payment\Api\Data\CardTokenSearchResultsInterface;

/**
 * CardToken entity search results implementation.
 */
class CardTokenSearchResults extends SearchResults implements CardTokenSearchResultsInterface
{
    /**
     * Set items list.
     *
     * @param array $items
     *
     * @return CardTokenSearchResultsInterface
     */
    public function setItems(array $items): CardTokenSearchResultsInterface
    {
        return parent::setItems($items);
    }

    /**
     * Get items list.
     *
     * @return array
     */
    public function getItems(): array
    {
        return parent::getItems();
    }
}
