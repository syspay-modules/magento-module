<?php

namespace SysPay\Payment\Controller\PlaceOrder;

use SysPay\Payment\Helper\Data;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Checkout\Model\Session;

class Handler implements HttpGetActionInterface
{
    private Session $checkoutSession;
    private RedirectFactory $redirectFactory;
    private Data $helper;

    public function __construct(
        Session         $checkoutSession,
        RedirectFactory $redirectFactory,
        Data            $helper
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->redirectFactory = $redirectFactory;
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->redirectFactory->create();
        $lastRealOrder = $this->checkoutSession->getLastRealOrder();
        if ($lastRealOrder) {
            $additionalInformation = $lastRealOrder->getPayment()->getAdditionalInformation();
            $redirectUrl = $additionalInformation['redirect_url'] ?? null;
            if ($redirectUrl) {
                $resultRedirect->setUrl($redirectUrl);
            } else {
                $resultRedirect->setPath($this->helper->getPlacedOrderRedirectPath($lastRealOrder));
            }
        } else {
            $resultRedirect->setPath('noRoute');
        }
        return $resultRedirect;
    }
}
