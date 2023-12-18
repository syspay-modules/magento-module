<?php

namespace SysPay\Payment\Http\Ems\Request;

use Magento\Framework\App\ObjectManager;

class HandlerFactory
{
    /**
     * @var array
     */
    private array $handlers;

    /**
     * @param array $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * @param string $type
     * @return AbstractHandler
     * @throws \InvalidArgumentException
     */
    public function create(string $type): AbstractHandler
    {
        if (!isset($this->handlers[$type])) {
            throw new \InvalidArgumentException(sprintf('Handler for type "%s" does not exist.', $type));
        }
        $object = ObjectManager::getInstance()->create($this->handlers[$type]);
        if ($object instanceof AbstractHandler) {
            return ObjectManager::getInstance()->create($this->handlers[$type]);
        } else {
            throw new \InvalidArgumentException(sprintf('Handler for type "%s" does not exist.', $type));
        }
    }
}
