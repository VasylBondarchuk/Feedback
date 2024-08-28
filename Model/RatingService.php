<?php

namespace Training\Feedback\Model;

use Training\Feedback\Model\DataProvider;
use Magento\Store\Model\System\Store;

class RatingService
{
    protected $dataProvider;
    protected $systemStore;

    public function __construct(
        DataProvider $dataProvider,
        Store $systemStore
    ) {
        $this->dataProvider = $dataProvider;
        $this->systemStore = $systemStore;
    }

    public function getRatingsForStores(array $storeIds)
    {
        $ratings = [];
        foreach ($storeIds as $storeId) {
            // Retrieve or process ratings based on the store ID
            $ratings[$storeId] = 'Rating for store ' . $storeId; // Replace with actual logic
        }
        return $ratings;
    }

    public function getAvailableStores()
    {
        // Retrieve available stores
        return $this->systemStore->getStoreValuesForForm(false, true);
    }
}
