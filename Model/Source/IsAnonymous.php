<?php
declare(strict_types=1);

namespace Training\Feedback\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 *
 */
class IsAnonymous implements OptionSourceInterface
{
    
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
        return array_combine([1,0],[__('Yes'), __('No')]);
    }
}
