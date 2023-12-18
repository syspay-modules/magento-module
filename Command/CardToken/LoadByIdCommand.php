<?php

namespace SysPay\Payment\Command\CardToken;

use Magento\Framework\Exception\NoSuchEntityException;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Api\LoadByIdInterface;
use SysPay\Payment\Api\LoadCardTokenByIdInterface;
use SysPay\Payment\Model\CardTokenModelFactory;
use SysPay\Payment\Model\ResourceModel\CardTokenResource;

class LoadByIdCommand implements LoadCardTokenByIdInterface
{

    private $modelFactory;
    private $resource;

    public function __construct(
        CardTokenModelFactory $modelFactory,
        CardTokenResource     $resource
    )
    {
        $this->modelFactory = $modelFactory;
        $this->resource = $resource;
    }

    /**
     * @param int $cardTokenId
     * @return CardTokenInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $cardTokenId): CardTokenInterface
    {
        $cardToken = $this->modelFactory->create();
        $this->resource->load($cardToken, $cardTokenId);
        if (!$cardToken->getId()) {
            throw new NoSuchEntityException(
                __(
                    'Customer card token with id "%1" does not exist.',
                    $cardTokenId
                )
            );

        }
        return $cardToken->getDataModel();
    }
}
