<?php

namespace SysPay\Payment\Http\Ems\Request\Handler;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SysPay\Payment\Api\CardTokenRepositoryInterface;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Gateway\Response\Data\Resolver as DataResolver;
use SysPay\Payment\Http\Ems\Request\AbstractHandler;
use SysPay\Payment\Model\CardTokenModel;

class Token extends AbstractHandler
{

    private CardTokenRepositoryInterface $cardTokenRepository;
    private CardTokenModel $cardTokenModel;

    public function __construct(
        string                       $type,
        ManagerInterface             $eventManager,
        CardTokenRepositoryInterface $cardTokenRepository,
        CardTokenModel               $cardTokenModel
    )
    {
        $this->cardTokenRepository = $cardTokenRepository;
        $this->cardTokenModel = $cardTokenModel;
        parent::__construct($type, $eventManager);
    }

    /**
     * @param DataResolver $requestDO
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    protected function resolve(DataResolver $requestDO): array
    {
        $requestTokenDO = $requestDO->getToken();

        if (!$requestTokenDO->getId()) {
            throw new LocalizedException(__('Token not found'));
        }
        $cardTokenDataModel = $this->getCardToken($requestTokenDO->getId());
        if (!$cardTokenDataModel) {
            return [];
        }

        $cardTokenDataModel = $this
            ->cardTokenModel
            ->setData($cardTokenDataModel->getData())
            ->apiRetrieveDataModel();


        $this->cardTokenRepository->save($cardTokenDataModel);

        return ['card_token' => $cardTokenDataModel];
    }

    protected function getCardToken($syspayTokenId): ?CardTokenInterface
    {
        try {
            return $this->cardTokenRepository->getByToken($syspayTokenId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
