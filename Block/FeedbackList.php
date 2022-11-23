<?php

namespace Training\Feedback\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Training\Feedback\Model\ResourceModel\Feedback as FeedbackResource;
use Training\Feedback\Model\Feedback as FeedbackModel;
use Training\Feedback\Model\ResourceModel\Feedback\Collection;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Training\Feedback\Model\ReplyRepository;
use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session;

/**
 *
 */
class FeedbackList extends Template
{
    const PAGE_SIZE = 5;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var
     */
    private $collection;
    /**
     * @var Timezone
     */
    private $timezone;

    /**
     * @var FeedbackResource
     */
    private $feedbackResource;

    /**
     * @var ReplyRepository
     */
    private $replyRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    protected $customerSession;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Timezone $timezone
     * @param FeedbackResource $feedbackResource
     * @param array $data
     */
    public function __construct(
        Context           $context,
        CollectionFactory $collectionFactory,
        Timezone          $timezone,
        FeedbackResource  $feedbackResource,
        ReplyRepository   $replyRepository,
        LoggerInterface   $logger,
        Session           $customerSession,
        array             $data = []
    )
    {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
        $this->feedbackResource = $feedbackResource;
        $this->replyRepository = $replyRepository;
        $this->logger = $logger;
        $this->customerSession = $customerSession;
    }

    /**
     * @return Collection
     */
    public function getCollection(): Collection
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
            $this->collection->addFieldToFilter('is_active', 1);
            $this->collection->setOrder('creation_time', 'DESC');
        }
        return $this->collection;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock(
            CustomPager::class,
            'feedback.list.pager'
        )->setCollection($this->getCollection());
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
        return $this->getUrl('training_feedback/index/form');
    }

    /**
     * @param $feedback
     * @return false|string
     */
    public function getFeedbackDate($feedback)
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
     * @param FeedbackModel $feedback
     * @return false|string
     */
    public function getReplyDate(FeedbackModel $feedback)
    {
        $replyDate = '';
        $feedbackId = $feedback->getFeedbackId();
        try {
            $replyDate =
                $this->timezone->formatDateTime(
                    $this->replyRepository->getByFeedbackId($feedbackId)->getReplyCreationTime()
                );
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage());
        }
        return $replyDate;
    }

    /**
     * @param FeedbackModel $feedback
     * @return string
     */
    public function getReplyText(FeedbackModel $feedback): ?string
    {
        $replyText = '';
        $feedbackId = $feedback->getFeedbackId();
        try {
            $replyText =  $this->replyRepository->getByFeedbackId($feedbackId)->getReplyText();
        } catch (LocalizedException $e) {
            $this->logger->error($e->getLogMessage());
        }
        return $replyText;
    }

    public function getReplyAuthorName(): string
    {
        return 'Admin';
    }


}
