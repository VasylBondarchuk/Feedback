<?php

declare(strict_types=1);

namespace Training\Feedback\ViewModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\User\Model\ResourceModel\User as UserResourceModel;
use Magento\User\Model\UserFactory;
use Psr\Log\LoggerInterface;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Model\Reply as ReplyModel;
use Training\Feedback\Model\ReplyRepository;
use Training\Feedback\Model\ResourceModel\Feedback as FeedbackResource;
use Training\Feedback\Model\ResourceModel\Reply\Collection as ReplyCollection;

/**
 *
 */
class FeedbackList implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;
    private const ADD_FEEDBACK_FORM_PATH = 'training_feedback/index/form';
    private const DEFAULT_ADMIN_NAME = 'Admin';

    /**
     * @var Timezone
     */
    private Timezone $timezone;

    /**
     * @var FeedbackResource
     */
    private FeedbackResource $feedbackResource;

    /**
     * @var ReplyRepository
     */
    private ReplyRepository $replyRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    protected UserFactory $userFactory;

    protected UserResourceModel $resourceModel;

    /**
     * @param UrlInterface $urlBuilder
     * @param Timezone $timezone
     * @param FeedbackResource $feedbackResource
     * @param ReplyRepository $replyRepository
     * @param LoggerInterface $logger
     * @param UserFactory $userFactory
     * @param UserResourceModel $resourceModel
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Timezone          $timezone,
        FeedbackResource  $feedbackResource,
        ReplyRepository   $replyRepository,
        LoggerInterface   $logger,
        UserFactory $userFactory,
        UserResourceModel $resourceModel,
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->timezone = $timezone;
        $this->feedbackResource = $feedbackResource;
        $this->replyRepository = $replyRepository;
        $this->logger = $logger;
        $this->userFactory = $userFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @return string
     */
    public function getAddFeedbackUrl(): string
    {
        return $this->urlBuilder->getUrl(self::ADD_FEEDBACK_FORM_PATH);
    }

    /**
     * @param FeedbackModel $feedback
     * @return string
     */
    public function getFeedbackDate(FeedbackModel $feedback) : string
    {
        return $this->timezone->formatDateTime($feedback->getCreationTime());
    }

    /**
     * @return string
     */
    public function getAllFeedbackNumber(): string
    {
        return $this->feedbackResource->getAllFeedbackNumber();
    }

    /**
     * @return string
     */
    public function getActiveFeedbackNumber(): string
    {
        return $this->feedbackResource->getActiveFeedbackNumber();
    }

    /**
     * @param int $feedbackId
     * @return ReplyCollection
     */
    public function getRepliesByFeedbackId(int $feedbackId): ReplyCollection
    {
        return $this->replyRepository->getRepliesByFeedbackId($feedbackId);
    }

    /**
     * @param ReplyModel $reply
     * @return string
     */
    public function getReplyAuthorName(ReplyModel $reply): string
    {
        try {
            $user = $this->userFactory->create();
            $this->resourceModel->load($user, $reply->getAdminId());
            $replyAuthorName = $user->getName();
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage());
        }
        return $replyAuthorName === ' ' ? self::DEFAULT_ADMIN_NAME : $replyAuthorName;
    }
}
