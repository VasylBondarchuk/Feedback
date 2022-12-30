<?php

namespace Training\Feedback\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Training\Feedback\Api\Data\Reply\ReplyInterface;
use Training\Feedback\Api\Data\Reply\ReplyInterfaceFactory as ReplyInterfaceFactory;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplySearchResultsInterface;
use Training\Feedback\Api\Data\Reply\ReplySearchResultsInterfaceFactory;
use Training\Feedback\Model\ResourceModel\Reply as ReplyResource;
use Training\Feedback\Model\ResourceModel\Reply\CollectionFactory as ReplyCollectionFactory;
use Magento\Backend\Model\Auth\Session;

/**
 * Reply repository
 */
class ReplyRepository implements ReplyRepositoryInterface
{
    /**
     * @var ReplyResource
     */
    private ReplyResource $resource;

    /**
     * @var ReplyInterfaceFactory
     */
    private ReplyInterfaceFactory $replyFactory;

    /**
     * @var ReplyCollectionFactory
     */
    private ReplyCollectionFactory $replyCollectionFactory;

    /**
     * @var ReplySearchResultsInterfaceFactory
     */
    private ReplySearchResultsInterfaceFactory $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;

    /**
     * @var Session
     */
    protected Session $authSession;

    /**
     * @param ReplyResource $resource
     * @param ReplyInterfaceFactory $replyFactory
     * @param ReplyCollectionFactory $replyCollectionFactory
     * @param ReplySearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param Session $authSession
     */
    public function __construct(
        ReplyResource $resource,
        ReplyInterfaceFactory $replyFactory,
        ReplyCollectionFactory $replyCollectionFactory,
        ReplySearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        Session $authSession,
    ) {
        $this->resource = $resource;
        $this->replyFactory = $replyFactory;
        $this->replyCollectionFactory = $replyCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->authSession = $authSession;
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
                __('Could not save the Reply: %1', $exception->getMessage()),
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
        $replyCollection = $this->replyCollectionFactory->create();
        $reply = $replyCollection
                ->addFieldToFilter(ReplyInterface::FEEDBACK_ID, $feedbackId)
                ->addFieldToFilter(ReplyInterface::ADMIN_ID, $this->getCurrentAdminId())->getFirstItem();

        if (!$reply->getId()) {
            throw new NoSuchEntityException(__('Reply with feedback id "%1" does not exist.', $feedbackId));
        }
        return $reply;
    }

    /**
     * @return int
     */
    private function getCurrentAdminId() : int
    {
        return (int)$this->authSession->getUser()->getId();
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
        if ($this->isReplyExist($feedbackId)) {
            return $this->delete($this->getByFeedbackId($feedbackId));
        }
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

    /**
     * @param int $feedbackId
     * @return bool
     */
    public function isFeedbackReplied(int $feedbackId): bool
    {
        try {
            $result = (bool)$this->getByFeedbackId($feedbackId)->getReplyText();
        } catch (\Exception $e) {
            $result = false;
        }
        return $result;
    }

    /**
     * @param int $feedbackId
     * @return ReplyResource\Collection
     */
    public function getRepliesByFeedbackId(int $feedbackId): ReplyResource\Collection
    {
        $replyCollection = $this->replyCollectionFactory->create();
        return $replyCollection
            ->addFieldToFilter(ReplyInterface::FEEDBACK_ID, $feedbackId);
    }
}
