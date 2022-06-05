<?php

namespace Training\Feedback\Api\Data\Feedback;

use Magento\Framework\Api\SearchResultsInterface;

/**
 *
 */
interface FeedbackSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Feedback list.
     *
     */
    public function getItems(): array;


    /**
     * @param array $items
     * @return FeedbackSearchResultsInterface
     */
    public function setItems(array $items);
}
