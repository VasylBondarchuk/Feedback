<?php

declare(strict_types=1);

namespace Training\Feedback\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Training\Feedback\Model\RatingOptionRepository;
use Training\Feedback\Model\RatingRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Training\Feedback\Helper\FeedbackConfigProvider;

/**
 *
 */
class FeedbackRatings implements ArgumentInterface {

    private const REQUEST_FIELD_NAME = 'feedback_id';
    private const DEFAULT_RATING_MIN = 0;
    private const DEFAULT_RATING_MAX = 5;

    /**
     * @var CollectionFactory
     */
    private RatingOptionRepository $ratingOptionRepository;

    /**
     * @var CollectionFactory
     */
    private RatingRepository $ratingRepository;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * 
     * @var FeedbackConfigProvider
     */
    private FeedbackConfigProvider $feedbackConfigProvider;
    
    /**
     * 
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    
    public function __construct(
            RatingOptionRepository $ratingOptionRepository,
            RatingRepository $ratingRepository,
            RequestInterface $request,
            StoreManagerInterface $storeManager,
            FeedbackConfigProvider $feedbackConfigProvider
    ) {
        $this->ratingOptionRepository = $ratingOptionRepository;
        $this->ratingRepository = $ratingRepository;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->feedbackConfigProvider = $feedbackConfigProvider;
    }

    /**
     * 
     * @return type
     */
    public function getActiveRatingOptions() {
        $storeId = (int) ($this->request->get('store'));
        return $storeId !== 0
                ? $this->ratingOptionRepository->getStoreActiveRatingOptions($storeId)
                : $this->ratingOptionRepository->getAllActiveRatingOptions($this->getAllStoreIds());
    }

    /**
     * 
     * @param int $ratingOptionId
     * @param int $feedbackId
     * @return int
     */
    public function getRatingValue(int $ratingOptionId, int $feedbackId): int {
        $rating = $feedbackId ? $this->ratingRepository->getRatingValue($feedbackId, $ratingOptionId) : self::DEFAULT_RATING_MIN;
        return (int) $rating;
    }
    
    
    /**
     * 
     * @return int
     */
    public function getRatingOptionMaxValue(): int {
        return $this->feedbackConfigProvider->getRatingMaxValue()
               ? (int)$this->feedbackConfigProvider->getRatingMaxValue()
               : self::DEFAULT_RATING_MAX; 
    }
    
    /**
     * Checks if there are any active rating options available.
     *
     * @return bool
     */
    public function hasRatingOptions(): bool {        
        return count($this->getActiveRatingOptions()) === 0;
    }

    /**
     * 
     * @return int
     */
    public function getFeedbackId(): int {

        return (int) $this->request->get(self::REQUEST_FIELD_NAME);
    }

    private function getAllStoreIds(): array {
        $storeIds = [];
        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {
            $storeIds[] = (int) $store->getId();
        }

        return $storeIds;
    }
}
