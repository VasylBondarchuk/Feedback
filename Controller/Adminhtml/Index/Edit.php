<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Edits feedback in the admin panel
 */
class Edit implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';

    private $messageManager;

    private $resultFactory;

    private $feedbackRepository;

    private $request;

    private $logger;

    /**
     *
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        FeedbackRepositoryInterface $feedbackRepository,
        RequestInterface $request,
        LoggerInterface  $logger
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     *
     * @return type
     */
    public function execute()
    {
        $feedbackId = (int)($this->request->get('feedback_id'));
        if (!$this->isFeedbackExist($feedbackId)) {
            $this->messageManager->addErrorMessage(__('This feedback does not exist.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/');
        }
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
            ->getConfig()->getTitle()->prepend(__('Edit Feedback'));
        return $resultPage;
    }
    /**
     * @param $feedbackId
     * @return bool
     */
    private function isFeedbackExist($feedbackId): bool
    {
        $exist = false;
            try {
                $this->feedbackRepository->getById($feedbackId);
                $exist = true;
            } catch (NoSuchEntityException $e) {
                $this->logger->error($e->getLogMessage());
            }
        return $exist;
    }
}
