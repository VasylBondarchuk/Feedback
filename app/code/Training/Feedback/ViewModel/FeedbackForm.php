<?php

declare(strict_types=1);

namespace Training\Feedback\ViewModel;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class FeedbackForm implements ArgumentInterface
{
    private $urlBuilder;

    public function __construct(UrlInterface $urlBuilder)
    {
        $this->urlBuilder = $urlBuilder;
    }

    public function getActionUrl()
    {
        return $this->urlBuilder->getUrl('training_feedback/index/save');
    }
}
