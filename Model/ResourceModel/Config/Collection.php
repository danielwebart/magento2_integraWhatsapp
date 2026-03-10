<?php
namespace Integra\Whatsapp\Model\ResourceModel\Config;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Integra\Whatsapp\Model\Config as Model;
use Integra\Whatsapp\Model\ResourceModel\Config as ResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
