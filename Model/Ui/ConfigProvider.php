<?php

namespace SysPay\Payment\Model\Ui;

use SysPay\Payment\Gateway\Config\Config;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\Session as CustomerSession;

class ConfigProvider implements ConfigProviderInterface
{
    public const CODE = 'syspay';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        Config          $config,
        CustomerSession $customerSession,
        UrlInterface    $urlBuilder
    )
    {
        $this->config = $config;
        $this->customerSession = $customerSession;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return \array[][]
     */
    public function getConfig()
    {
        if (!$this->config->isActive()) {
            return [];
        }
        return [
            'payment' => [
                self::CODE => [
                    'isActive' => $this->config->isActive(),
                    'isSandbox' => $this->config->isSandboxMode(),
                    'clientSideTokenizationScript' => $this->config->getValue(Config::CLIENT_TOKENIZATION_SCRIPT),
                    'publicKey' => $this->config->getPublicKey(),
                    'isAbleToSaveCard' => $this->isAbleToSaveCard(),
                    'isSaveCardEnabled' => $this->isSaveCardEnabled(),
                    'saveCardUrl' => $this->getSaveCardUrl(),
                    'isHostedPageFlow' => $this->isHostedPageFlow()
                ]
            ]
        ];
    }


    /**
     * @return bool
     */
    protected function isAbleToSaveCard(): bool
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * @return string
     */
    protected function getSaveCardUrl(): string
    {
        return $this->urlBuilder->getUrl('syspay/card/save');
    }

    /**
     * @return bool
     */
    protected function isSaveCardEnabled(): bool
    {
        return $this->config->isSaveCardEnabled();
    }

    /**
     * @return bool
     */
    protected function isHostedPageFlow(): bool
    {
        return $this->config->isHostedPageFlow();
    }
}
