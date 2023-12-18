<?php

namespace SysPay\Payment\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use SysPay\Payment\Api\Data\CardTokenSearchResultsInterface;

/**
 * Get CardToken list by search criteria query.
 *
 * @api
 */
interface GetCardTokenListInterface
{
    /**
     * Get CardToken list by search criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface|null $searchCriteria
     * @return \SysPay\Payment\Api\Data\CardTokenSearchResultsInterface
     */
    public function execute(?SearchCriteriaInterface $searchCriteria = null): CardTokenSearchResultsInterface;
}
