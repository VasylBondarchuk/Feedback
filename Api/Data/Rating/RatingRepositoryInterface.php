<?php

namespace Training\Feedback\Api\Data\Rating;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

interface RatingRepositoryInterface
{
    /**
     * 
     * @param RatingInterface $rating
     * @return RatingInterface
     */
    public function save(RatingInterface $rating): RatingInterface;
    
    /**
     * 
     * @param int $ratingId
     * @return RatingInterface
     */
    public function getById(int $ratingId): RatingInterface;
    
    /**
     * 
     * @param SearchCriteriaInterface $searchCriteria
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
    
    public function delete(RatingInterface $rating): bool;
    
    /**
     * 
     * @param int $ratingId
     * @return bool
     */
    public function deleteById(int $ratingId): bool;
}
