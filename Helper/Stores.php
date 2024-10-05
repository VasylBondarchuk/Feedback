<?php

declare(strict_types=1);

namespace Training\Feedback\Helper;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 *
 */
class Stores {

    const STORE_ID_REQUEST_NAME = 'store_id';    

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    /**
     * 
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
            RequestInterface $request,
            StoreManagerInterface $storeManager
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * 
     * @return int
     */
    private function getStoreId(): int {
        return (int) $this->request->get(self::STORE_ID_REQUEST_NAME);
    }

    /**
     * 
     * @return array
     */
    private function getAllStoreIds(): array {
        $storeIds = [];
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $storeIds[] = (int) $store->getId();
        }

        return $storeIds;
    }

    public function getStoreIds(): array {
        $storeIds = [];
        // All Store Views option
        if ($this->getStoreId() === 0) {
            $storeIds = $this->getAllStoreIds();
        } else {
            $storeIds[] = $this->getStoreId();
        }
        return $storeIds;
    }
}
