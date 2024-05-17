<?php
declare(strict_types=1);

namespace Training\Feedback\Model\ResourceModel\RatingOption;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Training\Feedback\Model\RatingOption as RatingOptionModel;
use Training\Feedback\Model\ResourceModel\RatingOption as RatingOptionResourceModel;

class Collection extends AbstractCollection
{

    protected function _construct()
    {
        $this->_init(
            RatingOptionModel::class,
            RatingOptionResourceModel::class
        );
    }
}
