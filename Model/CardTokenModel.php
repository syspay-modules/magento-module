<?php

namespace SysPay\Payment\Model;

use SysPay\Payment\Model\ResourceModel\CardTokenResource;
use SysPay\Payment\Api\Data\CardTokenInterfaceFactory;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Gateway\Command\GeneralCommand as SysPayMerchantTokenRetrieveCommand;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

class CardTokenModel extends AbstractModel
{
    const SYSPAY_TOKEN_STATUS_ACTIVE = 'ACTIVE';
    const SYSPAY_TOKEN_STATUS_FAILED = 'FAILED';
    const SYSPAY_TOKEN_STATUS_CANCELLED = 'CANCELLED';
    const SYSPAY_TOKEN_STATUS_ENDED = 'ENDED';
    const SYSPAY_TOKEN_STATUS_EXPIRED = 'EXPIRED';
    const SYSPAY_TOKEN_STATUS_TIMED_OUT = 'TIMED_OUT';
    const SYSPAY_TOKEN_STATUS_NOT_REQUESTED = 'NOT_REQUESTED';

    /**
     * @var string
     */
    protected $_eventPrefix = 'syspay_customer_card_token_model';

    /**
     * @var CardTokenInterfaceFactory
     */
    private $cardTokenDataFactory;

    private $merchantTokenRetrieveCommand;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param CardTokenInterfaceFactory $cardTokenDataFactory
     * @param SysPayMerchantTokenRetrieveCommand $merchantTokenRetrieveCommand
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context                            $context,
        Registry                           $registry,
        CardTokenInterfaceFactory          $cardTokenDataFactory,
        SysPayMerchantTokenRetrieveCommand $merchantTokenRetrieveCommand,
        AbstractResource                   $resource = null,
        AbstractDb                         $resourceCollection = null,
        array                              $data = []
    )
    {
        $this->cardTokenDataFactory = $cardTokenDataFactory;
        $this->merchantTokenRetrieveCommand = $merchantTokenRetrieveCommand;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(CardTokenResource::class);
    }

    /**
     * @return CardTokenInterface
     */
    public function getDataModel(): CardTokenInterface
    {
        $cardTokenData = $this->getData();

        $cardTokenDataObject = $this->cardTokenDataFactory->create();
        $cardTokenDataObject->setData($cardTokenData);

        return $cardTokenDataObject;
    }

    /**
     * Build data model from API request.
     *
     * @return CardTokenInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     */
    public function apiRetrieveDataModel(): CardTokenInterface
    {
        if (!$this->getToken() || !$this->getCustomerId()) {
            throw new LocalizedException(__('Token or customer id is empty'));
        }

        $cardTokenData = $this->getDataModel();
        $this->merchantTokenRetrieveCommand->execute(['token_object' => $cardTokenData]);
        return $cardTokenData;
    }
}
