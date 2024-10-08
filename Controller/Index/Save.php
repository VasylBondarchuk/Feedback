<?php

declare(strict_types=1);

namespace Training\Feedback\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\Model\UrlInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Rating\RatingRepositoryInterface;
use Training\Feedback\Helper\EmailNotifications\FeedbackEmailNotification;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Api\Data\Rating\RatingInterface;
use Training\Feedback\Model\FeedbackFactory;
use Training\Feedback\Model\RatingFactory;
use Training\Feedback\Model\ResourceModel\RatingOption\CollectionFactory as RatingOptionCollectionFactory;

use Psr\Log\LoggerInterface;
use Training\Feedback\Helper\Form;

class Save implements HttpPostActionInterface {

    private const FEEDBACK_EDIT_PAGE_PATH = 'training_feedback/index/edit/feedback_id/';
    private const PUBLISH_FEEDBACK_PATH = 'feedback_configuration/feedback_configuration_general/publish_feedback_without_moderation';

    private ManagerInterface $messageManager;
    private ResultFactory $resultFactory;
    private FeedbackFactory $feedbackFactory;
    private RatingFactory $ratingFactory;
    private RequestInterface $request;
    private FeedbackEmailNotification $email;
    private UrlInterface $urlInterface;
    protected ScopeConfigInterface $scopeConfig;
    private FeedbackRepositoryInterface $feedbackRepository;
    private RatingRepositoryInterface $ratingRepository;
    private RatingOptionCollectionFactory $ratingOptionCollectionFactory;
    private Session $customerSession;
    private StoreManagerInterface $storeManager;
    /**
     * 
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * 
     * @var Form
     */
    private Form $form;
    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    public function __construct(
            ManagerInterface $messageManager,
            ResultFactory $resultFactory,
            RequestInterface $request,
            FeedbackFactory $feedbackFactory,
            RatingFactory $ratingFactory,
            FeedbackEmailNotification $email,
            UrlInterface $urlInterface,
            ScopeConfigInterface $scopeConfig,
            FeedbackRepositoryInterface $feedbackRepository,
            RatingRepositoryInterface $ratingRepository,
            RatingOptionCollectionFactory $ratingOptionCollectionFactory,
            Session $customerSession,
            StoreManagerInterface $storeManager,
            LoggerInterface $logger,
            Form $form,
            DataPersistorInterface $dataPersistor,
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->request = $request;
        $this->feedbackFactory = $feedbackFactory;
        $this->ratingFactory = $ratingFactory;
        $this->email = $email;
        $this->urlInterface = $urlInterface;
        $this->scopeConfig = $scopeConfig;
        $this->feedbackRepository = $feedbackRepository;
        $this->ratingRepository = $ratingRepository;
        $this->ratingOptionCollectionFactory = $ratingOptionCollectionFactory;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->form = $form;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * 
     * @return type
     */
    public function execute() {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        if ($this->form->isFormSubmitted()) {
            $post = $this->form->getFormData();                      
            try {
                $this->form->validateFeedbackPost($post);
                $this->saveFeedback($post);
                $this->sendNewFeedbackNotificationEmail($this->getFeedbackMessage());
                $this->messageManager->addSuccessMessage(
                        __('Thank you for your feedback.')
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                        __('An error occurred while processing your form. %1', $e->getMessage())
                );
                $this->logger->error($e->getMessage());
                $this->dataPersistor->set('training_feedback', $post);
                $resultRedirect->setPath('*/*/form');
            }
        }
        return $resultRedirect;
    }

    /**
     * 
     * @param array $post
     * @return void
     * @throws \Exception
     */
    private function saveFeedback(array $post): void {
        $feedback = $this->feedbackFactory->create();
        $this->populateFeedbackModel($feedback, $post);
        try {
            $this->feedbackRepository->save($feedback);
            $this->saveRatings($feedback, $post);
        } catch (\Exception $e) {
            $this->logger->error('Could not save feedback or ratings: ' . $e->getMessage());
            throw new \Exception((string) __('An error occurred while saving feedback or ratings.'));
        }
    }
    
    
    private function saveRatings($feedback, array $post): void {        
        if (isset($post['ratings']) && is_array($post['ratings'])) {
            foreach ($post['ratings'] as $ratingOptionId => $ratingValue) {
                // Save the rating value for each option            
                $this->saveRating($feedback, (int)$ratingOptionId, (int)$ratingValue);
            }
        }
    }

    private function saveRating($feedback, int $ratingOptionId, int $ratingValue): void {        
        $rating = $this->ratingFactory->create();
        $rating->setFeedbackId($feedback->getFeedbackId());
        $rating->setRatingOptionId($ratingOptionId);
        $rating->setRatingValue($ratingValue);        
        $this->ratingRepository->save($rating);
    }  
    
     
    /**
     * 
     * @param FeedbackInterface $feedback
     * @param array $post
     * @return void
     */
    private function populateFeedbackModel(FeedbackInterface $feedback, array $post): void {
        $feedback->setData($post)
                ->setIsActive($this->publishFeedbackWithoutModeration())
                ->setStoreId((int) $this->storeManager->getStore()->getId());

        if (!isset($post['reply_notification'])) {
            $feedback->setReplyNotification(0);
        }

        if ($this->customerSession->isLoggedIn()) {
            $feedback->setCustomerId((int) $this->customerSession->getCustomerId());
        }
    }

    /**
     * 
     * @param RatingInterface $rating
     * @param FeedbackInterface $feedback
     * @param type $ratingOption
     * @param array $post
     * @return void
     */
    private function populateRatingModel(RatingInterface $rating, FeedbackInterface $feedback, $ratingOption, array $post) {
        $currentDate = (new \DateTime())->format('Y-m-d H:i:s');
        $feedbackId = $feedback->getFeedbackId();
        $ratingOptionId = $ratingOption->getRatingOptionId();
        $ratingKey = 'rating_' . $ratingOptionId;

        if (isset($post[$ratingKey])) {
            $ratingValue = (int) $post[$ratingKey];
            $rating->setFeedbackId($feedbackId)
                    ->setRatingOptionId($ratingOptionId)
                    ->setRatingValue($ratingValue)
                    ->setCreatedAt($currentDate);
        }
        return $rating;
    }

    /**
     * 
     * @return string|null
     */
    private function publishFeedbackWithoutModeration(): ?string {
        return $this->scopeConfig->getValue(self::PUBLISH_FEEDBACK_PATH);
    }

    /**
     * 
     * @param string $message
     */
    private function sendNewFeedbackNotificationEmail(string $message) {
        $feedback = $this->feedbackFactory->create();
        if ($this->email->getNotificationRecipientEmail() && $this->email->getNotificationRecipientName()) {
            $this->email->sendEmail(
                    $this->email->getNotificationRecipientEmail(),
                    [$this->email->getNotificationRecipientName(), $message, $this->getLinkToFeedbackEditPage($feedback)]
            );
        }
    }

    /**
     * 
     * @param FeedbackModel $feedback
     * @return string
     */
    private function getLinkToFeedbackEditPage(FeedbackModel $feedback): string {
        return $this->urlInterface->getRouteUrl(self::FEEDBACK_EDIT_PAGE_PATH, [
                    'feedback_id' => $feedback->getFeedbackId(),
                    'key' => $this->urlInterface->getSecretKey('training_feedback', 'index', 'edit')
        ]);
    }

    /**
     * 
     * @return type
     */
    private function getRatingOptions() {
        $collection = $this->ratingOptionCollectionFactory->create();
        return $collection->getItems();
    }

    /**
     * 
     * @return string
     */
    private function getFeedbackMessage(): string {
        return $this->form->getFormData()[Form::FEEDBACK_MESSAGE] ?? '';
    }
}
