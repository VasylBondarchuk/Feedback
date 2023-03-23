<?php

declare(strict_types=1);

namespace Training\Feedback\Block;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\ResourceModel\User as UserResourceModel;
use Magento\User\Model\UserFactory;
use Training\Feedback\Api\Data\Feedback\FeedbackInterface;
use Training\Feedback\Model\ResourceModel\Feedback\Collection;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 *
 */
class FeedbackList extends Template {

    const DEFAULT_SORT_ORDER = 'desc';
    const DEFAULT_FILTERING_PARAM = 'all';
    const FILTERING_PARAM_REQUEST_NAME = 'filtering_param';
    
    const FEEDBACK_BACKGROUND_COLOR_CONFIGS_PATH = 'feedback_configuration/feedback_configuration_appearance/feedback_background_color';
    const FEEDBACK_DEFAULT_COLOR = 'white';
    const REPLY_BACKGROUND_COLOR_CONFIGS_PATH = 'feedback_configuration/feedback_configuration_general/reply_background_color';
    const REPLY_DEFAULT_COLOR = 'white';
    
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

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
     * 
     * @var RequestInterface
     */
    private RequestInterface $request;
    
    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param UserFactory $userFactory
     * @param UserResourceModel $resourceModel
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
            Context $context,
            CollectionFactory $collectionFactory,
            UserFactory $userFactory,
            UserResourceModel $resourceModel,
            StoreManagerInterface $storeManager,
            RequestInterface $request,
            ScopeConfigInterface $scopeConfig,
            array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collectionFactory = $collectionFactory;
        $this->userFactory = $userFactory;
        $this->resourceModel = $resourceModel;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getCollection(): Collection {
        $collection = $this->collectionFactory->create();
        
        // Pagination
        $pageNum = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
        $collection->setPageSize($pageSize)->setCurPage($pageNum);
        
        // Filtering
        if ($this->getCurrentFilteringParam() !== 'all') {
            $collection->addFieldToFilter(FeedbackInterface::CUSTOMER_ID, ['neq' => NULL]);
        }
        $collection->addFieldToFilter(FeedbackInterface::IS_ACTIVE, 1)
                ->addFieldToFilter(FeedbackInterface::STORE_ID, $this->getStoreId());
        // Sorting
        $collection->setOrder(FeedbackInterface::CREATION_TIME, $this->getCurrentDirection());
        
        return $collection;
    }

    /**
     * @inheritDoc
     * @throws LocalizedException
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()
                ->createBlock(CustomPager::class, 'feedback.list.pager')
                ->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        return $this;
    }    

    /**
     * @return int
     * @throws NoSuchEntityException
     */
    public function getStoreId(): int {
        return (int) $this->storeManager->getStore()->getId();
    }

    // Returns sorting order, selected by front-end user  
    public function getCurrentDirection() {
        return ($this->request->getParam('order')) ?? self::DEFAULT_SORT_ORDER;
    }

    // Returns sorting order, selected by front-end user  
    public function getCurrentFilteringParam() {
        return ($this->request->getParam(self::FILTERING_PARAM_REQUEST_NAME)) ?? self::DEFAULT_FILTERING_PARAM;
    }
    
    public function getFeedbackBackgroundColor() : string{
        return $this->getConfigParamValue(self::FEEDBACK_BACKGROUND_COLOR_CONFIGS_PATH) ?? self::FEEDBACK_DEFAULT_COLOR;
    }
    
     public function getReplyBackgroundColor() : string{
        return $this->getConfigParamValue(self::REPLY_BACKGROUND_COLOR_CONFIGS_PATH) ?? self::REPLY_DEFAULT_COLOR;
    }
    
    private function getConfigParamValue(string $configPath){
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }

}