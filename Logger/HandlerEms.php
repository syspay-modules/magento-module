<?php

namespace SysPay\Payment\Logger;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class HandlerEms extends Base
{
    /**
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * @var string
     */
    protected $fileName = '/var/log/syspay-ems.log';
}
