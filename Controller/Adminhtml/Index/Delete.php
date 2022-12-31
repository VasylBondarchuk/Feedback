<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;

/**
 * Deletes a feedback
 */
class Delete implements HttpPostActionInterface, HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_delete';
    const REQUEST_FIELD_NAME = 'feedback_id';

    /**
     * @var FeedbackRepositoryInterface
     */
    private FeedbackRepositoryInterface $feedbackRepository;

    /**
     * @var ReplyRepositoryInterface
     */
    private ReplyRepositoryInterface $replyRepository;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param ReplyRepositoryInterface $replyRepository
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param RequestInterface $request
     */
    public function __construct(
        FeedbackRepositoryInterface $feedbackRepository,
        ReplyRepositoryInterface $replyRepository,
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        RequestInterface $request
    ) {
        $this->feedbackRepository = $feedbackRepository;
        $this->replyRepository = $replyRepository;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $feedbackId = (int)($this->request->get(self::REQUEST_FIELD_NAME));
        if ($feedbackId) {
            try {
                $this->feedbackRepository->deleteById($feedbackId);
                $this->replyRepository->deleteByFeedbackId($feedbackId);
                $this->messageManager->addSuccessMessage(__('You deleted the feedback and related replies if existed.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', [FeedbackInterface:: FEEDBACK_ID => $feedbackId]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t delete the feedback.'));
                return $resultRedirect->setPath('*/*/edit', [FeedbackInterface:: FEEDBACK_ID => $feedbackId]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a feedback to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
