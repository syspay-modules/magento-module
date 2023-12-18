<?php

namespace SysPay\Payment\Model;


use SysPay\Payment\Api\CardTokenRepositoryInterface;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Api\Data\CardTokenSearchResultsInterface;
use SysPay\Payment\Command\CardToken;
use SysPay\Payment\Query\CardToken\GetListQuery;
use Magento\Framework\Api\SearchCriteriaInterface;


class CardTokenRepository implements CardTokenRepositoryInterface
{
    /**
     * @var CardToken\SaveCommand
     */
    private $saveCommand;

    /**
     * @var CardToken\LoadByIdCommand
     */
    private $loadByIdCommand;

    /**
     * @var CardToken\LoadByTokenCommand
     */
    private $loadByTokenCommand;

    /**
     * @var CardToken\DeleteByIdCommand
     */
    private $deleteByIdCommand;

    /**
     * @var GetListQuery
     */
    private $getListQuery;

    /**
     * @param CardToken\SaveCommand $saveCommand
     * @param CardToken\LoadByIdCommand $loadByIdCommand
     * @param CardToken\DeleteByIdCommand $deleteByIdCommand
     * @param CardToken\LoadByTokenCommand $loadByTokenCommand
     * @param GetListQuery $getListQuery
     */
    public function __construct(
        CardToken\SaveCommand        $saveCommand,
        CardToken\LoadByIdCommand    $loadByIdCommand,
        CardToken\DeleteByIdCommand  $deleteByIdCommand,
        CardToken\LoadByTokenCommand $loadByTokenCommand,
        GetListQuery                 $getListQuery
    )
    {
        $this->saveCommand = $saveCommand;
        $this->loadByIdCommand = $loadByIdCommand;
        $this->loadByTokenCommand = $loadByTokenCommand;
        $this->deleteByIdCommand = $deleteByIdCommand;
        $this->getListQuery = $getListQuery;
    }

    /**
     * @param CardTokenInterface $item
     * @return int
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(CardTokenInterface $item): int
    {
        return $this->saveCommand->execute($item);
    }

    /**
     * @param CardTokenInterface $item
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(CardTokenInterface $item): void
    {
        $this->deleteByIdCommand->execute($item->getId());
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return CardTokenSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): CardTokenSearchResultsInterface
    {
        return $this->getListQuery->execute($searchCriteria);
    }

    /**
     * @param int $id
     * @return CardTokenInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById(int $id): CardTokenInterface
    {
        return $this->loadByIdCommand->execute($id);
    }

    /**
     * @param string $token
     * @return CardTokenInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByToken(string $token): CardTokenInterface
    {
        return $this->loadByTokenCommand->execute($token);
    }

    /**
     * @param int $id
     * @return void
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById(int $id): void
    {
        $this->deleteByIdCommand->execute($id);
    }
}
