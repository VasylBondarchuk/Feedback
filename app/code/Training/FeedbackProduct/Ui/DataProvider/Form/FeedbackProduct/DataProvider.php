<?php

namespace Training\FeedbackProduct\Ui\DataProvider\Form\FeedbackProduct;

use Training\Feedback\Api\Data\FeedbackInterface;
use Training\Feedback\Api\Data\FeedbackRepositoryInterface;
use Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;

class DataProvider extends ProductDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;
    /**
     * @var FeedbackRepositoryInterface
     */
    protected $feedbackRepository;
    /**
     * @var FeedbackInterface
     */
    private $feedback;
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param FeedbackRepositoryInterface $feedbackRepository
     * @param array $addFieldStrategies
     * @param array $addFilterStrategies
     * @param array $meta
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        FeedbackRepositoryInterface $feedbackRepository,
        $addFieldStrategies,
        $addFilterStrategies,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $collectionFactory,
            $addFieldStrategies,
            $addFilterStrategies,
            $meta,
            $data
        );
        $this->request = $request;
        $this->feedbackRepository = $feedbackRepository;
    }
    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();
        $collection->addAttributeToSelect('status');
        if (!$this->getFeedback()) {
            return $collection;
        }
        return $this->addCollectionFilters($collection);
    }
    /**
     * Add specific filters
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function addCollectionFilters(Collection $collection)
    {
        $assignedProductIds = [];
        $assignedProducts = $this->getFeedback()->getExtensionAttributes()->getProducts();
        if (is_array($assignedProducts)) {
            foreach ($assignedProducts as $assignedProduct) {
                $assignedProductIds[] = $assignedProduct->getId();
            }
        }
// exclude from collection products which are associated with current feedback
        if ($assignedProductIds) {
            $collection->addAttributeToFilter(
                $collection->getIdFieldName(),
                ['nin' => [$assignedProductIds]]
            );
        }
        return $collection;
    }
    /**
     * Retrieve feedback
     *
     * @return FeedbackInterface|null
     */
    protected function getFeedback()
    {
        if (null !== $this->feedback) {
            return $this->feedback;
        }
        if (!($id = $this->request->getParam('feedback_id'))) {
            return null;
        }
        return $this->feedback = $this->feedbackRepository->getById($id);
    }
}
