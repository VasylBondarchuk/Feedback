<?php

namespace Training\Feedback\Ui\DataProvider\Form;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends AbstractDataProvider
{
    protected $collection;
    protected $dataPersistor;
    protected $loadedData;
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $data = $this->dataPersistor->get('training_feedback');
        if (!empty($data)) {
            $feedback = $this->collection->getNewEmptyItem();
            $feedback->setData($data);
            $this->loadedData[$feedback->getId()] = $feedback->getData();
            $this->dataPersistor->clear('training_feedback');
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var \Training\Feedback\Model\Feedback $feedback */
        foreach ($items as $feedback) {
            $this->loadedData[$feedback->getId()] = $feedback->getData();
        }
        return $this->loadedData;
    }
}
