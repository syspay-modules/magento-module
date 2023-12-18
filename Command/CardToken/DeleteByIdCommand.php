<?php

namespace SysPay\Payment\Command\CardToken;

use Exception;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use SysPay\Payment\Api\Data\CardTokenInterface;
use SysPay\Payment\Api\DeleteCardTokenByIdInterface;
use SysPay\Payment\Model\CardTokenModel;
use SysPay\Payment\Model\CardTokenModelFactory;
use SysPay\Payment\Model\ResourceModel\CardTokenResource;

/**
 * Delete CardToken by id Command.
 */
class DeleteByIdCommand implements DeleteCardTokenByIdInterface
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
    public function execute(int $entityId): void
    {
        try {
            /** @var CardTokenModel $model */
            $model = $this->modelFactory->create();
            $this->resource->load($model, $entityId, CardTokenInterface::ID);

            if (!$model->getData(CardTokenInterface::ID)) {
                throw new NoSuchEntityException(
                    __('Could not find CardToken with id: `%id`',
                        [
                            'id' => $entityId
                        ]
                    )
                );
            }

            $this->resource->delete($model);
        } catch (Exception $exception) {
            $this->logger->error(
                __('Could not delete CardToken. Original message: {message}'),
                [
                    'message' => $exception->getMessage(),
                    'exception' => $exception
                ]
            );
            throw new CouldNotDeleteException(__('Could not delete CardToken.'));
        }
    }
}
