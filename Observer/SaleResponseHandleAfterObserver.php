<?php

namespace SysPay\Payment\Observer;

use SysPay\Payment\Api\CardTokenRepositoryInterface;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Model\CardTokenModel;
use SysPay\Payment\Gateway\Response\Data\Resolver;
use SysPay\Payment\Gateway\Config\Config as GatewayConfig;
use Monolog\Logger;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;


class SaleResponseHandleAfterObserver implements ObserverInterface
{
    /**
     * @var CardTokenRepositoryInterface
     */
    private $cardTokenRepository;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var GatewayConfig
     */
    private $gatewayConfig;

    /**
     * @var CardTokenModel
     */
    private $cardTokenModel;

    /**
     * @param CardTokenRepositoryInterface $cardTokenRepository
     * @param Logger $logger
     * @param GatewayConfig $gatewayConfig
     * @param CardTokenModel $cardTokenModel
     */
    public function __construct(
        CardTokenRepositoryInterface $cardTokenRepository,
        Logger                       $logger,
        GatewayConfig                $gatewayConfig,
        CardTokenModel               $cardTokenModel
    )
    {
        $this->cardTokenRepository = $cardTokenRepository;
        $this->logger = $logger;
        $this->gatewayConfig = $gatewayConfig;
        $this->cardTokenModel = $cardTokenModel;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Resolver $responseResolver */
        $responseResolver = $observer->getData('response_resolver');
        /** @var  OrderAdapterInterface $orderDo */
        $orderDo = $observer->getData('order_do');
        /** @var PaymentDataObjectInterface $paymentDo */
        $paymentDo = $observer->getData('payment_do');
        $customerId = $orderDo->getCustomerId();


        $isAbleToCreateToken = $this->isAbleToCreateToken($responseResolver, $orderDo);

        if ($isAbleToCreateToken) {
            try {
                $token = $this->buildToken($responseResolver, $customerId);
                $cardTokenId = $this->cardTokenRepository->save($token);
                $paymentDo
                    ->getPayment()
                    ->setAdditionalInformation(DataAssignObserver::SAVED_CARD_TOKEN_ID, $cardTokenId);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }

    /**
     * @param Resolver $responseResolver
     * @param $customerId
     * @return CardTokenInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     */
    protected function buildToken(Resolver $responseResolver, $customerId): ?CardTokenInterface
    {
        $tokenDO = $responseResolver->getToken();

        if (!$tokenDO || !$tokenDO->getId()) {
            return null;
        }

        $token = $tokenDO->getId();
        try {
            $token = $this->cardTokenRepository->getByToken($token);
            if ($token->getCustomerId() != $customerId) {
                throw new AlreadyExistsException(__("Token already exists for another customer."));
            }
            $this->cardTokenModel->setData($token->getData());
        } catch (NoSuchEntityException $e) {
            $this->cardTokenModel
                ->setData('token', $tokenDO->getId())
                ->setData('customer_id', $customerId);
        }

        return $this->cardTokenModel->apiRetrieveDataModel();

    }

    /**
     * @param Resolver $responseResolver
     * @param OrderAdapterInterface $orderDo
     * @return bool
     */
    protected function isAbleToCreateToken(Resolver $responseResolver, OrderAdapterInterface $orderDo): bool
    {
        $customerId = $orderDo->getCustomerId();

        $tokenDO = $responseResolver->getToken();
        $mandateDO = $responseResolver->getMandate();

        if ($this->gatewayConfig->isServerToServerFlow()) {
            return $tokenDO && $mandateDO && $customerId
                && $responseResolver->getClass() === Resolver::RESPONSE_CLASS_TOKEN;
        } elseif ($this->gatewayConfig->isHostedPageFlow() && $tokenDO && $mandateDO) {
            return $mandateDO->getStatus() == CardTokenModel::SYSPAY_TOKEN_STATUS_ACTIVE && $customerId;
        } else {
            return false;
        }
    }
}
