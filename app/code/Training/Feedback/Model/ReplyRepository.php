<?php

namespace Training\Feedback\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyInterface;
use Training\Feedback\Api\Data\Reply\ReplySearchResultsInterface;
use Training\Feedback\Api\Data\Reply\ReplySearchResultsInterfaceFactory;
use Training\Feedback\Model\ResourceModel\Reply as ReplyResource;
use Training\Feedback\Api\Data\Reply\ReplyInterfaceFactory as ReplyInterfaceFactory;
use Training\Feedback\Model\ResourceModel\Reply\CollectionFactory as ReplyCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 *
 */
class ReplyRepository implements ReplyRepositoryInterface
{
    /**
     * @var ReplyResource
     */
    private $resource;

    /**
     * @var ReplyInterfaceFactory
     */
    private $replyFactory;

    /**
     * @var ReplyCollectionFactory
     */
    private $replyCollectionFactory;

    /**
     * @var ReplySearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param ReplyResource $resource
     * @param ReplyInterfaceFactory $replyFactory
     * @param ReplyCollectionFactory $replyCollectionFactory
     * @param ReplySearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ReplyResource $resource,
        ReplyInterfaceFactory $replyFactory,
        ReplyCollectionFactory $replyCollectionFactory,
        ReplySearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->replyFactory = $replyFactory;
        $this->replyCollectionFactory = $replyCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param ReplyInterface $reply
     * @return ReplyInterface
     * @throws CouldNotSaveException
     */
    public function save(ReplyInterface $reply): ReplyInterface
    {
        try {
            $this->resource->save($reply);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the feedback: %1', $exception->getMessage()),
                $exception
            );
        }
        return $reply;
    }

    /**
     * @param $replyId
     * @return ReplyInterface
     * @throws NoSuchEntityException
     */
    public function getById($replyId): ReplyInterface
    {
        $reply = $this->replyFactory->create();
        $this->resource->load($reply, $replyId);
        if (!$reply->getId()) {
            throw new NoSuchEntityException(__('Reply with id "%1" does not exist.', $replyId));
        }
        return $reply;
    }


    /**
     * @throws NoSuchEntityException
     */
    public function getByFeedbackId(int $feedbackId): ReplyInterface
    {
        $reply = $this->replyFactory->create();
        $this->resource->load($reply, $feedbackId,ReplyInterface::FEEDBACK_ID);
        if (!$reply->getId()) {
            throw new NoSuchEntityException(__('Reply with feedback id "%1" does not exist.', $feedbackId));
        }
        return $reply;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ReplySearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ReplySearchResultsInterface
    {
        $collection = $this->replyCollectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @param ReplyInterface $reply
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ReplyInterface $reply): bool
    {
        try {
            $this->resource->delete($reply);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Reply: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @param $replyId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($replyId): bool
    {
        return $this->delete($this->getById($replyId));
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteByFeedbackId(int $feedbackId): bool
    {
        if($this->isReplyExist($feedbackId)){
            return $this->delete($this->getByFeedbackId($feedbackId));}
        return false;
    }

    /**
     * @param int $feedbackId
     * @return bool
     */
    public function isReplyExist(int $feedbackId): bool
    {
        try {
            $result = (bool)$this->getByFeedbackId($feedbackId);
        } catch (\Exception $e) {
            $result = false;
        }
        return $result;
    }
}
