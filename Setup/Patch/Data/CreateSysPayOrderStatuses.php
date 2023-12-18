<?php

namespace SysPay\Payment\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use SysPay\Payment\Model\Order\Source\Status as SysPayOrderStatusSource;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;

class CreateSysPayOrderStatuses implements DataPatchInterface, PatchRevertableInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private StatusResource $statusResource;
    private SysPayOrderStatusSource $sysPayOrderStatusSource;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StatusResource           $statusResource,
        SysPayOrderStatusSource  $sysPayOrderStatusSource
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->statusResource = $statusResource;
        $this->sysPayOrderStatusSource = $sysPayOrderStatusSource;
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $data = [];
        foreach ($this->sysPayOrderStatusSource->getOptionArray() as $code => $label) {
            $data[] = ['status' => $code, 'label' => $label];
        }

        $this->statusResource->getConnection()->insertArray(
            $this->statusResource->getMainTable(),
            ['status', 'label'],
            $data
        );

        $this->moduleDataSetup->endSetup();
    }


    public function revert()
    {
        $this->moduleDataSetup->startSetup();
        foreach ($this->sysPayOrderStatusSource->getOptionArray() as $code) {
            $this->statusResource->getConnection()->delete(
                $this->statusResource->getMainTable(),
                ['status = ?' => $code]
            );
        }
        $this->moduleDataSetup->endSetup();
    }
}
