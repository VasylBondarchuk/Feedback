<?php

declare(strict_types=1);

namespace Training\Feedback\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Training\Feedback\Api\Data\Rating\RatingInterface;

/**
 * Rating model
 */
class Rating extends AbstractExtensibleModel implements RatingInterface {

    /**
     * @return void
     */
    protected function _construct() {
        $this->_init(ResourceModel\Rating::class);
    }

    /**
     * 
     * @return int
     */
    public function getRatingId(): int {
        return $this->getData(self::RATING_ID);
    }

    /**
     * 
     * @return int
     */
    public function getFeedbackId(): int {
        return $this->getData(self::FEEDBACK_ID);
    }
    
    /**
     * 
     * @return int
     */
    public function getRatingOptionId(): int {
        return $this->getData(self::RATING_OPTION_ID);
    }

    /**
     * 
     * @return int
     */
    public function getRatingValue(): int {
        return $this->getData(self::RATING_VALUE);
    }
    
    /**
     * 
     * @return string
     */
    public function getCreatedAt(): string {
        return $this->getData(self::CREATED_AT);
    }
    
    /**
     * 
     * @param int $ratingId
     * @return RatingInterface
     */
    public function setRatingId(int $ratingId): RatingInterface {
        return $this->setData(self::RATING_ID, $ratingId);
    }
    
    /**
     * 
     * @param int $feedbackId
     * @return RatingInterface
     */
    public function setFeedbackId(int $feedbackId): RatingInterface {
        return $this->setData(self::FEEDBACK_ID, $feedbackId);
    }
    
    
    public function setRatingOptionId(int $ratingOptionId): RatingInterface {
        return $this->setData(self::RATING_OPTION_ID, $ratingOptionId);
    }

    /**
     * 
     * @param int $ratingValue
     * @return RatingInterface
     */
    public function setRatingValue(int $ratingValue): RatingInterface {
        return $this->setData(self::RATING_VALUE, $ratingValue);
    }
        
    /**
     * 
     * @param string $createdAt
     * @return RatingInterface
     */
    public function setCreatedAt(string $createdAt): RatingInterface {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
}
