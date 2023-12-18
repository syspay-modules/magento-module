<?php

namespace SysPay\Payment\Controller\Ems;

use SysPay\Payment\Exception\SysPayEmsCanNotUpdatePaymentException;
use SysPay\Payment\Gateway\Response\Data\ResolverFactory as DataResolverFactory;
use SysPay\Payment\Http\Ems\Request\HandlerFactory as RequestHandlerFactory;
use SysPay\Payment\Gateway\Config\Config;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Monolog\Logger;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Handler implements HttpPostActionInterface, CsrfAwareActionInterface
{
    protected Logger $logger;
    protected ResultFactory $resultFactory;
    protected RequestInterface $request;
    protected DataResolverFactory $dataResolverFactory;
    protected RequestHandlerFactory $requestHandlerFactory;
    protected Json $json;
    protected Config $config;

    public function __construct(
        Logger                $logger,
        ResultFactory         $resultFactory,
        RequestInterface      $request,
        DataResolverFactory   $dataResolverFactory,
        RequestHandlerFactory $requestHandlerFactory,
        Json                  $json,
        Config                $config
    )
    {

        $this->logger = $logger;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->dataResolverFactory = $dataResolverFactory;
        $this->requestHandlerFactory = $requestHandlerFactory;
        $this->json = $json;
        $this->config = $config;
    }


    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            if ($this->config->isDebugEnable()) {
                $this->logger->info('Request', [
                    'body' => $this->getRequestData(),
                    'merchant' => $this->getRequestMerchant(),
                    'event_id' => $this->getRequestEventId(),
                    'event_date' => $this->getRequestEventDate(),
                    'checksum' => $this->getRequestCheckSum()
                ]);
            }
            $data = $this->json->unserialize($this->getRequestData());
            $requestDO = $this->dataResolverFactory->create(['response' => $data]);

//            if (!$requestDO->getReference()) {
//                throw new LocalizedException(__('Invalid request. Reference not found'));
//            }

            $requestHandler = $this->requestHandlerFactory->create($requestDO->getClass());
            $requestHandler->handle($requestDO);
            $resultJson->setData(['success' => true]);
           // $this->logger->info(__('Object updated successfully'));
        } catch (SysPayEmsCanNotUpdatePaymentException $e) {
            $resultJson->setData([]);
        } catch (\Exception $e) {
            $resultJson->setData(['error' => $e->getMessage()]);
            $this->logger->error($e->getMessage());
        }
        return $resultJson;
    }

    /**
     * @param string|null $requestData
     * @return string
     * @throws LocalizedException
     */
    protected function isAllowed(?string $requestData): string
    {
        $merchant = $this->config->getMerchantId();
        if (!$requestData) {
            throw new LocalizedException(__('Request data not found'));
        }
        if ($this->getRequestMerchant() !== $merchant) {
            throw new LocalizedException(__('Merchant does not match'));
        }
        $private = $this->config->getPrivateKey();
        if (!$merchant || !$private) {
            throw new LocalizedException(__('Merchant or private key not set'));
        }
        $checkHash = sha1($requestData . $private);
        $checkSum = $this->getRequestCheckSum();
        if ($checkSum !== $checkHash) {
            throw new LocalizedException(__('Hashes do not match'));
        }
        return $requestData;
    }

    /**
     * @return mixed
     */
    protected function getRequestCheckSum(): ?string
    {
        return $this->request->get('HTTP_X_CHECKSUM');
    }

    /**
     * @return mixed
     */
    protected function getRequestMerchant(): ?string
    {
        return $this->request->get('HTTP_X_MERCHANT');
    }

    /**
     * @return mixed
     */
    protected function getRequestEventId(): ?string
    {
        return $this->request->get('HTTP_X_EVENT_ID');
    }

    /**
     * @return mixed
     */
    protected function getRequestEventDate(): ?string
    {
        return $this->request->get('HTTP_X_EVENT_DATE');
    }

    /**
     * @return string|null
     */
    protected function getRequestData(): ?string
    {
        return $this->request->getContent();
    }

    /**
     * @param RequestInterface $request
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        try {
            $this->isAllowed($this->getRequestData());
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param RequestInterface $request
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('noRoute');
        return new InvalidRequestException(
            $resultRedirect,
            [new Phrase('Action is not allowed.')]
        );
    }
}
