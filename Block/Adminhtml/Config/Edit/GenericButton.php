<?php
namespace Integra\Whatsapp\Block\Adminhtml\Config\Edit;

use Magento\Backend\Block\Widget\Context;
use Integra\Whatsapp\Model\ConfigFactory;

class GenericButton
{
    protected $context;
    protected $configFactory;

    public function __construct(
        Context $context,
        ConfigFactory $configFactory
    ) {
        $this->context = $context;
        $this->configFactory = $configFactory;
    }

    public function getConfigId()
    {
        try {
            return $this->context->getRequest()->getParam('entity_id');
        } catch (\Exception $e) {
        }
        return null;
    }

    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
