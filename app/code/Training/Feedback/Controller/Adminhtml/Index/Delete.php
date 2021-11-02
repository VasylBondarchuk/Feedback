<?php

namespace Training\Feedback\Controller\Adminhtml\Index;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Training\Feedback\Api\Data\FeedbackRepositoryInterface;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_delete';

    private $feedbackRepository;
    /**
     * @param \Magento\Backend\App\Action\Context $context $context
     * @param FeedbackRepositoryInterface $feedbackRepository
     */
    public function __construct(
        Context $context,
        FeedbackRepositoryInterface $feedbackRepository
    ) {
        $this->feedbackRepository = $feedbackRepository;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('feedback_id');
        if ($id) {
            try {
                $this->feedbackRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the feedback.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['feedback_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t delete the feedback.'));
                return $resultRedirect->setPath('*/*/edit', ['feedback_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a feedback to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
