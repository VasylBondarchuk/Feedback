<?php

namespace Training\Feedback\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;

class PositiveIntegerValidator extends Value
{
    /**
     * Validate that the field value is a positive integer greater than zero before saving
     *
     * @throws ValidatorException
     */
    public function beforeSave()
    {
        $value = $this->getValue();

        if (!is_numeric($value) || (int)$value != $value || $value <= 0) {
            // Accessing the field label directly from the field configuration
            $fieldConfig = $this->getFieldConfig();
            $fieldLabel = isset($fieldConfig['label']) ? (string)$fieldConfig['label'] : __('This field');

            throw new ValidatorException(
                __("The field '%1' must be a positive integer.", $fieldLabel)
            );
        }

        parent::beforeSave();
    }
}
