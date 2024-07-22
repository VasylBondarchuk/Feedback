<?php

namespace Training\Feedback\Block\Adminhtml\Feedback\Edit;

use Magento\Backend\Block\Template;
use Training\Feedback\ViewModel\FeedbackRatings;

class Ratings extends Template{
    
    private FeedbackRatings $viewModel;

    public function __construct(
        Template\Context $context,
        FeedbackRatings $viewModel,
        array $data = []
    ) {
        $this->viewModel = $viewModel;
        parent::__construct($context, $data);
    }

    public function getViewModel() : FeedbackRatings
    {
        return $this->viewModel;
    }
}