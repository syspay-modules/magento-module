<?php

namespace SysPay\Payment\Model\Adminhtml\Source;

class CcType extends \Magento\Payment\Model\Source\Cctype
{

    /**
     * @return string[]
     */
    public function getAllowedTypes(): array
    {
        return ['VI', 'MC', 'AE', 'DI', 'JCB', 'MI', 'DN'];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $allowed = $this->getAllowedTypes();
        $options = [];

        foreach ($this->_paymentConfig->getCcTypes() as $code => $name) {
            if (in_array($code, $allowed) || !count($allowed)) {
                $options[] = ['value' => $code, 'label' => $name];
            }
        }

        return $options;
    }
}
