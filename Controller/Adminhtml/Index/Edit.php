<?php

declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\AuthorizationInterface;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;

/**
 * Edits feedback in the admin panel
 */
class Edit implements ActionInterface {

    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';
    const REQUEST_FIELD_NAME = 'feedback_id';

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var FeedbackRepositoryInterface
     */
    private FeedbackRepositoryInterface $feedbackRepository;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

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
     * 
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param AuthorizationInterface $authorization
     */
    public function __construct(           
            ManagerInterface $messageManager,
            ResultFactory $resultFactory,
            FeedbackRepositoryInterface $feedbackRepository,
            RequestInterface $request,
            LoggerInterface $logger,
            AuthorizationInterface $authorization
    ) {        
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->request = $request;
        $this->logger = $logger;
        $this->authorization = $authorization;
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute() {
        
        // Check if the admin user has the required permission
        if (!$this->authorization->isAllowed(self::ADMIN_RESOURCE)) {
            $this->messageManager->addErrorMessage(__('You are not authorized to edit feedbacks.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                            ->setUrl($this->urlBuilder->getUrl('*/*/'));
        }
        
        $feedbackId = (int)($this->request->get(self::REQUEST_FIELD_NAME));        
        if (!$this->feedbackExists($feedbackId)) {
            $this->messageManager->addErrorMessage(__('This feedback does not exist.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/');
        }
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Feedback'));
        return $resultPage;
    }

    /**
     * @param int $feedbackId
     * @return bool
     */
    private function feedbackExists(int $feedbackId): bool {
        try {
            $this->feedbackRepository->getById($feedbackId);
            return true;
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getLogMessage());
            return false;
        }
    }
}
