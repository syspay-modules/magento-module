<?php

namespace SysPay\Payment\Api;

use Magento\Framework\Exception\NoSuchEntityException;
use SysPay\Payment\Api\Data\CardTokenInterface;

/**
 * Load by SysPay token
 *
 * @api
 */
interface LoadCardTokenByTokenInterface
{
    /**
     * Load by SysPay token
     *
     * @param string $token
     * @return CardTokenInterface
     * @throws NoSuchEntityException
     */
    public function execute(string $token): CardTokenInterface;
}
