<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Api\Data\Reply\ReplyInterface;
use Training\Feedback\Model\Feedback;
use Training\Feedback\Model\FeedbackFactory;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Training\Feedback\Model\Reply;
use Training\Feedback\Model\ReplyFactory;
use Magento\Backend\Model\Auth\Session;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Saves fedbacks
 */
class Save implements HttpPostActionInterface
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var FeedbackRepositoryInterface
     */
    private $feedbackRepository;

    /**
     * @var FeedbackFactory
     */
    private $feedbackFactory;

    /**
     * @var ReplyRepositoryInterface
     */
    private $replyRepository;

    /**
     * @var ReplyFactory
     */
    private $replyFactory;

    /**
     * @var Session
     */
    private $authSession;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RequestInterface
     */
    private $request;

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
     */
    public function __construct(
        ManagerInterface $messageManager,
        ResultFactory $resultFactory,
        DataPersistorInterface    $dataPersistor,
        FeedbackRepositoryInterface $feedbackRepository,
        FeedbackFactory           $feedbackFactory,
        ReplyRepositoryInterface  $replyRepository,
        ReplyFactory              $replyFactory,
        Session                   $authSession,
        LoggerInterface           $logger,
        RequestInterface          $request
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
    }

    /**
     * @return Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        // get data from the feddback form field
        $data = $this->request->getPostValue();
        if ($data) {
            if (isset($data[FeedbackInterface::IS_ACTIVE]) && $data[FeedbackInterface::IS_ACTIVE] === 'true') {
                $data[FeedbackInterface::IS_ACTIVE] = Feedback::STATUS_ACTIVE_VALUE;
            }
            if (empty($data[FeedbackInterface::FEEDBACK_ID])) {
                $data[FeedbackInterface::FEEDBACK_ID] = null;
            }

            $editedFeedbackId = (int)($this->request->get('feedback_id'));

            try {
                $feedbackModel = $this->getFeedBackModel($editedFeedbackId);
                $replyModel = $this->getReplyModel($editedFeedbackId);
            } catch (\Exception $e) {
                return $resultRedirect->setPath('*/*/');
            }

            try {
                $this->saveFeedback($feedbackModel, $data);
                $this->saveReply($replyModel, $feedbackModel, $data);

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
    private function getFeedBackModel(int $editedFeedbackId) : FeedbackInterface
    {
        return $editedFeedbackId
            ? $this->feedbackRepository->getById($editedFeedbackId)
            : $this->feedbackFactory->create();
    }

    /**
     * @param int $editedFeedbackId
     * @return Reply
     */
    private function getReplyModel(int $editedFeedbackId) : ReplyInterface
    {
        return $this->replyRepository->isReplyExist($editedFeedbackId)
            ? $this->replyRepository->getByFeedbackId($editedFeedbackId)
            : $this->replyFactory->create();
    }

    /**
     * @param FeedbackInterface $feedbackModel
     * @param array $data
     * @return void
     */
    private function saveFeedback(FeedbackInterface $feedbackModel, array $data): void
    {
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
    private function saveReply(ReplyInterface $replyModel, FeedbackInterface $feedbackModel, array $data)
    {
        $feedBackId = $feedbackModel->getFeedbackId();
        $replyModel
            ->setFeedbackId($feedBackId)
            ->setAdminId($this->getAdminId())
            ->setReplyText($data[ReplyInterface::REPLY_TEXT]);

        $this->replyRepository->save($replyModel);
    }

    /**
     * @return int
     */
    private function getAdminId(): int
    {
        return (int)$this->authSession
            ->getUser()
            ->getData('user_id');
    }

    /**
     * @param $model
     * @param $data
     * @param $resultRedirect
     * @return mixed
     */
    private function processRedirect($model, $data, $resultRedirect)
    {
        $redirect = $data['back'] ?? 'close';
        if ($redirect ==='continue') {
            $resultRedirect->setPath('*/*/edit', ['feedback_id' => $model->getId()]);
        } else if ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect;
    }
}
