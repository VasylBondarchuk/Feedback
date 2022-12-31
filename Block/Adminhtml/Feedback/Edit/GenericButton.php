<?php

namespace Training\Feedback\Block\Adminhtml\Feedback\Edit;

use Magento\Backend\Block\Widget\Context;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;

/**
 * Parent class for button classes
 */
class GenericButton
{
    /**
     * @var Context
     */
    protected Context $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @return int
     */
    public function getFeedbackId(): int
    {
        return (int)$this->context->getRequest()->getParam(FeedbackInterface:: FEEDBACK_ID);
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl(string $route = '', array $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
