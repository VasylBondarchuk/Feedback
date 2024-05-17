<?php
declare(strict_types=1);

namespace Training\Feedback\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Training\Feedback\Api\Data\RatingOption\RatingOptionInterface;
use Training\Feedback\Api\Data\RatingOption\RatingOptionInterfaceFactory as RatingOptionFactory;
use Training\Feedback\Api\Data\RatingOption\RatingOptionRepositoryInterface;
use Training\Feedback\Api\Data\RatingOption\RatingOptionSearchResultsInterface;
use Training\Feedback\Api\Data\RatingOption\RatingOptionSearchResultsInterfaceFactory;
use Training\Feedback\Model\ResourceModel\RatingOption as RatingOptionResource;
use Training\Feedback\Model\ResourceModel\RatingOption\CollectionFactory as RatingOptionCollectionFactory;

/**
 *
 */
class RatingOptionRepository implements RatingOptionRepositoryInterface
{
    /**
     * @var RatingOptionResource
     */
    private RatingOptionResource $resource;
    /**
     * @var RatingOptionFactory
     */
    private RatingOptionFactory $ratingOptionFactory;
    /**
     * @var RatingOptionCollectionFactory
     */
    private RatingOptionCollectionFactory $ratingOptionCollectionFactory;

    private RatingOptionSearchResultsInterfaceFactory $searchResultsFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @param RatingOptionResource $resource
     * @param RatingOptionFactory $ratingOptionFactory
     * @param RatingOptionCollectionFactory $ratingOptionCollectionFactory
     * @param RatingOptionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        RatingOptionResource $resource,
        RatingOptionFactory $ratingOptionFactory,
        RatingOptionCollectionFactory $ratingOptionCollectionFactory,
        RatingOptionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->ratingOptionFactory = $ratingOptionFactory;
        $this->ratingOptionCollectionFactory = $ratingOptionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Save RatingOption data
     *
     * @param RatingOptionInterface $ratingOption
     * @return RatingOptionInterface
     * @throws CouldNotSaveException
     */
    public function save(RatingOptionInterface $ratingOption): RatingOptionInterface
    {
        try {
            $this->resource->save($ratingOption);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the rating option: %1', $exception->getMessage()),
                $exception
            );
        }
        return $ratingOption;
    }

    /**
     * Load Rating Option data by given RatingOption Identity
     *
     * @param int $ratingOptionId
     * @return RatingOptionInterface Interface
     * @throws NoSuchEntityException
     */
    public function getById(int $ratingOptionId): RatingOptionInterface
    {
        $ratingOption = $this->ratingOptionFactory->create();
        $this->resource->load($ratingOption, $ratingOptionId);
        if (!$ratingOption->getId()) {
            throw new NoSuchEntityException(__('Rating option with id "%1" does not exist.', $ratingOptionId));
        }
        return $ratingOption;
    }

    /**
     * Load Rating Option data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param SearchCriteriaInterface $searchCriteria
     * @return RatingOptionSearchResultsInterface
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
     * Delete RatingOption
     *
     * @param RatingOptionInterface $ratingOption
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(RatingOptionInterface $ratingOption): bool
    {
        try {
            $this->resource->delete($ratingOption);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the rating option: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }
    /**
     * Delete RatingOption by given RatingOption Identity
     *
     * @param int $ratingOptionId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $ratingOptionId): bool
    {
        return $this->delete($this->getById($ratingOptionId));
    }
}
