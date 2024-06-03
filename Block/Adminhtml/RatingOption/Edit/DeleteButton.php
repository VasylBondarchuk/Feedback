<?php

declare(strict_types=1);

namespace Training\Feedback\Block\Adminhtml\RatingOption\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Training\Feedback\Api\Data\RatingOption\RatingOptionInterface;

/**
 * Provides data for 'Delete' button
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface {

    private const DELETE_PATH = 'training_feedback/ratingoption/delete';

    /**
     * @return array
     */
    public function getButtonData(): array {
        $data = [];
        if ($this->getRatingOptionId()) {
            $data = [
                'label' => __('Delete Rating Option'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to delete this rating option record?'
                ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl(): string {
        return $this->getUrl(
                        self::DELETE_PATH,
                        [RatingOptionInterface::RATING_OPTION_ID => $this->getRatingOptionId()]
        );
    }
}
