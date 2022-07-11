<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Psr\Log\LoggerInterface;
use Training\Feedback\Model\Feedback;

/**
 *
 */
class Edit extends Action
{
    /**
     *
     */
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var Feedback RepositoryInterface
     */
    private $feedbackRepository;
    /**
     *@v ar LoggerInterface
     */
    private $logger;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                   $context,
        PageFactory               $resultPageFactory,
        FeedbackRepositoryInterface $feedbackRepository,
        LoggerInterface           $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->feedbackRepository = $feedbackRepository;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @throws LocalizedException
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('feedback_id');

        if (!$this->isIdExist($id)) {
            $this->messageManager->addErrorMessage(__('This feedback does not exist.'));
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
            ->addBreadcrumb(__('Feedbacks'), __('Feedbacks'))
            ->addBreadcrumb(
                $id ? __('Edit Feedback') : __('New Feedback'),
                $id ? __('Edit Feedback') : __('New Feedback')
            )
            ->getConfig()->getTitle()->prepend(__('Edit Feedback'));
        return $resultPage;
    }
    /**
     * @param $id
     * @return bool
     * @throws LocalizedException
     */
    private function isIdExist($id): bool
    {
        $exist = false;
        if (is_int($id) && $id > 0) {
            try {
                $this->feedbackRepository->getById($id);
                $exist = true;
            } catch (NoSuchEntityException $e) {
                $this->logger->error($e->getLogMessage());
            }
        }
        return $exist;
    }

    private function redirectToIndexPage() : Redirect
    {
        $this->messageManager->addErrorMessage(__('This feedback does not exist.'));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
