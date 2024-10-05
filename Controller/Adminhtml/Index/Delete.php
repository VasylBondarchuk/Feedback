<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\AuthorizationInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;


/**
 * Deletes a feedback
 */
class Delete implements ActionInterface
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
     * 
     * @var AuthorizationInterface
     */
    private AuthorizationInterface $authorization;    
    

    /**
     * @param Context $context
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param ReplyRepositoryInterface $replyRepository
     * @param LoggerInterface $logger
     */
    public function __construct(    
    FeedbackRepositoryInterface $feedbackRepository,
    ReplyRepositoryInterface $replyRepository,
    ManagerInterface $messageManager, // Correct class reference
    ResultFactory $resultFactory,
    RequestInterface $request,
    AuthorizationInterface $authorization
) {    
    $this->feedbackRepository = $feedbackRepository;
    $this->replyRepository = $replyRepository;
    $this->messageManager = $messageManager;
    $this->resultFactory = $resultFactory;
    $this->request = $request;
    $this->authorization = $authorization;
}


    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
         // Check if the admin user has the required permission
        if (!$this->authorization->isAllowed(self::ADMIN_RESOURCE)) {
            $this->messageManager->addErrorMessage(__('You are not authorized to delete feedback.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                            ->setUrl($this->urlBuilder->getUrl('*/*/'));
        }
        
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $feedbackId = (int)$this->request->get(self::REQUEST_FIELD_NAME);

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
