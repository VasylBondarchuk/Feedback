<?php
declare(strict_types=1);

namespace Training\Feedback\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Rating Option resource model
 */
class RatingOption extends AbstractDb
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('training_feedback_rating_options', 'rating_option_id');
    }    
}

