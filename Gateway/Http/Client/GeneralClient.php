<?php

namespace SysPay\Payment\Gateway\Http\Client;

use SysPay\Payment\Model\Adapter;
use Monolog\Logger;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger as PaymentLogger;

class GeneralClient implements ClientInterface
{
    /**
     * @var Adapter
     */
    private $adapter;

    /**
     * @var PaymentLogger
     */
    private $paymentLogger;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Adapter $adapter
     * @param Logger $logger
     * @param PaymentLogger $paymentLogger
     */
    public function __construct(Adapter $adapter, Logger $logger, PaymentLogger $paymentLogger)
    {
        $this->adapter = $adapter;
        $this->logger = $logger;
        $this->paymentLogger = $paymentLogger;
    }

    /**
     * @param TransferInterface $transferObject
     * @return array
     * @throws ClientException
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $log = [
            'client' => static::class,
            //'headers' => $transferObject->getHeaders(),
            'url' => $this->adapter->buildUrl($transferObject->getUri()),
            'request' => $transferObject->getBody()
        ];
        $response = [];
        try {
            $response = $this->adapter->doRequest($transferObject);
        } catch (\Exception $e) {
            $message = __($e->getMessage() ?: 'Something went wrong');
            $this->logger->critical($message);
            throw new ClientException($message);
        } finally {
            $log['response'] = $response;
            $this->paymentLogger->debug($log);
        }

        return $response;
    }
}
