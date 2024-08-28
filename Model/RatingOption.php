<?php

declare(strict_types=1);

namespace Training\Feedback\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Training\Feedback\Api\Data\RatingOption\RatingOptionInterface;

/**
 * Rating model
 */
class RatingOption extends AbstractExtensibleModel implements RatingOptionInterface {

    /**
     * @return void
     */
    protected function _construct() {
        $this->_init(ResourceModel\RatingOption::class);
    }

    /**
     * Get RatingOption ID
     *
     * @return int|null
     */
    public function getRatingOptionId(): int {
        return (int)$this->getData(self::RATING_OPTION_ID);
    }

    /**
     * Get RatingOption code
     *
     * @return int|null
     */
    public function getRatingOptionCode(): string {
        return $this->getData(self::RATING_OPTION_CODE);
    }

    /**
     * Get RatingOption name
     *
     * @return int|null
     */
    public function getRatingOptionName(): string {
        return $this->getData(self::RATING_OPTION_NAME);
    }
    
    /**
     * Get Rating Option max Value
     *
     * @return int|null
    */
    public function getRatingOptionMaxValue(): int {
        return (int)$this->getData(self::RATING_OPTION_MAX_VALUE);
    }
    
    /**
     * Get Rating Option max Value
     *
     * @return int|null
    */
    public function getIsActive(): int {
        return (int)$this->getData(self::RATING_OPTION_IS_ACTIVE);
    }
    
    /**
     * Get Rating Option max Value
     *
     * @return int|null
    */
    public function getStoreId(): int {
        return (int)$this->getData(self::RATING_STORE_ID);
    }

    /**
     * 
     * @param int $ratingOptionId
     * @return RatingOptionInterface
     */
    public function setRatingOptionId(int $ratingOptionId): RatingOptionInterface {
        return $this->setData(self::RATING_OPTION_ID, $ratingOptionId);
    }

    /**
     * 
     * @param string $ratingOptionCode
     * @return RatingOptionInterface
     */
    public function setRatingOptionCode(string $ratingOptionCode): RatingOptionInterface {
        return $this->setData(self::RATING_OPTION_CODE, $ratingOptionCode);
    }

    /**
     * 
     * @param string $ratingOptionName
     * @return RatingOptionInterface
     */
    public function setRatingOptionName(string $ratingOptionName): RatingOptionInterface {
        return $this->setData(self::RATING_OPTION_NAME, $ratingOptionName);
    }
    
    /**
     * 
     * @param string $ratingOptionMaxValue
     * @return RatingOptionInterface
     */
    public function setRatingOptionMaxValue(int $ratingOptionMaxValue): RatingOptionInterface {
        return $this->setData(self::RATING_OPTION_MAX_VALUE, $ratingOptionMaxValue);
    }
    
    /**
    * 
    * @return int
    */
    public function setIsActive(int $isActive): RatingOptionInterface{
        return $this->setData(self::RATING_OPTION_IS_ACTIVE, $isActive);
    }
    /**
     * 
     * @return int
     */
    public function setStoreId(int $storeId): RatingOptionInterface{
        return $this->setData(self::RATING_STORE_ID, $storeId);
    }
}
