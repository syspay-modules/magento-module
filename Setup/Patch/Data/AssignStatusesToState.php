<?php

namespace SysPay\Payment\Setup\Patch\Data;

use SysPay\Payment\Model\Order\Source\Status as SysPayOrderStatusSource;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use SysPay\Payment\Mapper\OrderStatusMapper;

class AssignStatusesToState implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private SysPayOrderStatusSource $sysPayOrderStatusSource;
    private OrderStatusMapper $orderStatusMapper;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        SysPayOrderStatusSource  $sysPayOrderStatusSource,
        OrderStatusMapper        $orderStatusMapper

    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->orderStatusMapper = $orderStatusMapper;
        $this->sysPayOrderStatusSource = $sysPayOrderStatusSource;
    }

    public static function getDependencies()
    {
        return [CreateSysPayOrderStatuses::class];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $data = [];
        foreach (array_keys($this->sysPayOrderStatusSource->getOptionArray()) as $status) {
            $data[] = [
                'status' => $status,
                'state' => $this->orderStatusMapper->getOrderStateByOrderStatus($status),
                'is_default' => '0',
                'visible_on_front' => '1'
            ];
        }

        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getConnection()->getTableName('sales_order_status_state'),
            ['status', 'state', 'is_default', 'visible_on_front'],
            $data
        );

        $this->moduleDataSetup->endSetup();
    }
}
