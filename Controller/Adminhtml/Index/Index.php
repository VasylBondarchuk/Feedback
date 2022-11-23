<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Training\Feedback\Model\Feedback;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

/**
 * Index page
 */
class Index implements HttpGetActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_view';

    /**
     * @var ResultFactory
     */
    private ResultFactory $resultFactory;

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var FeedbackRepositoryInterface
     */
    private FeedbackRepositoryInterface $feedbackRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @param ResultFactory $resultFactory
     * @param DataPersistorInterface $dataPersistor
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ResultFactory $resultFactory,
        DataPersistorInterface    $dataPersistor,
        FeedbackRepositoryInterface $feedbackRepository,
        SearchCriteriaBuilder     $searchCriteriaBuilder,
        ManagerInterface $messageManager
    ) {
        $this->resultFactory = $resultFactory;
        $this->dataPersistor = $dataPersistor;
        $this->feedbackRepository = $feedbackRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->messageManager = $messageManager;
    }

    /**
     * @return ResponseInterface|ResultInterface
     */
    public function execute()
    {
        $this->displayNotPublishedFeedbacksNumber();
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage
            ->setActiveMenu('Training_Feedback::feedback')
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
     *
     * @return int
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
