<?php
declare(strict_types=1);

namespace Training\Feedback\Block\Adminhtml\RatingOption\Edit;

use Magento\Backend\Block\Widget\Context;
use Training\Feedback\Api\Data\RatingOption\RatingOptionInterface;

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
    public function getRatingOptionId(): int
    {
        return (int)$this->context->getRequest()->getParam(RatingOptionInterface::RATING_OPTION_ID);
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
