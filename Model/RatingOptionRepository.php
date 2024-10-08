<?php

declare(strict_types=1);

namespace Training\Feedback\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
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
class RatingOptionRepository implements RatingOptionRepositoryInterface {

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

    /**
     * 
     * @var RatingOptionSearchResultsInterfaceFactory
     */
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
    public function save(RatingOptionInterface $ratingOption): RatingOptionInterface {

        if ($this->isCodeExist($ratingOption->getRatingOptionCode(), $ratingOption->getId())) {
            throw new LocalizedException(__('Rating option code already exists.'));
        }
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
     * Check if rating option code exists
     * 
     * @param string $ratingOptionCode
     * @param int|null $ratingOptionId
     * @return bool
     */
    protected function isCodeExist($ratingOptionCode, $ratingOptionId = null): bool {
        $collection = $this->ratingOptionFactory->create()->getCollection()
                ->addFieldToFilter('rating_option_code', $ratingOptionCode);

        if ($ratingOptionId) {
            $collection->addFieldToFilter('rating_option_id', ['neq' => $ratingOptionId]);
        }

        return $collection->getSize() > 0;
    }

    /**
     * Load Rating Option data by given RatingOption Identity
     *
     * @param int $ratingOptionId
     * @return RatingOptionInterface Interface
     * @throws NoSuchEntityException
     */
    public function getById(int $ratingOptionId): RatingOptionInterface {
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
    public function getList(SearchCriteriaInterface $searchCriteria) {
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
    public function delete(RatingOptionInterface $ratingOption): bool {
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
    public function deleteById(int $ratingOptionId): bool {
        return $this->delete($this->getById($ratingOptionId));
    }

    /**
     * Get active rating options for a specific store.
     *
     * @param int $storeId
     * @return array
     */
    public function getStoreActiveRatingOptions(int $storeId) {
        $collection = $this->ratingOptionCollectionFactory->create();
        $collection->addFieldToFilter('is_active', 1);
        if ($storeId !== 0) {
            $collection->addFieldToFilter('store_id', $storeId);
        }
        return $collection->getItems();
    }

    /**
     * Get active rating options for all specified stores.
     *
     * @param array $storeIds
     * @return array
     */
    public function getAllActiveRatingOptions(array $storeIds): array {
        // Create a collection of rating options
        $collection = $this->ratingOptionCollectionFactory->create();

        // Filter the collection to include only active rating options
        $collection->addFieldToFilter('is_active', 1);

        // If specific store IDs are provided, filter the collection by these store IDs
        if (!empty($storeIds)) {
            $collection->addFieldToFilter('store_id', ['in' => $storeIds]);
        }

        // Return the filtered collection items
        return $collection->getItems();
    }    
}
