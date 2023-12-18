<?php

namespace SysPay\Payment\Model\Customer;

use SysPay\Payment\Api\Customer\CardTokenManagementInterface;
use SysPay\Payment\Api\CardTokenRepositoryInterface;
use SysPay\Payment\Api\Data\CardTokenSearchResultsInterface;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Model\CardTokenModel;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Exception\LocalizedException;

class CardTokenManagement implements CardTokenManagementInterface
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var CardTokenRepositoryInterface
     */
    private $cardTokenRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var
     */
    private $cardTokenModel;

    /**
     * @param Session $customerSession
     * @param CardTokenRepositoryInterface $cardTokenRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param CardTokenModel $cardTokenModel
     */
    public function __construct(
        Session                      $customerSession,
        CardTokenRepositoryInterface $cardTokenRepository,
        SearchCriteriaBuilder        $searchCriteriaBuilder,
        FilterBuilder                $filterBuilder,
        CardTokenModel               $cardTokenModel

    )
    {
        $this->customerSession = $customerSession;
        $this->cardTokenRepository = $cardTokenRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->cardTokenModel = $cardTokenModel;
    }

    /**
     * @param int $entityId
     * @return void
     * @throws Exception
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteTokenById(int $entityId): void
    {
        if (!$this->isAllowed()) {
            throw new LocalizedException(__('You must be logged in to delete a card token'));
        }
        try {
            $token = $this->cardTokenRepository->getById($entityId);
            if ($token->getCustomerId() != $this->customerSession->getCustomerId()) {
                throw new LocalizedException(__('You are not allowed to delete this card token'));
            }
            $this->cardTokenRepository->delete($token);
        } catch (NoSuchEntityException $e) {
            throw new LocalizedException(__('Card token not found'));
        }
    }

    /**
     * @return CardTokenSearchResultsInterface
     * @throws LocalizedException
     */
    public function getCardTokenList(): CardTokenSearchResultsInterface
    {
        if (!$this->isAllowed()) {
            throw new LocalizedException(__('You must be logged in to get a card token list'));
        }

        $customerId = $this->customerSession->getCustomerId();

        $filter = $this->filterBuilder
            ->setField(CardTokenInterface::CUSTOMER_ID)
            ->setValue($customerId)
            ->setConditionType('eq')
            ->create();
        $this->searchCriteriaBuilder->addFilters([$filter]);
        return $this->cardTokenRepository->getList($this->searchCriteriaBuilder->create());
    }

    /**
     * @param int $entityId
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     */
    public function isTokenAvailable(int $entityId): bool
    {
        if (!$this->isAllowed()) {
            return false;
        }

        try {
            $tokenObject = $this->cardTokenRepository->getById($entityId);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        $customerId = $this->customerSession->getCustomerId();

        if ($tokenObject->getCustomerId() != $customerId) {
            return false;
        }

        $tokenObject = $this->cardTokenModel->setData($tokenObject->getData())->apiRetrieveDataModel();


        $this->cardTokenRepository->save($tokenObject);

        if ($tokenObject->getMandateStatus() != CardTokenModel::SYSPAY_TOKEN_STATUS_ACTIVE) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isAllowed(): bool
    {
        return $this->customerSession->isLoggedIn();
    }
}
