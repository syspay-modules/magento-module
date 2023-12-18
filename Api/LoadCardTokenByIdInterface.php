<?php

namespace SysPay\Payment\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use SysPay\Payment\Api\Data\CardTokenInterface;

/**
 * Load By ID CardToken Command.
 *
 * @api
 */
interface LoadCardTokenByIdInterface
{
    /**
     * Load CardToken by id.
     * @param int $cardTokenId
     * @return \SysPay\Payment\Api\Data\CardTokenInterface
     * @throws NoSuchEntityException
     */
    public function execute(int $cardTokenId): CardTokenInterface;
}
