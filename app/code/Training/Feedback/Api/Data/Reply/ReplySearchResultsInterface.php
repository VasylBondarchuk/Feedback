<?php

namespace Training\Feedback\Api\Data\Reply;

use Magento\Framework\Api\SearchResultsInterface;

interface ReplySearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Reply list.
     *
     * @return ReplyInterface[]
     */
    public function getItems(): array;

    /**
     * Set Reply list.
     *
     * @param ReplyInterface[] $items
     * @return ReplySearchResultsInterface
     */
    public function setItems(array $items): ReplySearchResultsInterface;
}
