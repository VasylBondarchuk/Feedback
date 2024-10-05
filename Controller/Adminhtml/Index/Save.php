<?php

declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\AuthorizationInterface;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Training\Feedback\Api\Data\Rating\RatingInterface;
use Training\Feedback\Helper\EmailNotifications\ReplyEmailNotification;
use Training\Feedback\Model\Feedback;
use Training\Feedback\Model\FeedbackFactory;
use Training\Feedback\Model\Reply;
use Training\Feedback\Model\ReplyFactory;
use Training\Feedback\Model\RatingFactory;
use Training\Feedback\Api\Data\Rating\RatingRepositoryInterface;
use Training\Feedback\Helper\Form;
use Training\Feedback\Api\Data\RatingOption\RatingOptionRepositoryInterface;
use Training\Feedback\Helper\Stores;

/**
 * Saves feedbacks
 */
class Save implements ActionInterface {

    /**
     *
     */
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var FeedbackInterface
     */
    private FeedbackInterface $feedback;

    /**
     * @var ReplyRepositoryInterface
     */
    private ReplyInterface $reply;

    /**
     * @var FeedbackRepositoryInterface
     */
    private FeedbackRepositoryInterface $feedbackRepository;

    /**
     * @var FeedbackFactory
     */
    private FeedbackFactory $feedbackFactory;

    /**
     * @var ReplyRepositoryInterface
     */
    private ReplyRepositoryInterface $replyRepository;

    /**
     * @var ReplyFactory
     */
    private ReplyFactory $replyFactory;

    /**
     * 
     * @var RatingFactory
     */
    private RatingFactory $ratingFactory;

    /**
     * 
     * @var RatingRepositoryInterface
     */
    private RatingRepositoryInterface $ratingRepository;

    /**
     * @var Session
     */
    private Session $authSession;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @var ReplyEmailNotification
     */
    private ReplyEmailNotification $email;

    /**
     * 
     * @var Form
     */
    private Form $form;

    
    /**
     * 
     * @var Stores
     */
    private Stores $storeManager;

    /**
     * 
     * @var RatingOptionRepositoryInterface
     */
    private RatingOptionRepositoryInterface $ratingOptionRepository;

    /**
     * 
     * @var AuthorizationInterface
     */
    private AuthorizationInterface $authorization;
    
    
    public function __construct(
            ManagerInterface $messageManager,
            ResultFactory $resultFactory,
            DataPersistorInterface $dataPersistor,
            FeedbackInterface $feedback,
            ReplyInterface $reply,
            FeedbackRepositoryInterface $feedbackRepository,
            FeedbackFactory $feedbackFactory,
            ReplyRepositoryInterface $replyRepository,
            ReplyFactory $replyFactory,
            RatingFactory $ratingFactory,
            RatingRepositoryInterface $ratingRepository,
            Session $authSession,
            LoggerInterface $logger,
            RequestInterface $request,
            ReplyEmailNotification $email,
            Form $form,
            Stores $storeManager,
            RatingOptionRepositoryInterface $ratingOptionRepository,
            AuthorizationInterface $authorization
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->dataPersistor = $dataPersistor;
        $this->feedback = $feedback;
        $this->reply = $reply;
        $this->feedbackRepository = $feedbackRepository;
        $this->feedbackFactory = $feedbackFactory;
        $this->replyRepository = $replyRepository;
        $this->replyFactory = $replyFactory;
        $this->ratingFactory = $ratingFactory;
        $this->ratingRepository = $ratingRepository;
        $this->authSession = $authSession;
        $this->logger = $logger;
        $this->request = $request;
        $this->email = $email;
        $this->form = $form;
        $this->storeManager = $storeManager;
        $this->ratingOptionRepository = $ratingOptionRepository;
        $this->authorization = $authorization;
    }

    /**
     * @return Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute(): ResultInterface {
        
        if (!$this->authorization->isAllowed(self::ADMIN_RESOURCE)) {
            $this->messageManager->addErrorMessage(__('You are not authorized to save feedbacks.'));
            return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)
                            ->setUrl($this->urlBuilder->getUrl('*/*/'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($this->form->isFormSubmitted()) {
            $post = $this->form->getFormData();
            //print_r($post);exit;
            try {
                $this->form->validateFeedbackPost($post);
                foreach ($this->storeManager->getStoreIds() as $storeId) {
                    $this->saveFeedback($post, $storeId);
                    $this->saveRatings($post, $storeId);
                    $this->saveReply($post);
                    $this->sendNotificationEmail();
                }
                $this->messageManager->addSuccessMessage(__('You saved the feedback.'));
                $this->dataPersistor->clear('training_feedback');                
                $resultRedirect->setPath('*/*/*');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                        __('An error occurred while saving the feedback. %1', $e->getMessage())
                );
                $this->logger->error($e->getMessage());
                // Save input data for re-population on the form
                $this->dataPersistor->set('training_feedback', $post);
                // Redirect back to the referrer page
                return $resultRedirect->setUrl($this->request->getServer('HTTP_REFERER'));
            }
        } else {
            $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }

    /**
     * @param int $editedFeedbackId
     * @return Feedback
     * @throws LocalizedException
     */
    private function getFeedbackModel(): FeedbackInterface {
        $editedFeedbackId = $this->getEditedFeedbackId();
        if (!$this->feedback) {
            $this->feedback = $editedFeedbackId ? $this->feedbackRepository->getById($editedFeedbackId) : $this->feedbackFactory->create();
        }
        return $this->feedback;
    }

    /**
     * @param int $editedFeedbackId
     * @return Reply
     */
    private function getReplyModel(): ReplyInterface {
        $editedFeedbackId = $this->getEditedFeedbackId();
        $this->reply = $this->replyRepository->isReplyExist($editedFeedbackId) ? $this->replyRepository->getByFeedbackId($editedFeedbackId) : $this->replyFactory->create();
        return $this->reply;
    }

    /**
     * 
     * @param type $feedbackId
     * @param type $ratingOptionId
     * @return RatingInterface
     */
    private function getRatingModel($feedbackId, $ratingOptionId): RatingInterface {
        $editedFeedbackId = $this->getEditedFeedbackId();
        $this->rating = $editedFeedbackId ? $this->ratingRepository->getRatingByFeedbackIdRatingOptionId($feedbackId, $ratingOptionId) : $this->ratingFactory->create();
        return $this->rating;
    }

    /**
     * 
     * @param array $post
     * @param type $storId
     * @return void
     */
    private function saveFeedback(array $post, $storId): void {
        $feedbackModel = $this->getFeedbackModel();
        if (empty($post[FeedbackInterface::FEEDBACK_ID])) {
            $post[FeedbackInterface::FEEDBACK_ID] = null;
        }
        try {
            $feedbackModel->setData($post);
            $feedbackModel->setStoreId($storId);
            $this->feedbackRepository->save($feedbackModel);
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage());
        }
    }

    /**
     * 
     * @param array $post
     * @return void
     */
    private function saveRatings(array $post, int $storId): void {
        if (isset($post['ratings']) && is_array($post['ratings'])) {
            foreach ($post['ratings'] as $ratingOptionId => $ratingValue) {
                if ($this->isRatingOptionAllowedForStore($ratingOptionId, $storId)) {// Save the rating value for each option  }          
                    $this->saveRating((int) $ratingOptionId, (int) $ratingValue);
                }
            }
        }
    }

    /**
     * 
     * @param int $ratingOptionId
     * @param int $ratingValue
     * @return void
     */
    private function saveRating(int $ratingOptionId, int $ratingValue): void {
        $feedbackId = $this->getFeedbackModel()->getFeedbackId();
        $rating = $this->getRatingModel($feedbackId, $ratingOptionId);
        $rating->setFeedbackId($feedbackId);
        $rating->setRatingOptionId($ratingOptionId);
        $rating->setRatingValue($ratingValue);
        $this->ratingRepository->save($rating);
    }

    /**
     * 
     * @param FeedbackInterface $feedbackModel
     * @param ReplyInterface $replyModel
     * @param array $post
     */
    private function saveReply(array $post): void {
        try {
            $replyModel = $this->getReplyModel();
            $feedbackModel = $this->getFeedbackModel();
            if ($this->isReplySubmitted()) {
                $feedBackId = $feedbackModel->getFeedbackId();
                $replyModel
                        ->setFeedbackId($feedBackId)
                        ->setAdminId($this->getAdminId())
                        ->setReplyText($post[ReplyInterface::REPLY_TEXT])
                        ->setReplyCreationTime(date("F j, Y, g:i a"));
                $this->replyRepository->save($replyModel);
                $feedbackModel->setIsReplied($this->replyRepository->isReplied($feedBackId));
                $this->feedbackRepository->save($feedbackModel);
            } else {
                $editedFeedbackId = (int) ($this->request->get(FeedbackInterface::FEEDBACK_ID));
                $this->replyRepository->deleteByFeedbackId($editedFeedbackId);
                $feedbackModel->setIsReplied($this->replyRepository->isReplied($editedFeedbackId));
                $this->feedbackRepository->save($feedbackModel);
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                    __('An error occurred while saving the reply. %1', $e->getMessage()));
            $this->logger->error($e->getLogMessage());
        }
    }

    /**
     * 
     */
    private function sendNotificationEmail() {
        $replyModel = $this->getReplyModel();
        $feedbackModel = $this->getFeedbackModel();
        try {
            $this->email->sendEmail(
                    $feedbackModel->getAuthorEmail(),
                    [$feedbackModel->getAuthorName(), $replyModel->getReplyText()]
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(
                    __('An error occurred while sending notification email. %1', $e->getMessage())
            );
            $this->logger->error($e->getLogMessage());
        }
    }

    /**
     * 
     * @return bool
     */
    private function isReplySubmitted(): bool {
        $post = $this->request->getPostValue();
        return !empty($post[Form::FEEDBACK_REPLY_FIELD]);
    }

    /**
     * @return int
     */
    private function getAdminId(): int {
        return (int) $this->authSession
                        ->getUser()
                        ->getData('user_id');
    }

    /**
     * @param $model
     * @param $post
     * @param $resultRedirect
     * @return mixed
     */
    private function redirect($post): mixed {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $feedbackModel = $this->getFeedbackModel();
        $redirect = $post['back'] ?? 'close';
        if ($redirect === 'continue') {
            $resultRedirect->setPath('*/*/edit', ['feedback_id' => $feedbackModel->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect;
    }

    /**
     * 
     * @return int
     */
    private function getEditedFeedbackId(): int {
        return (int) ($this->request->get(FeedbackInterface::FEEDBACK_ID));
    }    

    /**
     * 
     * @param int $ratingOptionId
     * @param int $storeId
     * @return type
     */
    private function isRatingOptionAllowedForStore(int $ratingOptionId, int $storeId) {
        return $this->ratingOptionRepository->getById($ratingOptionId)->getStoreId() === $storeId;
    }
}
