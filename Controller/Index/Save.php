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
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Rating\RatingRepositoryInterface;
use Training\Feedback\Helper\EmailNotifications\FeedbackEmailNotification;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Api\Data\Rating\RatingInterface;
use Training\Feedback\Model\FeedbackFactory;
use Training\Feedback\Model\RatingFactory;
use Training\Feedback\Model\ResourceModel\RatingOption\CollectionFactory as RatingOptionCollectionFactory;
use Magento\Backend\Model\UrlInterface;
use Psr\Log\LoggerInterface;
use Training\Feedback\Helper\Form;
use Magento\Framework\DB\Transaction;

class Save implements HttpPostActionInterface
{
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
    private LoggerInterface $logger;
    private Form $form;
    private Transaction $transaction;

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
        Form $form        
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
    }

    /**
     * 
     * @return type
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('*/*/index');
        if ($this->form->isFormSubmitted()) {
            $post = $this->form->getFormData();
            try {
                $this->form->validatePost($post);
                $this->saveFeedback($post);
                $this->sendNewFeedbackNotificationEmail($post['message']);
                $this->messageManager->addSuccessMessage(
                    __('Thank you for your feedback.')
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage(
                    __('An error occurred while processing your form. %1', $e->getMessage())
                );
                $this->logger->error($e->getMessage());
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
    private function saveFeedback(array $post): void
    {
        $feedback = $this->feedbackFactory->create();
        $this->populateFeedbackModel($feedback, $post);

        try {
            $this->feedbackRepository->save($feedback);
            $this->saveRating($feedback, $post);
        } catch (\Exception $e) {
            $this->logger->error('Could not save feedback or ratings: ' . $e->getMessage());
            throw new \Exception((string) __('An error occurred while saving feedback or ratings.'));
        }
    }

    /**
     * 
     * @param FeedbackInterface $feedback
     * @param array $post
     * @return void
     */
    private function saveRating(FeedbackInterface $feedback, array $post): void
    {
        foreach ($this->getRatingOptions() as $ratingOption) {
            $rating = $this->ratingFactory->create();
            $this->populateRatingModel($rating, $feedback, $ratingOption, $post);
            if ($rating->getRatingValue() !== null) {
                $this->ratingRepository->save($rating);
            }
        }
    }

    /**
     * 
     * @param FeedbackInterface $feedback
     * @param array $post
     * @return void
     */
    private function populateFeedbackModel(FeedbackInterface $feedback, array $post): void
    {
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
    private function populateRatingModel(RatingInterface $rating, FeedbackInterface $feedback, $ratingOption, array $post): void
    {
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
    }

    /**
     * 
     * @return string|null
     */
    private function publishFeedbackWithoutModeration(): ?string
    {
        return $this->scopeConfig->getValue(self::PUBLISH_FEEDBACK_PATH);
    }

    /**
     * 
     * @param string $message
     */
    private function sendNewFeedbackNotificationEmail(string $message)
    {
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
    private function getLinkToFeedbackEditPage(FeedbackModel $feedback): string
    {
        return $this->urlInterface->getRouteUrl(self::FEEDBACK_EDIT_PAGE_PATH, [
            'feedback_id' => $feedback->getFeedbackId(),
            'key' => $this->urlInterface->getSecretKey('training_feedback', 'index', 'edit')
        ]);
    }

    /**
     * 
     * @return type
     */
    private function getRatingOptions()
    {
        $collection = $this->ratingOptionCollectionFactory->create();
        return $collection->getItems();
    }
}
