<?php

namespace SysPay\Payment\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use SysPay\Payment\Api\Data\CardTokenInterface;

class CardTokenResource extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'syspay_customer_card_token_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('syspay_customer_card_token', CardTokenInterface::ID);
        $this->_useIsObjectNew = true;
    }
}
