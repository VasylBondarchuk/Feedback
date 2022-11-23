<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;

/**
 *
 */
class Delete implements HttpGetActionInterface
{

    const ADMIN_RESOURCE = 'Training_Feedback::feedback_delete';
    
    const REQUEST_FIELD_NAME = 'feedback_id';
    
    private $feedbackRepository;
    
    private $replyRepository;
    
    private $messageManager;
    
    private $resultFactory;
    
    private $request;
    
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
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {        
        $feedbackId = (int)($this->request->get('feedback_id')); 
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
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
