<?php

namespace SysPay\Payment\Gateway\Http;

use SysPay\Payment\Gateway\Config\Config;
use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;

class TransferFactory implements TransferFactoryInterface
{
    protected const HEADER_CONTENT_TYPE = 'Content-Type';
    protected const HEADER_AUTH = 'X-Wsse';

    /**
     * @var TransferBuilder
     */
    private $transferBuilder;

    /**
     * @var string|null
     */
    private $uri;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param TransferBuilder $transferBuilder
     * @param Config $config
     * @param string|null $uri
     */
    public function __construct(
        TransferBuilder $transferBuilder,
        Config          $config,
        string          $uri = null,

    )
    {
        $this->uri = $uri;
        $this->transferBuilder = $transferBuilder;
        $this->config = $config;
    }

    /**
     * @param array $request
     * @return \Magento\Payment\Gateway\Http\Transfer|\Magento\Payment\Gateway\Http\TransferInterface
     */
    public function create(array $request)
    {
        return $this->transferBuilder
            ->setUri($this->getUri($request))
            ->setBody($this->getBody($request))
            ->setHeaders($this->getHeaders($request))
            ->build();
    }

    /**
     * @param array $request
     * @return array
     */
    protected function getBody(array $request): array
    {
        return $request['body'] ?? [];
    }

    /**
     * @param array $request
     * @return string
     */
    protected function getUri(array $request): string
    {
        $uri = empty($request['uri']) ? $this->uri : $request['uri'];
        if (!$uri) {
            throw new \InvalidArgumentException('URI is missing');
        }
        if (str_contains($uri, '%paymentId%')) {
            $body = $this->getBody($request);
            if (empty($body['payment_id'])) {
                throw new \InvalidArgumentException('Payment ID is missing');
            }
            $uri = str_replace('%paymentId%', (int)$body['payment_id'], $uri);
        }
        return $uri;
    }

    /**
     * @param array $subject
     * @return array
     */
    protected function getHeaders(array $subject): array
    {
        $headers = [
            self::HEADER_CONTENT_TYPE => 'application/json',
            self::HEADER_AUTH => $this->generateAuthHeader()
        ];

        if (!empty($subject['headers']) && is_array($subject['headers'])) {
            $headers = array_merge($headers, $subject['headers']);
        }
        return $headers;
    }

    /**
     * @return string
     */
    protected function generateAuthHeader(): string
    {
        $nonce = hash('md5', rand());
        $timestamp = time();
        $digest = base64_encode(
            sha1(
                $nonce . $timestamp . $this->config->getPrivateKey(),
                true
            )
        );
        $b64nonce = base64_encode($nonce);
        return sprintf('AuthToken MerchantAPILogin="%s", PasswordDigest="%s", Nonce="%s", Created="%d"',
            $this->config->getMerchantId(), $digest, $b64nonce, $timestamp);
    }
}
