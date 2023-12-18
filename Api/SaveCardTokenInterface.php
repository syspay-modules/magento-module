<?php

namespace SysPay\Payment\Api;

use Magento\Framework\Exception\CouldNotSaveException;
use SysPay\Payment\Api\Data\CardTokenInterface;

/**
 * Save CardToken Command.
 *
 * @api
 */
interface SaveCardTokenInterface
{
    /**
     * Save CardToken.
     * @param \SysPay\Payment\Api\Data\CardTokenInterface $cardToken
     * @return int
     * @throws CouldNotSaveException
     */
    public function execute(CardTokenInterface $cardToken): int;
}
