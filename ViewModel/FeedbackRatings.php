<?php
declare(strict_types=1);

namespace Training\Feedback\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Training\Feedback\Model\RatingOptionRepository;
use Training\Feedback\Model\RatingRepository;
use Magento\Framework\App\RequestInterface;

/**
 *
 */
class FeedbackRatings implements ArgumentInterface {

    private const REQUEST_FIELD_NAME = 'feedback_id';
    
    private const DEFAULT_RATING_MIN = 0;

    /**
     * @var CollectionFactory
     */
    private RatingOptionRepository $ratingOptionRepository;

    /**
     * @var CollectionFactory
     */
    private RatingRepository $ratingRepository;    
    
    
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * 
     * @param RatingOptionRepository $ratingOptionRepository
     * @param RatingRepository $ratingRepository
     * @param RequestInterface $request
     */
    public function __construct(            
            RatingOptionRepository $ratingOptionRepository,
            RatingRepository $ratingRepository,            
            RequestInterface $request
    ) {        
        $this->ratingOptionRepository = $ratingOptionRepository;
        $this->ratingRepository = $ratingRepository;        
        $this->request = $request;
    }    
    
    /**
     * 
     * @return type
     */
    public function getActiveRatingOptions() {
        return $this->ratingOptionRepository->getActiveOptions();
    }

    /**
     * 
     * @param int $ratingOptionId
     * @param int $feedbackId
     * @return int
     */
    public function getRatingValue(int $ratingOptionId, int $feedbackId) : int {        
        $rating = $feedbackId
                ? $this->ratingRepository->getRatingValue($feedbackId, $ratingOptionId)
                : self::DEFAULT_RATING_MIN;
        return (int)$rating;
    }    
 
    /**
     * 
     * @return int
     */
    public function getFeedbackId() : int {        
        
        return (int)$this->request->get(self::REQUEST_FIELD_NAME);
    } 
    
}
