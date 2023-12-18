<?php

namespace SysPay\Payment\Block\Form;


use SysPay\Payment\Model\ResourceModel\CardTokenModel\CardTokenCollectionFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Model\Config;
use Magento\Backend\Model\Session\Quote as SessionQuote;

class Cc extends \Magento\Payment\Block\Form\Cc
{
    /**
     * @var CardTokenCollectionFactory
     */
    private $cardTokenCollectionFactory;

    /**
     * @var SessionQuote
     */
    private $sessionQuote;

    /**
     * @param CardTokenCollectionFactory $cardTokenCollectionFactory
     * @param Context $context
     * @param Config $paymentConfig
     * @param SessionQuote $sessionQuote
     * @param array $data
     */
    public function __construct(
        CardTokenCollectionFactory $cardTokenCollectionFactory,
        Context                    $context,
        Config                     $paymentConfig,
        SessionQuote               $sessionQuote,
        array                      $data = []
    )
    {
        $this->sessionQuote = $sessionQuote;
        $this->cardTokenCollectionFactory = $cardTokenCollectionFactory;
        $this->setTemplate('SysPay_Payment::form/cc.phtml');
        parent::__construct($context, $paymentConfig, $data);
    }

    /**
     * @return array
     */
    public function getSavedCardsTokens(): array
    {
        return $this->cardTokenCollectionFactory
            ->create()
            ->addCustomerIdFilter($this->getCustomerId())
            ->toOptionArray();
    }

    /**
     * @return false
     */
    public function hasVerification()
    {
        return false;
    }

    /**
     * @return int
     */
    protected function getCustomerId(): int
    {
        return $this->getSessionQuote()->getCustomerId();
    }

    /**
     * @return SessionQuote
     */
    protected function getSessionQuote(): SessionQuote
    {
        return $this->sessionQuote;
    }
}
