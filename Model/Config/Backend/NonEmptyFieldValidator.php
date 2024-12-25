<?php

namespace Training\Feedback\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\ValidatorException;

class NonEmptyFieldValidator extends Value {

    /**
     * Validate that the field value is not empty before saving
     *
     * @throws ValidatorException
     */
    public function beforeSave() {
        $value = $this->getValue();

        if ($value === null || $value === '') {
            // Accessing the field label directly as an array element
            $fieldConfig = $this->getFieldConfig();
            $fieldLabel = isset($fieldConfig['label']) ? (string) $fieldConfig['label'] : __('This field');

            throw new ValidatorException(__("The field '%1' cannot be empty.", $fieldLabel));
        }

        parent::beforeSave();
    }
}
