<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Reply\ReplyInterface;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Training\Feedback\Helper\EmailNotifications\ReplyEmailNotification;
use Training\Feedback\Model\Feedback;
use Training\Feedback\Model\FeedbackFactory;
use Training\Feedback\Model\Reply;
use Training\Feedback\Model\ReplyFactory;
use Training\Feedback\Helper\Form;

/**
 * Saves feedbacks
 */
class Save extends Action implements HttpGetActionInterface {

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
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param DataPersistorInterface $postPersistor
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param FeedbackFactory $feedbackFactory
     * @param ReplyRepositoryInterface $replyRepository
     * @param ReplyFactory $replyFactory
     * @param Session $authSession
     * @param LoggerInterface $logger
     * @param RequestInterface $request
     * @param ReplyEmailNotification $email
     */
    public function __construct(
            Context $context,
            ManagerInterface $messageManager,
            ResultFactory $resultFactory,
            DataPersistorInterface $dataPersistor,
            FeedbackInterface $feedback,
            ReplyInterface $reply,
            FeedbackRepositoryInterface $feedbackRepository,
            FeedbackFactory $feedbackFactory,
            ReplyRepositoryInterface $replyRepository,
            ReplyFactory $replyFactory,
            Session $authSession,
            LoggerInterface $logger,
            RequestInterface $request,
            ReplyEmailNotification $email,
            Form $form
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
        $this->authSession = $authSession;
        $this->logger = $logger;
        $this->request = $request;
        $this->email = $email;
        $this->form = $form;
        parent::__construct($context);
    }

    /**
     * @return Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute() {
        if ($this->form->isFormSubmitted()) {
            $post = $this->form->getFormData();
            //print_r($post);exit;
            try {
                $this->form->validateFeedbackPost($post);
                $this->saveFeedback($post);
                $this->saveReply($post);
                $this->sendNotificationEmail();

                $this->messageManager->addSuccessMessage(__('You saved the feedback.'));
                $this->dataPersistor->clear('training_feedback');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                        __('An error occurred while saving the feedback. %1', $e->getMessage()));
                $this->logger->error($e->getLogMessage());
            }
            $this->dataPersistor->set('training_feedback', $post);
                        
            return $this->redirect($post);
        }
    }

    /**
     * @param int $editedFeedbackId
     * @return Feedback
     * @throws LocalizedException
     */
    private function getFeedbackModel(): FeedbackInterface {
        $editedFeedbackId = $this->getEditedFeedbackId();
        if (!$this->feedback) {
            $this->feedback = $editedFeedbackId
                    ? $this->feedbackRepository->getById($editedFeedbackId)
                    : $this->feedbackFactory->create();
        }
        return $this->feedback;
    }

    /**
     * @param int $editedFeedbackId
     * @return Reply
     */
    private function getReplyModel(): ReplyInterface {
        $editedFeedbackId = $this->getEditedFeedbackId();
        if (!$this->reply) {
            $this->freply = $this->replyRepository->isReplyExist($editedFeedbackId)
                    ? $this->replyRepository->getByFeedbackId($editedFeedbackId)
                    : $this->replyFactory->create();
        }
        return $this->reply;
    }

    /**
     * @param FeedbackInterface $feedbackModel
     * @param array $post
     * @return void
     */
    private function saveFeedback(array $post): void {
        $feedbackModel = $this->getFeedbackModel();
        if (empty($post[FeedbackInterface::FEEDBACK_ID])) {
            $post[FeedbackInterface::FEEDBACK_ID] = null;
        }
        try {
            $feedbackModel->setData($post);
            $this->feedbackRepository->save($feedbackModel);
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage());
        }
    }

    /**
     * 
     * @param FeedbackInterface $feedbackModel
     * @param ReplyInterface $replyModel
     * @param array $post
     */
    private function saveReply(array $post) {
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
                // If reply was deleted
            } else {
                $editedFeedbackId = (int)($this->request->get(FeedbackInterface::FEEDBACK_ID));
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
                    [$feedbackModel->getAuthorName(),$replyModel->getReplyText()]
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

    private function getEditedFeedbackId(): int {
        return (int) ($this->request->get(FeedbackInterface::FEEDBACK_ID));
    }
}
