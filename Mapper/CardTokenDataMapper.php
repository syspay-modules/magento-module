<?php

namespace SysPay\Payment\Mapper;

use Magento\Framework\DataObject;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Api\Data\CardTokenInterfaceFactory;
use SysPay\Payment\Model\CardTokenModel;

/**
 * Converts a collection of CardToken entities to an array of data transfer objects.
 */
class CardTokenDataMapper
{
    /**
     * @var CardTokenInterfaceFactory
     */
    private CardTokenInterfaceFactory $entityDtoFactory;

    /**
     * @param CardTokenInterfaceFactory $entityDtoFactory
     */
    public function __construct(
        CardTokenInterfaceFactory $entityDtoFactory
    )
    {
        $this->entityDtoFactory = $entityDtoFactory;
    }

    /**
     * Map magento models to DTO array.
     *
     * @param AbstractCollection $collection
     *
     * @return array|CardTokenInterface[]
     */
    public function map(AbstractCollection $collection): array
    {
        $results = [];
        /** @var CardTokenModel $item */
        foreach ($collection->getItems() as $item) {
            /** @var CardTokenInterface|DataObject $entityDto */
            $entityDto = $this->entityDtoFactory->create();
            $entityDto->addData($item->getData());

            $results[] = $entityDto;
        }

        return $results;
    }
}
