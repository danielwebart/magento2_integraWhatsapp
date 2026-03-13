<?php
namespace Integra\Whatsapp\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Integra\Whatsapp\Model\ConfigFactory;

class Save extends Action
{
    const ADMIN_RESOURCE = 'Integra_Whatsapp::config';

    protected $configFactory;

    public function __construct(
        Context $context,
        ConfigFactory $configFactory
    ) {
        parent::__construct($context);
        $this->configFactory = $configFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (isset($data['data']) && is_array($data['data'])) {
                $data = $data['data'];
            }
            if (isset($data['config']) && is_array($data['config'])) {
                $data = $data['config'];
            }
            $id = (int)($data['entity_id'] ?? $this->getRequest()->getParam('entity_id'));
            unset($data['entity_id']);

            $apiProvider = $data['api_provider'] ?? 'facebook';
            if ($apiProvider === 'facebook') {
                if (empty($data['phone_number_id']) || empty($data['access_token'])) {
                    $this->messageManager->addErrorMessage(__('For Facebook, fill Phone Number ID and Access Token.'));
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id ?: null]);
                }
            } elseif ($apiProvider === 'zapi') {
                if (empty($data['zapi_instance_id']) || empty($data['zapi_token'])) {
                    $this->messageManager->addErrorMessage(__('For Z-API, fill Instance ID and Token.'));
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id ?: null]);
                }
            }

            $model = $this->configFactory->create();
            if ($id) {
                $model->load($id);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This configuration no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the configuration.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
