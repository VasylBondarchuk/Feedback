<?php

namespace Training\Feedback\Ui\Component\Form;

use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Training\Feedback\Model\RatingOptionRepository;

class RatingOptions implements DataProviderInterface
{
    protected $ratingOptionRepository;

    public function __construct(RatingOptionRepository $ratingOptionRepository)
    {
        $this->ratingOptionRepository = $ratingOptionRepository;
    }

    public function getData()
    {
        $ratingOptions = $this->ratingOptionRepository->getActiveOptions();
        $result = [];
        foreach ($ratingOptions as $ratingOption) {
            $result[] = [
                'ratingOptionId' => $ratingOption->getRatingOptionId(),
                'ratingOptionName' => $ratingOption->getRatingOptionName(),
                'ratingOptionMaxValue' => $ratingOption->getRatingOptionMaxValue(),
                'ratingValue' => 2 // Assuming getValue() fetches current rating value
            ];
        }
        return ['items' => $result];
    }
}
