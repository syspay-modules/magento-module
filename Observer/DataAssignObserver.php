<?php

namespace SysPay\Payment\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

class DataAssignObserver extends AbstractDataAssignObserver
{
    public const TMP_PAYMENT_TOKEN = 'tmp_payment_token';
    public const SAVE_CARD = 'save_card';
    public const SAVED_CARD_TOKEN_ID = 'saved_card_token_id';
    public const CC_CID = 'cc_cid';

    /**
     * @var array
     */
    protected array $additionalInformationList = [];

    /**
     * @param array $additionalInformationList
     */
    public function __construct(array $additionalInformationList = [])
    {
        $this->additionalInformationList = $additionalInformationList;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);
        $paymentInfo->unsAdditionalInformation();
        foreach ($this->additionalInformationList as $additionalInformationKey) {
            if (isset($additionalData[$additionalInformationKey])) {
                $paymentInfo->setAdditionalInformation(
                    $additionalInformationKey,
                    $additionalData[$additionalInformationKey]
                );
            }
        }
    }
}
