<?php
declare(strict_types=1);

namespace Training\Feedback\Ui\DataProvider\RatingOption\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\Feedback\Model\ResourceModel\RatingOption\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class DataProvider extends AbstractDataProvider
{
    protected $collection;
    protected $loadedData;

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $data = $this->dataPersistor->get('training_feedback_rating_options');
        if (!empty($data)) {
            $rating = $this->collection->getNewEmptyItem();
            $rating->setData($data);
            $this->loadedData[$rating->getId()] = $rating->getData();
            $this->dataPersistor->clear('training_feedback_rating_options');
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $rating) {
            $this->loadedData[$rating->getId()] = $rating->getData();
        }

        return $this->loadedData ?? [];
    }
}
