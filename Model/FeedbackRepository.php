<?php

namespace Training\Feedback\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Training\Feedback\Api\Data;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackInterfaceFactory as FeedbackFactory;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackSearchResultsInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackSearchResultsInterfaceFactory;
use Training\Feedback\Model\ResourceModel\Feedback as FeedbackResource;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory as FeedbackCollectionFactory;

/**
 *
 */
class FeedbackRepository implements FeedbackRepositoryInterface
{
    /**
     * @var FeedbackResource
     */
    private FeedbackResource $resource;
    /**
     * @var FeedbackFactory
     */
    private FeedbackFactory $feedbackFactory;
    /**
     * @var FeedbackCollectionFactory
     */
    private FeedbackCollectionFactory $feedbackCollectionFactory;

    private FeedbackSearchResultsInterfaceFactory $searchResultsFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param FeedbackResource $resource
     * @param FeedbackFactory $feedbackFactory
     * @param FeedbackCollectionFactory $feedbackCollectionFactory
     * @param FeedbackSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        FeedbackResource $resource,
        FeedbackFactory $feedbackFactory,
        FeedbackCollectionFactory $feedbackCollectionFactory,
        FeedbackSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->feedbackFactory = $feedbackFactory;
        $this->feedbackCollectionFactory = $feedbackCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Save Feedback data
     *
     * @param FeedbackInterface $feedback
     * @return FeedbackInterface
     * @throws CouldNotSaveException
     */
    public function save(FeedbackInterface $feedback): FeedbackInterface
    {
        try {
            $this->resource->save($feedback);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the feedback: %1', $exception->getMessage()),
                $exception
            );
        }
        return $feedback;
    }

    /**
     * Load Feedback data by given Feedback Identity
     *
     * @param int $feedbackId
     * @return FeedbackInterface Interface
     * @throws NoSuchEntityException
     */
    public function getById(int $feedbackId): FeedbackInterface
    {
        $feedback = $this->feedbackFactory->create();
        $this->resource->load($feedback, $feedbackId);
        if (!$feedback->getId()) {
            throw new NoSuchEntityException(__('Feedback with id "%1" does not exist.', $feedbackId));
        }
        return $feedback;
    }

    /**
     * Load Feedback data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param SearchCriteriaInterface $searchCriteria
     * @return FeedbackSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->feedbackCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Feedback
     *
     * @param FeedbackInterface $feedback
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(FeedbackInterface $feedback): bool
    {
        try {
            $this->resource->delete($feedback);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the feedback: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }
    /**
     * Delete Feedback by given Feedback Identity
     *
     * @param int $feedbackId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $feedbackId): bool
    {
        return $this->delete($this->getById($feedbackId));
    }
}
