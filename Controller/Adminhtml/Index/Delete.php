<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;

/**
 *
 */
class Delete extends Action
{
    /**
     *
     */
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_delete';
    /**
     *
     */
    const REQUEST_FIELD_NAME = 'feedback_id';

    /**
     * @var FeedbackRepositoryInterface
     */
    private $feedbackRepository;

    /**
     * @var ReplyRepositoryInterface
     */
    private $replyRepository;

    /**
     * @param Context $context
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param ReplyRepositoryInterface $replyRepository
     */
    public function __construct(
        Context $context,
        FeedbackRepositoryInterface $feedbackRepository,
        ReplyRepositoryInterface $replyRepository
    ) {
        $this->feedbackRepository = $feedbackRepository;
        $this->replyRepository = $replyRepository;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $feedbackId = (int)$this->getRequest()->getParam(self::REQUEST_FIELD_NAME);

        if ($feedbackId) {
            try {
                $this->feedbackRepository->deleteById($feedbackId);
                $this->replyRepository->deleteByFeedbackId($feedbackId);

                $this->messageManager->addSuccessMessage(__('You deleted the feedback and related reply if existed.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', [self::REQUEST_FIELD_NAME => $feedbackId]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t delete the feedback.'));
                return $resultRedirect->setPath('*/*/edit', [self::REQUEST_FIELD_NAME => $feedbackId]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a feedback to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
