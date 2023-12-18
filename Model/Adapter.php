<?php

namespace SysPay\Payment\Model;

use SysPay\Payment\Gateway\Config\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Adapter
{
    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Json
     */
    private $json;

    /**
     * @param Curl $curl
     * @param Config $config
     * @param Json $json
     */
    public function __construct(
        Curl   $curl,
        Config $config,
        Json   $json
    )
    {
        $this->curl = $curl;
        $this->config = $config;
        $this->json = $json;
    }

    /**
     * @param TransferInterface $transfer
     * @return array
     * @throws LocalizedException
     */
    public function doRequest(TransferInterface $transfer): array
    {
        foreach ($transfer->getHeaders() as $name => $value) {
            $this->curl->addHeader($name, $value);
        }
        $target = $this->buildUrl($transfer->getUri());
        $body = $transfer->getBody();
        if (is_array($body)) {
            $body = $this->json->serialize($body);
        }
        $this->curl->post($target, $body);

        if (!$this->curl->getBody() && $this->curl->getStatus() != 200) {
            throw new LocalizedException(__('Empty response from SysPay'));
        }

        return $this->json->unserialize($this->curl->getBody());
    }

    /**
     * @param string $uri
     * @return string
     */
    public function buildUrl(string $uri): string
    {
        return $this->config->getApiUrl() . $uri;
    }
}
