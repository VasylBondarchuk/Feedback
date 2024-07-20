<?php
namespace Training\Feedback\Block\Adminhtml\Feedback\Edit;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\Feedback\ViewModel\FeedbackRatings;

class Ratings extends Template
{
    protected $feedbackRatingsViewModel;

    public function __construct(
        Context $context,
        FeedbackRatings $feedbackRatingsViewModel,
        array $data = []
    ) {
        $this->feedbackRatingsViewModel = $feedbackRatingsViewModel;
        parent::__construct($context, $data);
    }

    public function getViewModel()
    {
        return $this->feedbackRatingsViewModel;
    }
}
