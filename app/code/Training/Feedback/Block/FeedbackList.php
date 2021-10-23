<?php

namespace Training\Feedback\Block;
/**
 *
 */
class FeedbackList extends \Magento\Framework\View\Element\Template
{
    /**
     *
     */
    const PAGE_SIZE = 5;
    /**
     * @var \Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var
     */
    private $collection;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Timezone
     */
    private $timezone;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Stdlib\DateTime\Timezone $timezone
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context                  $context,
        \Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory $collectionFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone                       $timezone,
        array                                                             $data = array()
    )
    {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->timezone = $timezone;
    }

    /**
     * @return \Training\Feedback\Model\ResourceModel\Feedback\Collection
     */
    public function getCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
            $this->collection->addFieldToFilter('is_active', 1);
            $this->collection->setOrder('creation_time', 'DESC');
        }
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getPagerHtml() : string
    {
        $pagerBlock = $this->getChildBlock('feedback_list_pager');
        if ($pagerBlock instanceof \Magento\Framework\DataObject) {
            /* @var $pagerBlock \Magento\Theme\Block\Html\Pager */
            $pagerBlock
                ->setUseContainer(false)
                ->setShowPerPage(false)
                ->setShowAmounts(false)
                ->setLimit($this->getLimit())
                ->setCollection($this->getCollection());
            return $pagerBlock->toHtml();
        }
        return '';
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return static::PAGE_SIZE;
    }

    /**
     * @return string
     */
    public function getAddFeedbackUrl()
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
}


