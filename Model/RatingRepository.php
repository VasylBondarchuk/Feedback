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

/**
 *
 */
class RatingRepository implements RatingRepositoryInterface
{
    /**
     * @var RatingResource
     */
    private RatingResource $resource;
    /**
     * @var RatingFactory
     */
    private RatingFactory $ratingFactory;
    /**
     * @var RatingCollectionFactory
     */
    private RatingCollectionFactory $ratingCollectionFactory;

    private RatingSearchResultsInterfaceFactory $searchResultsFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @param RatingResource $resource
     * @param RatingFactory $ratingFactory
     * @param RatingCollectionFactory $ratingCollectionFactory
     * @param RatingSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        RatingResource $resource,
        RatingFactory $ratingFactory,
        RatingCollectionFactory $ratingCollectionFactory,
        RatingSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->ratingOptionFactory = $ratingFactory;
        $this->ratingOptionCollectionFactory = $ratingCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Save Rating data
     *
     * @param RatingInterface $rating
     * @return RatingInterface
     * @throws CouldNotSaveException
     */
    public function save(RatingInterface $rating): RatingInterface
    {
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

    /**
     * Load Rating Option data by given Rating Identity
     *
     * @param int $ratingId
     * @return RatingInterface Interface
     * @throws NoSuchEntityException
     */
    public function getById(int $ratingId): RatingInterface
    {
        $rating = $this->ratingOptionFactory->create();
        $this->resource->load($rating, $ratingId);
        if (!$rating->getId()) {
            throw new NoSuchEntityException(__('Rating option with id "%1" does not exist.', $ratingId));
        }
        return $rating;
    }

    /**
     * Load Rating Option data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param SearchCriteriaInterface $searchCriteria
     * @return RatingSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->ratingOptionCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Rating
     *
     * @param RatingInterface $rating
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(RatingInterface $rating): bool
    {
        try {
            $this->resource->delete($rating);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the rating option: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }
    /**
     * Delete Rating by given Rating Identity
     *
     * @param int $ratingId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $ratingId): bool
    {
        return $this->delete($this->getById($ratingId));
    }
}
