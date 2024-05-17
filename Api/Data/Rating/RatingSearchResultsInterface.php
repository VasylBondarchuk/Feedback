<?php

namespace Training\Feedback\Api\Data\Rating;

use Magento\Framework\Api\SearchResultsInterface;

/**
 *
 */
interface RatingSearchResultsInterface extends SearchResultsInterface
{
    
    public function getItems(): array;


   /**
    * 
    * @param array $items
    * @return RatingOptionSearchResultsInterface
    */
    public function setItems(array $items): RatingSearchResultsInterface;
}
