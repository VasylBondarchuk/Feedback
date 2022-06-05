<?php

namespace Training\Feedback\Api\Data\Feedback;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface FeedbackRepositoryInterface
{
    /**
     * Save feedback.
     *
     * @param FeedbackInterface $feedback
     * @return FeedbackInterface
     * @throws LocalizedException
     */
    public function save(FeedbackInterface $feedback): FeedbackInterface;
    /**
     * Retrieve feedback.
     *
     * @param int $feedbackId
     * @return FeedbackInterface
     * @throws LocalizedException
     */
    public function getById(int $feedbackId): FeedbackInterface;
    /**
     * Retrieve feedbacks matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return FeedbackSearchResultsInterface
     */

    public function getList(SearchCriteriaInterface $searchCriteria);
    /**
     * Delete feedback.
     *
     * @param FeedbackInterface $feedback
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(FeedbackInterface $feedback): bool;
    /**
     * Delete feedback by ID.
     *
     * @param int $feedbackId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $feedbackId): bool;
}
