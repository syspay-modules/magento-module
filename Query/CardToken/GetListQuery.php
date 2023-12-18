<?php

namespace SysPay\Payment\Query\CardToken;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use SysPay\Payment\Api\Data\CardTokenSearchResultsInterface;
use SysPay\Payment\Api\Data\CardTokenSearchResultsInterfaceFactory;
use SysPay\Payment\Api\GetCardTokenListInterface;
use SysPay\Payment\Mapper\CardTokenDataMapper;
use SysPay\Payment\Model\ResourceModel\CardTokenModel\CardTokenCollection;
use SysPay\Payment\Model\ResourceModel\CardTokenModel\CardTokenCollectionFactory;

/**
 * Get CardToken list by search criteria query.
 */
class GetListQuery implements GetCardTokenListInterface
{
    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var CardTokenCollectionFactory
     */
    private CardTokenCollectionFactory $entityCollectionFactory;

    /**
     * @var CardTokenDataMapper
     */
    private CardTokenDataMapper $entityDataMapper;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var CardTokenSearchResultsInterfaceFactory
     */
    private CardTokenSearchResultsInterfaceFactory $searchResultFactory;

    /**
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CardTokenCollectionFactory $entityCollectionFactory
     * @param CardTokenDataMapper $entityDataMapper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CardTokenSearchResultsInterfaceFactory $searchResultFactory
     */
    public function __construct(
        CollectionProcessorInterface           $collectionProcessor,
        CardTokenCollectionFactory             $entityCollectionFactory,
        CardTokenDataMapper                    $entityDataMapper,
        SearchCriteriaBuilder                  $searchCriteriaBuilder,
        CardTokenSearchResultsInterfaceFactory $searchResultFactory
    )
    {
        $this->collectionProcessor = $collectionProcessor;
        $this->entityCollectionFactory = $entityCollectionFactory;
        $this->entityDataMapper = $entityDataMapper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute(?SearchCriteriaInterface $searchCriteria = null): CardTokenSearchResultsInterface
    {
        /** @var CardTokenCollection $collection */
        $collection = $this->entityCollectionFactory->create();

        if ($searchCriteria === null) {
            $searchCriteria = $this->searchCriteriaBuilder->create();
        } else {
            $this->collectionProcessor->process($searchCriteria, $collection);
        }

        $entityDataObjects = $this->entityDataMapper->map($collection);

        /** @var CardTokenSearchResultsInterface $searchResult */
        $searchResult = $this->searchResultFactory->create();
        $searchResult->setItems($entityDataObjects);
        $searchResult->setTotalCount($collection->getSize());
        $searchResult->setSearchCriteria($searchCriteria);

        return $searchResult;
    }
}
