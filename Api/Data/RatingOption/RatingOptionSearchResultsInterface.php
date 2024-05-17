<?php

namespace Training\Feedback\Api\Data\RatingOption;

use Magento\Framework\Api\SearchResultsInterface;

/**
 *
 */
interface RatingOptionSearchResultsInterface extends SearchResultsInterface
{
    
    public function getItems(): array;


   /**
    * 
    * @param array $items
    * @return RatingOptionSearchResultsInterface
    */
    public function setItems(array $items): RatingOptionSearchResultsInterface;
}
