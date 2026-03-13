<?php
namespace Integra\Whatsapp\Model\Config;

use Integra\Whatsapp\Model\ResourceModel\Config\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    protected $collection;
    protected $dataPersistor;
    protected $loadedData;
    private $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $this->loadedData = [];

        $requestedId = (int)(
            $this->request->getParam($this->getRequestFieldName())
            ?: $this->request->getParam('entity_id')
            ?: $this->request->getParam('id')
        );
        if ($requestedId) {
            $this->collection->addFieldToFilter($this->getPrimaryFieldName(), $requestedId);
        }

        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $id = (int)$model->getId();
            $rowData = $model->getData();
            $entityData = $rowData;
            $entityData['config'] = $rowData;
            $entityData['data'] = [
                'config' => $rowData,
            ];
            $this->loadedData[$id] = $entityData;
        }
        $data = $this->dataPersistor->get('integra_whatsapp_config');
        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $id = (int)$model->getId();
            $rowData = $model->getData();
            $entityData = $rowData;
            $entityData['config'] = $rowData;
            $entityData['data'] = [
                'config' => $rowData,
            ];
            $this->loadedData[$id ?: 0] = $entityData;
            $this->dataPersistor->clear('integra_whatsapp_config');
        }
        return $this->loadedData;
    }
}
