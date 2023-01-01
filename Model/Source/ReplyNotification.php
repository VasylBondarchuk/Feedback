<?php
declare(strict_types=1);

namespace Training\Feedback\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Training\Feedback\Model\Feedback;

/**
 *
 */
class ReplyNotification implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */

    const OPTION_VALUES = [Feedback::REPLY_NOTIFY, Feedback::REPLY_DO_NOT_NOTIFY];
    const OPTION_LABELS = [Feedback::REPLY_NOTIFY_LABEL, Feedback::REPLY_DO_NOT_NOTIFY_LABEL];

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
