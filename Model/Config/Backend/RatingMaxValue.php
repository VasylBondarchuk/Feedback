<?php

namespace Training\Feedback\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;

class RatingMaxValue extends Value
{
    /**
     * Validate positive integer non-zero value before saving
     *
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (!is_numeric($value) || (int)$value != $value || $value <= 0 ) {
            throw new ValidatorException(__('The "Ratings maximum value" must be an integer greater than 0.'));
        }
        parent::beforeSave();
    }
}
