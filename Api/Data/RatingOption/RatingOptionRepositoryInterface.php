<?php

namespace Training\Feedback\Api\Data\RatingOption;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface RatingOptionRepositoryInterface
{
    /**
     * Save feedback.
     *
     * @param RatingOptionInterface $ratingOption
     * @return RatingOptionInterface
     * @throws LocalizedException
     */
    public function save(RatingOptionInterface $ratingOption): RatingOptionInterface;
    /**
     * Retrieve feedback.
     *
     * @param int $ratingOptionId
     * @return RatingOptionInterface
     * @throws LocalizedException
     */
    public function getById(int $ratingOptionId): RatingOptionInterface;
    /**
     * Retrieve feedbacks matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return RatingOptionSearchResultsInterface
     */

    public function getList(SearchCriteriaInterface $searchCriteria);
    /**
     * Delete feedback.
     *
     * @param RatingOptionInterface $ratingOption
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(RatingOptionInterface $ratingOption): bool;
    /**
     * Delete feedback by ID.
     *
     * @param int $ratingOptionId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById(int $ratingOptionId): bool;
}
