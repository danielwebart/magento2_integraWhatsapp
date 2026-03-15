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
        $postData = (array)$this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($postData) {
            $data = [];

            if (isset($postData['data']['config']) && is_array($postData['data']['config'])) {
                $data = array_replace($data, $postData['data']['config']);
            }
            if (isset($postData['data']) && is_array($postData['data'])) {
                $data = array_replace($data, $postData['data']);
            }
            if (isset($postData['config']) && is_array($postData['config'])) {
                $data = array_replace($data, $postData['config']);
            }

            $allowedKeys = [
                'name',
                'phone_number_id',
                'business_account_id',
                'access_token',
                'api_provider',
                'facebook_api_version',
                'zapi_instance_id',
                'zapi_token',
                'zapi_client_token',
                'test_phone',
                'test_message',
                'is_active',
            ];

            $filteredData = [];
            foreach ($allowedKeys as $key) {
                if (array_key_exists($key, $data) && !is_array($data[$key])) {
                    $filteredData[$key] = $data[$key];
                }
            }

            if (array_key_exists('is_active', $filteredData)) {
                $filteredData['is_active'] = (int)filter_var($filteredData['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? (int)$filteredData['is_active'];
            }

            $referer = (string)$this->getRequest()->getServer('HTTP_REFERER');
            $refererId = 0;
            if ($referer) {
                if (preg_match('~/entity_id/(\d+)/~', $referer, $m)) {
                    $refererId = (int)$m[1];
                } elseif (preg_match('~/id/(\d+)/~', $referer, $m)) {
                    $refererId = (int)$m[1];
                }
            }

            $id = (int)(
                $data['entity_id']
                ?? $data['id']
                ?? $postData['entity_id']
                ?? $postData['id']
                ?? $this->getRequest()->getParam('entity_id')
                ?? $this->getRequest()->getParam('id')
                ?? $refererId
            );

            $apiProvider = $filteredData['api_provider'] ?? 'facebook';
            if ($apiProvider === 'facebook') {
                if (empty($filteredData['phone_number_id']) || empty($filteredData['access_token'])) {
                    $this->messageManager->addErrorMessage(__('For Facebook, fill Phone Number ID and Access Token.'));
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id ?: null]);
                }
            } elseif ($apiProvider === 'zapi') {
                if (empty($filteredData['zapi_instance_id']) || empty($filteredData['zapi_token'])) {
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

            if ($id) {
                $model->addData($filteredData);
            } else {
                $model->setData($filteredData);
            }

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the configuration.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $model->getId(), 'id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id, 'id' => $id]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
