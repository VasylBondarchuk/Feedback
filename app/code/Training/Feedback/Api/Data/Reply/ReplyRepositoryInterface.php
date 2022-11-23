<?php

namespace Training\Feedback\Api\Data\Reply;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 *
 */
interface ReplyRepositoryInterface
{
    /**
     * Save reply.
     *
     * @param ReplyInterface $reply
     * @return ReplyInterface
     * @throws LocalizedException
     */
    public function save(ReplyInterface $reply): ReplyInterface;

    /**
     * Retrieve reply.
     *
     * @param $replyId
     * @return ReplyInterface
     * @throws LocalizedException
     */
    public function getById($replyId): ReplyInterface;

    /**
     * @param int $feedbackId
     * @return ReplyInterface
     */
    public function getByFeedbackId(int $feedbackId): ReplyInterface;

    /**
     * Retrieve reply matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return ReplySearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ReplySearchResultsInterface;

    /**
     * Delete reply.
     *
     * @param ReplyInterface $reply
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(ReplyInterface $reply): bool;
    /**
     * Delete reply by ID.
     *
     * @param int $replyId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $replyId): bool;

    /**
     * @param int $feedbackId
     * @return bool
     */
    public function deleteByFeedbackId(int $feedbackId): bool;
}
