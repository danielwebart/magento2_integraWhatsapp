<?php
namespace Integra\Whatsapp\Controller\Adminhtml\Config;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Integra\Whatsapp\Model\ConfigFactory;

class Delete extends Action
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
        $id = $this->getRequest()->getParam('entity_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->configFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the configuration.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['entity_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a configuration to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
