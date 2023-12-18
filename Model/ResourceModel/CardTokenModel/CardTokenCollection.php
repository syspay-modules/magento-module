<?php

namespace SysPay\Payment\Model\ResourceModel\CardTokenModel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Model\CardTokenModel;
use SysPay\Payment\Model\ResourceModel\CardTokenResource;

class CardTokenCollection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'syspay_customer_card_token_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(CardTokenModel::class, CardTokenResource::class);
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray(CardTokenInterface::ID, CardTokenInterface::CARD_DISPLAY);
    }

    /**
     * @param int $customerId
     * @return CardTokenCollection
     */
    public function addCustomerIdFilter(int $customerId): CardTokenCollection
    {
        return $this->addFieldToFilter(CardTokenInterface::CUSTOMER_ID, $customerId);
    }

    public function addTokenFilter(string $syspayTokenId): CardTokenCollection
    {
        return $this->addFieldToFilter(CardTokenInterface::TOKEN, $syspayTokenId);
    }
}
