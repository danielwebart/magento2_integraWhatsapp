<?php
namespace Integra\Whatsapp\Model;

use Magento\Framework\Model\AbstractModel;

class Config extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Integra\Whatsapp\Model\ResourceModel\Config::class);
    }
}
