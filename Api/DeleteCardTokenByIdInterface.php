<?php

namespace SysPay\Payment\Api;

use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Delete CardToken by id Command.
 *
 * @api
 */
interface DeleteCardTokenByIdInterface
{
    /**
     * Delete CardToken.
     * @param int $entityId
     * @return void
     * @throws CouldNotDeleteException
     */
    public function execute(int $entityId): void;
}
