<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Deletes a feedback
 */
class Delete extends Action
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
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param Context $context
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param ReplyRepositoryInterface $replyRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context,
    \Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface $feedbackRepository,
    \Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface $replyRepository,
    \Magento\Framework\Message\ManagerInterface $messageManager, // Correct class reference
    \Magento\Framework\Controller\ResultFactory $resultFactory,
    \Magento\Framework\App\RequestInterface $request
) {
    parent::__construct($context);
    $this->feedbackRepository = $feedbackRepository;
    $this->replyRepository = $replyRepository;
    $this->messageManager = $messageManager;
    $this->resultFactory = $resultFactory;
    $this->request = $request;
}


    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $feedbackId = (int)$this->getRequest()->getParam(self::REQUEST_FIELD_NAME);

        if ($feedbackId) {
            try {
                $this->feedbackRepository->deleteById($feedbackId);
                $this->replyRepository->deleteByFeedbackId($feedbackId);
                $this->messageManager->addSuccessMessage(__('You deleted the feedback and the related reply if existed.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->logger->error($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', [FeedbackInterface::FEEDBACK_ID => $feedbackId]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('We can\'t delete the feedback.'));
                $this->logger->error($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', [FeedbackInterface::FEEDBACK_ID => $feedbackId]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a feedback to delete.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
