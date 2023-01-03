<?php

declare(strict_types=1);

namespace Training\Feedback\ViewModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\ResourceModel\User as UserResourceModel;
use Magento\User\Model\UserFactory;
use Psr\Log\LoggerInterface;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Model\Reply as ReplyModel;
use Training\Feedback\Model\ReplyRepository;
use Training\Feedback\Model\ResourceModel\Feedback as FeedbackResource;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Training\Feedback\Model\ResourceModel\Reply\Collection as ReplyCollection;

/**
 *
 */
class FeedbackList implements ArgumentInterface
{
    /**
     *
     */
    private const ADD_FEEDBACK_FORM_PATH = 'training_feedback/index/form';
    /**
     *
     */
    private const DEFAULT_ADMIN_NAME = 'Admin';

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;

    /**
     * @var Timezone
     */
    private Timezone $timezone;

    /**
     * @var ReplyRepository
     */
    private ReplyRepository $replyRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var UserFactory
     */
    protected UserFactory $userFactory;

    /**
     * @var UserResourceModel
     */
    protected UserResourceModel $resourceModel;

    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param UrlInterface $urlBuilder
     * @param Timezone $timezone
     * @param ReplyRepository $replyRepository
     * @param LoggerInterface $logger
     * @param UserFactory $userFactory
     * @param UserResourceModel $resourceModel
     * @param StoreManagerInterface $storeManager
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Timezone          $timezone,
        ReplyRepository   $replyRepository,
        LoggerInterface   $logger,
        UserFactory $userFactory,
        UserResourceModel $resourceModel,
        StoreManagerInterface $storeManager,
        CollectionFactory $collectionFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->timezone = $timezone;
        $this->replyRepository = $replyRepository;
        $this->logger = $logger;
        $this->userFactory = $userFactory;
        $this->resourceModel = $resourceModel;
        $this->storeManager = $storeManager;
        $this->collectionFactory = $collectionFactory;
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
     * @return int
     * @throws NoSuchEntityException
     */
    public function getAllFeedbackNumber(): int
    {
        return $this->collectionFactory->create()
            ->addFieldToFilter(FeedbackInterface::STORE_ID, $this->getStoreId())
            ->count();
    }

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getActiveFeedbackNumber(): int
    {
        return $this->collectionFactory->create()
            ->addFieldToFilter(FeedbackInterface::IS_ACTIVE, 1)
            ->addFieldToFilter(FeedbackInterface::STORE_ID, $this->getStoreId())
            ->count();
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

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId(): int
    {
        return (int)$this->storeManager->getStore()->getId();
    }
}
