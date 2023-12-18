<?php

namespace SysPay\Payment\Model\Method;

use SysPay\Payment\Model\Adminhtml\Source\PaymentFlow;
use SysPay\Payment\Model\ResourceModel\CardTokenModel\CardTokenCollectionFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Backend\Model\Session\Quote as BackendSessionQuote;
use Magento\Payment\Model\Method\Adapter;

class Facade extends Adapter
{
    /**
     * @return bool
     */
    public function canUseInternal()
    {
        /** @var BackendSessionQuote $backendSessionQuote */
        $backendSessionQuote = ObjectManager::getInstance()->get(BackendSessionQuote::class);
        $cardTokenCollectionFactory = ObjectManager::getInstance()->create(CardTokenCollectionFactory::class);
        $isServerToServerFlow = $this->getConfigData('payment_flow') === PaymentFlow::SERVER_TO_SERVER;

        if (!$isServerToServerFlow) {
            return false;
        }
        $customerId = $backendSessionQuote->getCustomerId();
        if (!$customerId) {
            return false;
        }
        $tokensExist = (bool)$cardTokenCollectionFactory
            ->create()
            ->addCustomerIdFilter($customerId)
            ->getSize();
        return parent::canUseInternal() && $tokensExist;
    }
}
