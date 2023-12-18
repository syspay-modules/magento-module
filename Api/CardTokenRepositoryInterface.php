<?php

namespace SysPay\Payment\Api;

use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Api\Data\CardTokenSearchResultsInterface;
use Magento\Framework\Api\SearchCriteriaInterface;

interface CardTokenRepositoryInterface
{

    /**
     * @param CardTokenInterface $item
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CardTokenInterface $item): int;

    /**
     * @param CardTokenInterface $item
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(CardTokenInterface $item): void;

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return CardTokenSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): CardTokenSearchResultsInterface;

    /**
     * @param int $id
     * @return CardTokenInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): CardTokenInterface;

    /**
     * @param int $id
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): void;

    /**
     * @param string $token
     * @return CardTokenInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByToken(string $token): CardTokenInterface;

}
