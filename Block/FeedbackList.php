<?php

namespace Training\Feedback\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Psr\Log\LoggerInterface;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Model\Reply as ReplyModel;
use Training\Feedback\Model\ReplyRepository;
use Training\Feedback\Model\ResourceModel\Feedback as FeedbackResource;
use Training\Feedback\Model\ResourceModel\Feedback\Collection;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Magento\User\Model\UserFactory;
use \Training\Feedback\Model\ResourceModel\Reply\Collection as ReplyCollection;

/**
 *
 */
class FeedbackList extends Template
{

    private const ADD_FEEDBACK_FORM_PATH = 'training_feedback/index/form';

    private const DEFAULT_ADMIN_NAME = 'Admin';
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;
    /**
     * @var
     */
    private Collection $collection;
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

    /**
     * @var UserFactory
     */
    protected UserFactory $userFactory;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Timezone $timezone
     * @param FeedbackResource $feedbackResource
     * @param ReplyRepository $replyRepository
     * @param LoggerInterface $logger
     * @param UserFactory $userFactory
     * @param array $data
     */
    public function __construct(
        Context           $context,
        CollectionFactory $collectionFactory,
        Timezone          $timezone,
        FeedbackResource  $feedbackResource,
        ReplyRepository   $replyRepository,
        LoggerInterface   $logger,
        UserFactory $userFactory,
        array             $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
        $this->feedbackResource = $feedbackResource;
        $this->replyRepository = $replyRepository;
        $this->logger = $logger;
        $this->userFactory = $userFactory;
    }

    /**
     * Gets only active feedbacks
     *
     * @return Collection
     */
    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        $this->collection = $this->collectionFactory->create();
        $this->collection->addFieldToFilter('is_active', 1);
        $this->collection->setOrder('creation_time', 'DESC');
        return $this->collection;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()
            ->createBlock(CustomPager::class,'feedback.list.pager')
            ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        return $this;
    }

    /**
     * Get Pager child block output
     *
     * @return string
     */
    public function getPagerHtml(): string
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @return string
     */
    public function getAddFeedbackUrl(): string
    {
        return $this->getUrl(self::ADD_FEEDBACK_FORM_PATH);
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
        $replyAuthorName = self::DEFAULT_ADMIN_NAME;
        try {
            $replyAuthorId =  $reply->getAdminId();
            $replyAuthorName = $this->userFactory->create()->load($replyAuthorId)->getName();
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage());
        }
        return $replyAuthorName;
    }
}
