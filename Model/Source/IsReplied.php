<?php
declare(strict_types=1);

namespace Training\Feedback\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Training\Feedback\Model\Feedback;

/**
 *
 */
class IsReplied implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */

    const OPTION_VALUES = [Feedback::IS_REPLIED_VALUE, Feedback::IS_NOT_REPLIED_VALUE];
    const OPTION_LABELS = [Feedback::IS_REPLIED_LABEL, Feedback::IS_NOT_REPLIED_LABEL];

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $toOptionArray = [];
        foreach ($this->getOptionValuesLabelsArray() as $value => $label) {
            $toOptionArray[] = [
                'label' => __($label),
                'value' => $value
            ];
        }
        return $toOptionArray;
    }

    /**
     * @return array
     */
    public function getOptionValuesLabelsArray() : array
    {
        return array_combine(self::OPTION_VALUES, self::OPTION_LABELS);
    }
}
