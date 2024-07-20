<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Magento\Framework\Registry;

/**
 * Edits feedback in the admin panel
 */
class Edit extends Action implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';
    const REQUEST_FIELD_NAME = 'feedback_id';

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

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
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     *
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,    
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        FeedbackRepositoryInterface $feedbackRepository,
        RequestInterface $request,
        LoggerInterface  $logger,
        Registry $coreRegistry    
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->request = $request;
        $this->logger = $logger;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context); 
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $feedbackId = (int)($this->request->get(self::REQUEST_FIELD_NAME));
        if (!$this->isFeedbackExist($feedbackId)) {
            $this->messageManager->addErrorMessage(__('This feedback does not exist.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/');
        }
        $feedback = $this->feedbackRepository->getById($feedbackId);
        $this->coreRegistry->register('feedback_data', $feedback);
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
            ->getConfig()->getTitle()->prepend(__('Edit Feedback'));
        return $resultPage;
    }

    /**
     * @param $feedbackId
     * @return bool
     * @throws LocalizedException
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
