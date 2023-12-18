<?php

namespace SysPay\Payment\Gateway\Helper;

use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Observer\DataAssignObserver;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Helper;
use Magento\Payment\Gateway\Data\OrderAdapterInterface;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;
use InvalidArgumentException;

class SubjectReader
{
    private State $appState;

    /**
     * @param State $appState
     */
    public function __construct(
        State $appState
    )
    {
        $this->appState = $appState;
    }


    /**
     * @param array $subject
     * @return OrderAdapterInterface
     */
    public function readOrderDataAdapter(array $subject): OrderAdapterInterface
    {
        $paymentDO = $this->readPayment($subject);
        $orderDA = $paymentDO->getOrder();
        if (!$orderDA) {
            throw new InvalidArgumentException('Order does not exist');
        }
        return $orderDA;
    }

    /**
     * @param array $subject
     * @return int|null
     */
    public function readCustomerId(array $subject): ?int
    {
        return $this->readOrderDataAdapter($subject)->getCustomerId();
    }


    /**
     * @param array $subject
     * @return string
     */
    public function readCurrencyCode(array $subject): string
    {
        return $this->readOrderDataAdapter($subject)->getCurrencyCode();
    }

    /**
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject): PaymentDataObjectInterface
    {
        return Helper\SubjectReader::readPayment($subject);
    }

    /**
     * @param array $subject
     * @return bool
     */
    public function readIsSaveCard(array $subject): bool
    {
        $paymentDO = $this->readPayment($subject);
        return (bool)$paymentDO->getPayment()
            ->getAdditionalInformation(DataAssignObserver::SAVE_CARD);
    }

    /**
     * @param array $subject
     * @return int
     */
    public function readSavedCardTokenId(array $subject): int
    {
        $paymentDO = $this->readPayment($subject);
        return (int)$paymentDO->getPayment()
            ->getAdditionalInformation(DataAssignObserver::SAVED_CARD_TOKEN_ID);
    }

    /**
     * This is the temporary payment token that is used to create the payment token
     *
     * @param array $subject
     * @return ?string
     */
    public function readTmpPaymentToken(array $subject): ?string
    {
        $paymentDO = $this->readPayment($subject);
        return $paymentDO
            ->getPayment()
            ->getAdditionalInformation(DataAssignObserver::TMP_PAYMENT_TOKEN);
    }

    /**
     * @param array $subject
     * @return mixed
     */
    public function readAmount(array $subject): mixed
    {
        return Helper\SubjectReader::readAmount($subject);
    }

    /**
     * @param array $subject
     * @return float
     */
    public function readAmountInCents(array $subject): float
    {
        $amount = $this->readAmount($subject);
        return round($amount * 100);
    }

    /**
     * @param array $subject
     * @return string
     */
    public function readIpAddress(array $subject): string
    {
        $ip = $this->readOrderDataAdapter($subject)->getRemoteIp();
        if (!$ip) {
            throw new InvalidArgumentException('IP address does not exist');
        }
        if (!filter_var($ip, FILTER_VALIDATE_IP)) {
            throw new InvalidArgumentException('IP address is not valid');
        }

        //payment provider does not accept localhost IP
        if ($ip === '127.0.0.1') {
            $ip = "80.90.62.101";
        }
        return $ip;
    }

    /**
     * @param array $subject
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function readCcCid(array $subject): ?string
    {
        $ccCid = $this->readPayment($subject)->getPayment()
            ->getAdditionalInformation(DataAssignObserver::CC_CID);
        if (!$ccCid) {
            if ($this->appState->getAreaCode() === Area::AREA_ADMINHTML) {
                return null;
            } else {
                throw new InvalidArgumentException('CVV should be provided.');
            }
        }
        return $ccCid;
    }

    public function readTokenDo(array $subject): ?CardTokenInterface
    {
        if (!isset($subject['token_object'])) {
            throw new InvalidArgumentException('Token object does not exist');
        }
        if (!$subject['token_object'] instanceof CardTokenInterface) {
            throw new InvalidArgumentException('Token object is not an instance of CardTokenInterface');
        }
        return $subject['token_object'];
    }

}
