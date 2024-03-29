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
     * @param ManagerInterface $messageManager
     * @param ResultFactory $resultFactory
     * @param DataPersistorInterface $dataPersistor
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
            FeedbackRepositoryInterface $feedbackRepository,
            FeedbackFactory $feedbackFactory,
            ReplyRepositoryInterface $replyRepository,
            ReplyFactory $replyFactory,
            Session $authSession,
            LoggerInterface $logger,
            RequestInterface $request,
            ReplyEmailNotification $email
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->dataPersistor = $dataPersistor;
        $this->feedbackRepository = $feedbackRepository;
        $this->feedbackFactory = $feedbackFactory;
        $this->replyRepository = $replyRepository;
        $this->replyFactory = $replyFactory;
        $this->authSession = $authSession;
        $this->logger = $logger;
        $this->request = $request;
        $this->email = $email;
        parent::__construct($context);
    }

    /**
     * @return Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute() {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        // get data from the feedback form field
        $data = $this->request->getPostValue();
        
        if ($data) {
            if (isset($data[FeedbackInterface::IS_ACTIVE]) && $data[FeedbackInterface::IS_ACTIVE] === 'true') {
                $data[FeedbackInterface::IS_ACTIVE] = Feedback::STATUS_ACTIVE_VALUE;
            }
            if (empty($data[FeedbackInterface::FEEDBACK_ID])) {
                $data[FeedbackInterface::FEEDBACK_ID] = null;
            }
            $editedFeedbackId = (int) ($this->request->get(FeedbackInterface::FEEDBACK_ID));
            try {
                $feedbackModel = $this->getFeedBackModel($editedFeedbackId);
                $replyModel = $this->getReplyModel($editedFeedbackId);
            } catch (\Exception $e) {
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $this->saveFeedback($feedbackModel, $data);
                if (!empty($data['reply_text'])) {
                    $this->saveReply($replyModel, $feedbackModel, $data);
                } else {
                    $this->replyRepository->deleteByFeedbackId($editedFeedbackId);
                    $feedbackModel->setIsReplied($this->replyRepository->isReplied($editedFeedbackId));
                    $this->feedbackRepository->save($feedbackModel);
                }
                $this->email->sendEmail(
                        $feedbackModel->getAuthorEmail(),
                        [$feedbackModel->getAuthorName(),
                        $replyModel->getReplyText()]
                        );

                $this->messageManager->addSuccessMessage(__('You saved the feedback.'));
                $this->dataPersistor->clear('training_feedback');

                return $this->processRedirect($feedbackModel, $data, $resultRedirect);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->messageManager
                        ->addExceptionMessage($e, __('Something went wrong while saving the feedback.'));
            }

            $this->dataPersistor->set('training_feedback', $data);

            return $resultRedirect->setPath(
                            '*/*/edit',
                            ['feedback_id' => $editedFeedbackId]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param int $editedFeedbackId
     * @return Feedback
     * @throws LocalizedException
     */
    private function getFeedBackModel(int $editedFeedbackId): FeedbackInterface {
        return $editedFeedbackId ? $this->feedbackRepository->getById($editedFeedbackId) : $this->feedbackFactory->create();
    }

    /**
     * @param int $editedFeedbackId
     * @return Reply
     */
    private function getReplyModel(int $editedFeedbackId): ReplyInterface {
        return $this->replyRepository->isReplyExist($editedFeedbackId) ? $this->replyRepository->getByFeedbackId($editedFeedbackId) : $this->replyFactory->create();
    }

    /**
     * @param FeedbackInterface $feedbackModel
     * @param array $data
     * @return void
     */
    private function saveFeedback(FeedbackInterface $feedbackModel, array $data): void {
        try {
            $feedbackModel->setData($data);           
            $this->feedbackRepository->save($feedbackModel);
        } catch (LocalizedException $exception) {
            $this->logger->error($exception->getLogMessage());
        }
    }

    /**
     * @param Reply $replyModel
     * @param Feedback $feedbackModel
     * @param array $data
     * @return void
     * @throws LocalizedException
     */
    private function saveReply(ReplyInterface $replyModel, FeedbackInterface $feedbackModel, array $data) {
        $feedBackId = $feedbackModel->getFeedbackId();

        $replyModel
                ->setFeedbackId($feedBackId)
                ->setAdminId($this->getAdminId())
                ->setReplyText($data[ReplyInterface::REPLY_TEXT])
                ->setReplyCreationTime(date("F j, Y, g:i a"));
        $this->replyRepository->save($replyModel);
        $feedbackModel->setIsReplied($this->replyRepository->isReplied($feedBackId));
        $this->feedbackRepository->save($feedbackModel);
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
     * @param $data
     * @param $resultRedirect
     * @return mixed
     */
    private function processRedirect($model, $data, $resultRedirect): mixed {
        $redirect = $data['back'] ?? 'close';
        if ($redirect === 'continue') {
            $resultRedirect->setPath('*/*/edit', ['feedback_id' => $model->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect;
    }
}
