<?php

namespace SysPay\Payment\Command\CardToken;

use Exception;
use Magento\Framework\Exception\CouldNotSaveException;
use Psr\Log\LoggerInterface;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Api\SaveCardTokenInterface;
use SysPay\Payment\Model\CardTokenModel;
use SysPay\Payment\Model\CardTokenModelFactory;
use SysPay\Payment\Model\ResourceModel\CardTokenResource;

/**
 * Save CardToken Command.
 */
class SaveCommand implements SaveCardTokenInterface
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var CardTokenModelFactory
     */
    private CardTokenModelFactory $modelFactory;

    /**
     * @var CardTokenResource
     */
    private CardTokenResource $resource;

    /**
     * @param LoggerInterface $logger
     * @param CardTokenModelFactory $modelFactory
     * @param CardTokenResource $resource
     */
    public function __construct(
        LoggerInterface       $logger,
        CardTokenModelFactory $modelFactory,
        CardTokenResource     $resource
    )
    {
        $this->logger = $logger;
        $this->modelFactory = $modelFactory;
        $this->resource = $resource;
    }

    /**
     * @inheritDoc
     */
    public function execute(CardTokenInterface $cardToken): int
    {
        try {
            /** @var CardTokenModel $model */
            $model = $this->modelFactory->create();
            $model->addData($cardToken->getData());
            $model->setHasDataChanges(true);

            if (!$model->getData(CardTokenInterface::ID)) {
                $model->isObjectNew(true);
            }
            $this->resource->save($model);
        } catch (Exception $exception) {
            $this->logger->error(
                __('Could not save CardToken. Original message: {message}'),
                [
                    'message' => $exception->getMessage(),
                    'exception' => $exception
                ]
            );
            throw new CouldNotSaveException(__('Could not save CardToken.'));
        }

        return (int)$model->getData(CardTokenInterface::ID);
    }
}
