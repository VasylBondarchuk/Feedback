<?php
declare(strict_types=1);

namespace Training\Feedback\Model\ResourceModel\Rating;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Training\Feedback\Model\Rating as RatingModel;
use Training\Feedback\Model\ResourceModel\Rating as RatingResourceModel;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            RatingModel::class,
            RatingResourceModel::class
        );
    }
}
