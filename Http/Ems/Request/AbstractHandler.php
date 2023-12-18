<?php

namespace SysPay\Payment\Http\Ems\Request;

use SysPay\Payment\Gateway\Response\Data\Resolver as DataResolver;
use Magento\Framework\Event\ManagerInterface;

abstract class AbstractHandler
{
    protected string $type;
    private ManagerInterface $eventManager;

    public function __construct(string $type, ManagerInterface $eventManager)
    {
        $this->type = $type;
        $this->eventManager = $eventManager;
    }

    abstract protected function resolve(DataResolver $requestDO): array;

    public function handle(DataResolver $requestData): array
    {
        $this->eventManager->dispatch(
            'syspay_payment_http_ems_request_handler_' . $this->getType() . '_before', [
            'request_data' => $requestData
        ]);

        $result = $this->resolve($requestData);

        if (!isset($result['request_data'])) {
            $result['request_data'] = $requestData;
        }

        $this->eventManager->dispatch(
            'syspay_payment_http_ems_request_handler_' . $this->getType() . '_after', $result);

        return $result;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
