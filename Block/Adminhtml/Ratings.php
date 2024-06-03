<?php

namespace Training\Feedback\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Training\Feedback\ViewModel\FeedbackList;

class Ratings extends Template
{
    protected $_template = 'Training_Feedback::ratings.phtml';
    protected $viewModel;

    public function __construct(
        Template\Context $context,
        FeedbackList $viewModel,
        array $data = []
    ) {
        $this->viewModel = $viewModel;
        parent::__construct($context, $data);
    }

    public function getViewModel()
    {
        return $this->viewModel;
    }
}
