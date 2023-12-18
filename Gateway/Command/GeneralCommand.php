<?php

namespace SysPay\Payment\Gateway\Command;

use SysPay\Payment\Gateway\Http\TransferFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Gateway\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Http\ClientInterface;

class GeneralCommand implements CommandInterface
{

    /**
     * @var BuilderInterface
     */
    private $requestBuilder;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var HandlerInterface
     */
    private $responseHandler;

    /**
     * @var TransferFactory
     */
    private $transferFactory;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @param BuilderInterface $requestBuilder
     * @param ValidatorInterface $validator
     * @param HandlerInterface $responseHandler
     * @param TransferFactory $transferFactory
     * @param ClientInterface $client
     */
    public function __construct(
        BuilderInterface   $requestBuilder,
        ValidatorInterface $validator,
        HandlerInterface   $responseHandler,
        TransferFactory    $transferFactory,
        ClientInterface    $client
    )
    {
        $this->requestBuilder = $requestBuilder;
        $this->validator = $validator;
        $this->responseHandler = $responseHandler;
        $this->transferFactory = $transferFactory;
        $this->client = $client;
    }

    /**
     * @param array $commandSubject
     * @return \Magento\Payment\Gateway\Command\ResultInterface|\Magento\Payment\Gateway\Validator\ResultInterface|null
     * @throws LocalizedException
     * @throws \Magento\Payment\Gateway\Http\ClientException
     * @throws \Magento\Payment\Gateway\Http\ConverterException
     */
    public function execute(array $commandSubject)
    {
        $request = $this->requestBuilder->build($commandSubject);
        $response = $this->client->placeRequest($this->transferFactory->create($request));
        $result = $this->validator->validate($response);
        if (!$result->isValid()) {
            throw new LocalizedException(__(implode("\n", $result->getFailsDescription())));
        }
        $this->responseHandler->handle($commandSubject, $response);

        return $result;
    }
}
