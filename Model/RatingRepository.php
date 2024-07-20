<?php

declare(strict_types=1);

namespace Training\Feedback\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Training\Feedback\Api\Data\Rating\RatingInterface;
use Training\Feedback\Api\Data\Rating\RatingInterfaceFactory as RatingFactory;
use Training\Feedback\Api\Data\Rating\RatingRepositoryInterface;
use Training\Feedback\Api\Data\Rating\RatingSearchResultsInterface;
use Training\Feedback\Api\Data\Rating\RatingSearchResultsInterfaceFactory;
use Training\Feedback\Model\ResourceModel\Rating as RatingResource;
use Training\Feedback\Model\ResourceModel\Rating\CollectionFactory as RatingCollectionFactory;

class RatingRepository implements RatingRepositoryInterface {

    private RatingResource $resource;
    private RatingFactory $ratingFactory;
    private RatingCollectionFactory $ratingCollectionFactory;
    private RatingSearchResultsInterfaceFactory $searchResultsFactory;
    private CollectionProcessorInterface $collectionProcessor;

    public function __construct(
            RatingResource $resource,
            RatingFactory $ratingFactory,
            RatingCollectionFactory $ratingCollectionFactory,
            RatingSearchResultsInterfaceFactory $searchResultsFactory,
            CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->ratingFactory = $ratingFactory;
        $this->ratingCollectionFactory = $ratingCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    public function save(RatingInterface $rating): RatingInterface {
        try {
            $this->resource->save($rating);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the rating option: %1', $exception->getMessage()),
                $exception
            );
        }
        return $rating;
    }

    public function getById(int $ratingId): RatingInterface {
        $rating = $this->ratingFactory->create();
        $this->resource->load($rating, $ratingId);
        if (!$rating->getId()) {
            throw new NoSuchEntityException(__('Rating option with id "%1" does not exist.', $ratingId));
        }
        return $rating;
    }

    public function getList(SearchCriteriaInterface $searchCriteria): RatingSearchResultsInterface {
        $collection = $this->ratingCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    public function delete(RatingInterface $rating): bool {
        try {
            $this->resource->delete($rating);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the rating option: %1', $exception->getMessage())
            );
        }
        return true;
    }

    public function deleteById(int $ratingId): bool {
        return $this->delete($this->getById($ratingId));
    }

    public function getRatingValue($feedbackId, $ratingOptionId) {
        $rating = $this->ratingCollectionFactory->create()
            ->addFieldToFilter('feedback_id', $feedbackId)
            ->addFieldToFilter('rating_option_id', $ratingOptionId)
            ->getFirstItem();
        return $rating ? $rating->getRatingValue() : 0;
    }

    /**
     * 
     * @param int $feedbackId
     * @param int $ratingOptionId
     * @return type
     */
    public function getRatingByFeedbackIdRatingOptionId(int $feedbackId, int $ratingOptionId) {
        $collection = $this->ratingCollectionFactory->create()
            ->addFieldToFilter('feedback_id', $feedbackId)
            ->addFieldToFilter('rating_option_id', $ratingOptionId);            
        return $collection->getFirstItem();
    }
}
