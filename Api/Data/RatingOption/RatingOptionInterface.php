<?php

namespace Training\Feedback\Api\Data\RatingOption;

/**
 *
 */
interface RatingOptionInterface
{
    /**
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const RATING_OPTION_ID = 'rating_option_id';

    /**
     *
     */
    const RATING_OPTION_CODE = 'rating_option_code';

    /**
     *
     */
    const RATING_OPTION_NAME = 'rating_option_name';   
    
    
    /**
     *
     */
    const RATING_OPTION_IS_ACTIVE = 'is_active';
    
    /**
     *
     */
    const RATING_STORE_ID = 'store_id';
    
    /**
     * 
     * @return int
     */
    public function getRatingOptionId(): int;

    /**
     * 
     * @return string
     */
    public function getRatingOptionCode(): string;

    /**
     * 
     * @return string
     */
    public function getRatingOptionName(): string; 
    
    /**
    * 
    * @return int
    */
    public function getIsActive(): int;
    
    /**
     * 
     * @return int
     */
    public function getStoreId(): int;
    
  
    /**
     * 
     * @param int $ratingOptionId
     * @return RatingOptionInterface
     */
    public function setRatingOptionId(int $ratingOptionId): RatingOptionInterface;


    /**
     * 
     * @param string $ratingOptionCode
     * @return RatingOptionInterface
     */
    public function setRatingOptionCode(string $ratingOptionCode): RatingOptionInterface;
    
    
    /**
     * 
     * @param string $ratingOptionName
     * @return RatingOptionInterface
     */
    public function setRatingOptionName(string $ratingOptionName): RatingOptionInterface;    
    
    /**
    * 
    * @return int
    */
    public function setIsActive(int $isActive): RatingOptionInterface;
    
    /**
     * 
     * @return int
     */
    public function setStoreId(int $storeId): RatingOptionInterface;
    
}
