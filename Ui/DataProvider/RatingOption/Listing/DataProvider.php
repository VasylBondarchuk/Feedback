<?php
declare(strict_types=1);

namespace Training\Feedback\Ui\DataProvider\RatingOption\Listing;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\Feedback\Model\ResourceModel\RatingOption\CollectionFactory;

class DataProvider extends AbstractDataProvider
{
    protected $collection;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData() : array
    {
        $items = $this->collection->getItems();
        $data = [];

        foreach ($items as $item) {
            $itemData = $item->getData();

            // Convert is_active from 0/1 to No/Yes
            if (isset($itemData['is_active'])) {
                $itemData['is_active'] = $itemData['is_active'] ? __('Yes') : __('No');
            }

            $data[] = $itemData;
        }

        return [
            'totalRecords' => $this->collection->getSize(),
            'items' => $data,
        ];
    }
}
