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
            $url = $this->getTestUrl($configId);
            $confirmText = (string)__('Enviar mensagem de teste usando esta configuração?');
            $data = [
                'label' => __('Enviar mensagem teste'),
                'class' => 'action-secondary',
                'on_click' => "(() => {"
                    . "const phoneEl = document.querySelector('input[name=\"test_phone\"], input[name=\"data[test_phone]\"], input[name$=\"[test_phone]\"]');"
                    . "const msgEl = document.querySelector('textarea[name=\"test_message\"], textarea[name=\"data[test_message]\"], textarea[name$=\"[test_message]\"]');"
                    . "const phone = phoneEl ? phoneEl.value : '';"
                    . "const msg = msgEl ? msgEl.value : '';"
                    . "let url = '" . addslashes($url) . "';"
                    . "url += (url.indexOf('?') !== -1 ? '&' : '?') + 'test_phone=' + encodeURIComponent(phone) + '&test_message=' + encodeURIComponent(msg);"
                    . "confirmSetLocation('" . addslashes($confirmText) . "', url);"
                . "})()",
                'sort_order' => 80,
            ];
        }
        return $data;
    }

    private function getTestUrl($configId)
    {
        return $this->getUrl('*/*/testMessage', ['entity_id' => $configId, 'id' => $configId]);
    }
}
