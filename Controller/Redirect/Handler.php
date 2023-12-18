<?php

namespace SysPay\Payment\Controller\Redirect;

use SysPay\Payment\Exception\SysPayEmsCanNotUpdatePaymentException;
use SysPay\Payment\Gateway\Config\Config;
use SysPay\Payment\Gateway\Response\Data\ResolverFactory as DataResolverFactory;
use SysPay\Payment\Http\Ems\Request\HandlerFactory as RequestHandlerFactory;
use SysPay\Payment\Controller\Ems\Handler as EmsHandler;
use SysPay\Payment\Helper\Data as Helper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Payment\Model\Method\Logger as PaymentLogger;
use Monolog\Logger;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\Controller\Result\Redirect;

class Handler extends EmsHandler implements HttpGetActionInterface, CsrfAwareActionInterface
{
    protected const EXPECTED_REQUEST_DATA_CLASS = 'payment';

    protected MessageManagerInterface $messageManager;
    protected PaymentLogger $paymentLogger;

    protected Helper $helper;

    public function __construct(
        Logger                  $logger,
        ResultFactory           $resultFactory,
        RequestInterface        $request,
        DataResolverFactory     $dataResolverFactory,
        RequestHandlerFactory   $requestHandlerFactory,
        Json                    $json,
        Config                  $config,
        PaymentLogger           $paymentLogger,
        MessageManagerInterface $messageManager,
        Helper                  $helper
    )
    {
        $this->messageManager = $messageManager;
        $this->paymentLogger = $paymentLogger;
        $this->helper = $helper;
        parent::__construct(
            $logger,
            $resultFactory,
            $request,
            $dataResolverFactory,
            $requestHandlerFactory,
            $json,
            $config
        );
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $redirectResult = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $log = [];
        try {
            $data = $this->json->unserialize(base64_decode($this->getRequestData()));
            $log = [
                'client' => static::class,
                'params' => [
                    'merchant' => $this->getRequestMerchant(),
                    'result' => $data,
                    'checksum' => $this->getRequestMerchant(),
                    'data' => $data
                ]
            ];
            $requestDataDO = $this->dataResolverFactory->create(['response' => $data]);
            if (!$requestDataDO->getPayment()) {
                throw new LocalizedException(__('Invalid request data class'));
            }
            $requestHandler = $this->requestHandlerFactory->create(static::EXPECTED_REQUEST_DATA_CLASS);
            $result = $requestHandler->handle($requestDataDO);
            /** @var Order $order */
            $order = $result['order'];
            $redirectResult->setPath($this->helper->getPlacedOrderRedirectPath($order));
        } catch (SysPayEmsCanNotUpdatePaymentException $e) {
            // case when payment is already updated
            $redirectResult->setPath($this->helper->getPlacedOrderRedirectPath($e->getPayment()->getOrder()));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $redirectResult->setPath('noRoute');
            $log['error'] = $e->getMessage();
            $this->logger->critical($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('An error occurred while processing the payment.'));
            $redirectResult->setPath('noRoute');
            $log['error'] = $e->getMessage();
            $this->logger->critical($e->getMessage());
        }

        $this->paymentLogger->debug($log);

        return $redirectResult;
    }

    /**
     * @return string|null
     */
    protected function getRequestCheckSum(): ?string
    {
        return $this->request->getParam('checksum');
    }

    /**
     * @return string|null
     */
    protected function getRequestMerchant(): ?string
    {
        return $this->request->getParam('merchant');
    }

    /**
     * @return string|null
     */
    protected function getRequestData(): ?string
    {
        return $this->request->getParam('result');
    }
}
