<?php

declare(strict_types=1);

namespace Training\Feedback\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Training\Feedback\Model\RatingOptionRepository;
use Training\Feedback\Model\RatingRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 *
 */
class FeedbackRatings implements ArgumentInterface {

    private const REQUEST_FIELD_NAME = 'feedback_id';
    private const DEFAULT_RATING_MIN = 0;

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
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * 
     * @param RatingOptionRepository $ratingOptionRepository
     * @param RatingRepository $ratingRepository
     * @param RequestInterface $request
     */
    public function __construct(
            RatingOptionRepository $ratingOptionRepository,
            RatingRepository $ratingRepository,
            RequestInterface $request,
            StoreManagerInterface $storeManager
    ) {
        $this->ratingOptionRepository = $ratingOptionRepository;
        $this->ratingRepository = $ratingRepository;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * 
     * @return type
     */
    public function getActiveRatingOptions() {
        $storeId = (int) ($this->request->get('store'));
        return $storeId !== 0 ? $this->ratingOptionRepository->getStoreActiveRatingOptions($storeId) : $this->ratingOptionRepository->getAllActiveRatingOptions($this->getAllStoreIds());
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
