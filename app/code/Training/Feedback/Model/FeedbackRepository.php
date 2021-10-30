<?php

namespace Training\Feedback\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Training\Feedback\Api\Data;
use Training\Feedback\Api\Data\FeedbackInterface;
use Training\Feedback\Api\Data\FeedbackRepositoryInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Training\Feedback\Api\Data\FeedbackSearchResultsInterface;
use Training\Feedback\Model\ResourceModel\Feedback as FeedbackResource;
use Training\Feedback\Api\Data\FeedbackInterfaceFactory as FeedbackFactory;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory as FeedbackCollectionFactory;

class FeedbackRepository implements FeedbackRepositoryInterface
{
    /**
    © 2018 M2Training.com.ua 6
     * @var FeedbackResource
     */
    private $resource;
    /**
     * @var FeedbackFactory
     */
    private $feedbackFactory;
    /**
     * @var FeedbackCollectionFactory
     */
    private $feedbackCollectionFactory;
    /**
     * @var Data\FeedbackSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @param FeedbackResource $resource
     * @param FeedbackFactory $feedbackFactory
     * @param FeedbackCollectionFactory $feedbackCollectionFactory
     * @param Data\FeedbackSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        FeedbackResource $resource,
        FeedbackFactory $feedbackFactory,
        FeedbackCollectionFactory $feedbackCollectionFactory,
        Data\FeedbackSearchResultsInterfaceFactory $searchResultsFactory,
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
    public function save(FeedbackInterface $feedback)
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
    © 2018 M2Training.com.ua 7
     *
     * @param string $feedbackId
     * @return FeedbackInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($feedbackId)
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
     * @param SearchCriteriaInterface $criteria
     * @return FeedbackSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->feedbackCollectionFactory->create();
        $this->collectionProcessor->process($criteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
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
    public function delete(FeedbackInterface $feedback)
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
     * @param string $feedbackId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($feedbackId)
    {
        return $this->delete($this->getById($feedbackId));
    }
}
