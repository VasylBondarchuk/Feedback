<?php

namespace Training\Feedback\Model\Source;
use Magento\Framework\Data\OptionSourceInterface;
use Training\Feedback\Model\Feedback;

class IsActive implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Published'),
                'value' => Feedback::STATUS_ACTIVE
            ],
            [
                'label' => __('Not published'),
                'value' => Feedback::STATUS_INACTIVE
            ]
        ];
    }
}
