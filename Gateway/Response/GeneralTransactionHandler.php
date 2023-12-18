<?php

namespace SysPay\Payment\Gateway\Response;

use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use SysPay\Payment\Gateway\Helper\SubjectReader;
use SysPay\Payment\Gateway\Response\Data\ResolverFactory as ResponseResolverFactory;
use SysPay\Payment\Gateway\Response\Data\Resolver as ResponseResolver;

class GeneralTransactionHandler implements HandlerInterface
{
    /**
     * @var SubjectReader
     */
    protected $subjectReader;

    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var array
     */
    protected array $transactionAdditionalInformationMapping = [];

    /**
     * @var string
     */
    protected $transactionType;

    /**
     * @var ResponseResolverFactory
     */
    protected $responseFactory;

    /**
     * @var ResponseResolverFactory
     */
    protected $responseResolverFactory;

    /**
     * @param SubjectReader $subjectReader
     * @param ManagerInterface $eventManager
     * @param ResponseResolverFactory $responseResolverFactory
     * @param string $transactionType
     * @param array $transactionAdditionalInformationMapping
     */
    public function __construct(
        SubjectReader           $subjectReader,
        ManagerInterface        $eventManager,
        ResponseResolverFactory $responseResolverFactory,
        string                  $transactionType,
        array                   $transactionAdditionalInformationMapping = []
    )
    {
        $this->responseResolverFactory = $responseResolverFactory;
        $this->subjectReader = $subjectReader;
        $this->eventManager = $eventManager;
        $this->transactionType = $transactionType;
        $this->transactionAdditionalInformationMapping = $transactionAdditionalInformationMapping;
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     * @return void
     */
    public function handle(array $handlingSubject, array $response)
    {
        $responseResolver = $this->responseResolverFactory->create(['response' => $response]);
        $orderDo = $this->subjectReader->readOrderDataAdapter($handlingSubject);
        $paymentDo = $this->subjectReader->readPayment($handlingSubject);

        $this->eventManager->dispatch(
            'syspay_payment_gateway_' . $this->transactionType . '_response_handle_before',
            [
                'response_resolver' => $responseResolver,
                'order_do' => $orderDo,
                'payment_do' => $paymentDo,
            ]
        );

        if (!empty($this->transactionAdditionalInformationMapping) && $responseResolver instanceof ResponseResolver) {
            /** @var OrderPaymentInterface $payment */
            $payment = $this->subjectReader->readPayment($handlingSubject)->getPayment();
            $transactionAdditionalData = [];
            foreach ($this->transactionAdditionalInformationMapping as $responseClassName => $props) {
                $responseDataClass = $responseResolver->getData($responseClassName);
                if ($responseDataClass instanceof DataObject) {
                    foreach ($props as $prop) {
                        if ($responseDataClass->hasData($prop)) {
                            $transactionAdditionalData[$prop] = $responseDataClass->getData($prop);
                        }
                    }
                }
            }

            $payment->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $transactionAdditionalData);
        }

        $this->handleTransaction($handlingSubject, $responseResolver);

        $this->eventManager->dispatch(
            'syspay_payment_gateway_' . $this->transactionType . '_response_handle_after',
            [
                'response_resolver' => $responseResolver,
                'order_do' => $this->subjectReader->readOrderDataAdapter($handlingSubject),
                'payment_do' => $this->subjectReader->readPayment($handlingSubject),
            ]
        );

    }

    /**
     * @param array $handlingSubject
     * @param ResponseResolver $response
     * @return void
     */
    protected function handleTransaction(array $handlingSubject, ResponseResolver $response): void
    {
    }

    /**
     * @param string $responseClassName
     * @param string $property
     * @return void
     */
    public function addTransactionAdditionalInformationMapping(string $responseClassName, string $property): void
    {
        if (!isset($this->transactionAdditionalInformationMapping[$responseClassName])) {
            $this->transactionAdditionalInformationMapping[$responseClassName] = [];
        }
        $this->transactionAdditionalInformationMapping[$responseClassName][$property] = $property;
    }

    /**
     * @param string $transactionType
     * @return void
     */
    public function setTransactionType(string $transactionType): void
    {
        $this->transactionType = $transactionType;
    }
}
