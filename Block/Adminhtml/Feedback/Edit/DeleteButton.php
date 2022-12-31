<?php

namespace Training\Feedback\Block\Adminhtml\Feedback\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;

/**
 * Provides data for 'Delete' button
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    private const DELETE_PATH = 'training_feedback/index/delete';
    /**
     * @return array
     */
    public function getButtonData(): array
    {
        $data = [];
        if ($this->getFeedbackId()) {
            $data = [
                'label' => __('Delete Feedback'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\', {"data": {}})',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl(): string
    {
        return $this->getUrl(self::DELETE_PATH, [FeedbackInterface:: FEEDBACK_ID => $this->getFeedbackId()]);
    }
}
