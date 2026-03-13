<?php
namespace Integra\Whatsapp\Block\Adminhtml\Config\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class TestMessageButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData()
    {
        $data = [];
        $configId = $this->getConfigId();
        if ($configId) {
            $data = [
                'label' => __('Enviar mensagem teste'),
                'class' => 'action-secondary',
                'on_click' => 'confirmSetLocation(\'' . __(
                    'Enviar mensagem de teste usando esta configuração?'
                ) . '\', \'' . $this->getTestUrl($configId) . '\')',
                'sort_order' => 80,
            ];
        }
        return $data;
    }

    private function getTestUrl($configId)
    {
        return $this->getUrl('*/*/testMessage', ['entity_id' => $configId]);
    }
}
