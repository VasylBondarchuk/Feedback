<?php

namespace Training\Feedback\Api\Data\Rating;

/**
 *
 */
interface RatingInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const RATING_ID = 'rating_id';

    /**
     *
     */
    const FEEDBACK_ID = 'feedback_id';

    /**
     *
     */
    const RATING_OPTION_ID = 'rating_option_id';
    
    /**
     *
     */
    const RATING_VALUE = 'rating_value';
    
    /**
     *
     */
    const CREATED_AT = 'created_at';
    
    /**
     * 
     * @return int
     */
    public function getRatingId(): int;
   
    /**
     * 
     * @return int
     */
    public function getFeedbackId(): int;
   
    /**
     * 
     * @return int
     */
    public function getRatingOptionId(): int;
    
    /**
     * 
     * @return int
     */
    public function getRatingValue(): int;
    
   /**
    * 
    * @return string
    */
    public function getCreatedAt(): string;
    
  
    /**
     * 
     * @param int $ratingId
     * @return RatingInterface
     */
    public function setRatingId(int $ratingId): RatingInterface;


   /**
    * 
    * @param int $feedbackId
    * @return RatingOptionInterface
    */
    public function setFeedbackId(int $feedbackId): RatingInterface;
    
    
    /**
     * 
     * @param int $ratingOptionId
     * @return RatingOptionInterface
     */
    public function setRatingOptionId(int $ratingOptionId): RatingInterface;

    /**
     * 
     * @param int $ratingValue
     * @return RatingInterface
     */    
    public function setRatingValue(int $ratingValue): RatingInterface;
    
    /**
     * 
     * @param string $ratingValue
     * @return RatingInterface
     */
    public function setCreatedAt(string $ratingValue): RatingInterface;
    
}
