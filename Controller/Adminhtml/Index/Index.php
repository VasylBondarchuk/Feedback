<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Training\Feedback\Model\Feedback;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;

/**
 *
 */
class Index extends Action
{
    /**
     *
     */
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_view';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;


    /**
     * @var FeedbackRepositoryInterface
     */
    protected $feedbackRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param DataPersistorInterface $dataPersistor
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context                   $context,
        PageFactory               $resultPageFactory,
        DataPersistorInterface    $dataPersistor,
        FeedbackRepositoryInterface $feedbackRepository,
        SearchCriteriaBuilder     $searchCriteriaBuilder
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor;
        $this->feedbackRepository = $feedbackRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $this->displayNotPublishedFeedbacksNumber();
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
            ->addBreadcrumb(__('Feedbacks'), __('Feedbacks'))
            ->getConfig()->getTitle()->prepend(__('Feedback'));
        $this->dataPersistor->clear('training_feedback');
        return $resultPage;
    }

    /**
     * @return void
     */
    private function displayNotPublishedFeedbacksNumber()
    {
        if($this->getNotPublishedFeedbacksNumber()){
            $this->messageManager->addSuccessMessage(
                __('%1 Feedback(s) are submitted but not published yet.', $this->getNotPublishedFeedbacksNumber())
            );
        }
    }

    /**
     */
    private function getNotPublishedFeedbacksNumber(): int
    {
        $this->searchCriteriaBuilder->addFilter(
            FeedbackInterface::IS_ACTIVE,
            Feedback::STATUS_INACTIVE_VALUE,
            'like'
        );

        $criteria = $this->searchCriteriaBuilder->create();
        $feedbacks = $this->feedbackRepository->getList($criteria);
        return count($feedbacks->getItems());
    }
}