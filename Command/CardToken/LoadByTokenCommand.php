<?php

namespace SysPay\Payment\Command\CardToken;

use Magento\Framework\Exception\NoSuchEntityException;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Api\LoadCardTokenByTokenInterface;
use SysPay\Payment\Model\CardTokenModelFactory;
use SysPay\Payment\Model\ResourceModel\CardTokenResource;

class LoadByTokenCommand implements LoadCardTokenByTokenInterface
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
     * @param string $token
     * @return CardTokenInterface
     * @throws NoSuchEntityException
     */
    public function execute(string $token): CardTokenInterface
    {
        $cardToken = $this->modelFactory->create();
        $this->resource->load($cardToken, $token, CardTokenInterface::TOKEN);
        if (!$cardToken->getId()) {
            throw new NoSuchEntityException(
                __('Customer card token with token "%1" does not exist.', $token)
            );

        }
        return $cardToken->getDataModel();
    }
}
