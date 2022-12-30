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
use Training\Feedback\Model\ResourceModel\Reply\Collection as ReplyCollection;
use Training\Feedback\Api\Data\FeedbackInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

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

    protected CustomerRepositoryInterface $customerRepositoryInterface;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Timezone $timezone
     * @param FeedbackResource $feedbackResource
     * @param ReplyRepository $replyRepository
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     * @param array $data
     */
    public function __construct(
        Context           $context,
        CollectionFactory $collectionFactory,
        Timezone          $timezone,
        FeedbackResource  $feedbackResource,
        ReplyRepository   $replyRepository,
        LoggerInterface   $logger,
        CustomerRepositoryInterface $customerRepositoryInterface,
        array             $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
        $this->feedbackResource = $feedbackResource;
        $this->replyRepository = $replyRepository;
        $this->logger = $logger;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
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
        return $this->collectionFactory->create()
            ->addFieldToFilter(FeedbackInterface::IS_ACTIVE, 1)
            ->setOrder(FeedbackInterface::CREATION_TIME, 'DESC');
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
            $replyAuthor = $this->customerRepositoryInterface->getById($reply->getAdminId());
            $replyAuthorName = $replyAuthor->getFirstname() . ' ' . $replyAuthor->getLastname();
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage());
        }
        return $replyAuthorName;
    }
}
