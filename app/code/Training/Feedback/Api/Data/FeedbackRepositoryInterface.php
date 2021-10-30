<?php

namespace Training\Feedback\Api\Data;

interface FeedbackRepositoryInterface
{
    /**
     * Save feedback.
     *
     * @param \Training\Feedback\Api\Data\FeedbackInterface $feedback
     * @return \Training\Feedback\Api\Data\FeedbackInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Training\Feedback\Api\Data\FeedbackInterface $feedback);
    /**
     * Retrieve feedback.
     *
     * @param int $feedbackId
     * @return \Training\Feedback\Api\Data\FeedbackInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($feedbackId);
    /**
     * Retrieve feedbacks matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Training\Feedback\Api\Data\FeedbackSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
    /**
     * Delete feedback.
     *
     * @param \Training\Feedback\Api\Data\FeedbackInterface $feedback
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Training\Feedback\Api\Data\FeedbackInterface $feedback);
    /**
     * Delete feedback by ID.
     *
     * @param int $feedbackId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($feedbackId);
}
