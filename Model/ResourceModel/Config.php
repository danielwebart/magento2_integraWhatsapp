<?php
namespace Integra\Whatsapp\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Config extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('intwhats_config', 'entity_id');
    }
}
