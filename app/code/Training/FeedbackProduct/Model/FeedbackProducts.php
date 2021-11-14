<?php

namespace Training\FeedbackProduct\Model;

use Magento\Framework\App\RequestInterface;

class FeedbackProducts
{
    private $feedbackDataLoader;
    private $feedbackProductsResource;
    private $request;

    public function __construct(
        FeedbackDataLoader $feedbackDataLoader,
        ResourceModel\FeedbackProducts $feedbackProductsResource,
        RequestInterface $request
    ) {
        $this->feedbackDataLoader = $feedbackDataLoader;
        $this->feedbackProductsResource = $feedbackProductsResource;
        $this->request = $request;
    }

    public function loadProductRelations($feedback)
    {
        $productIds = $this->feedbackProductsResource->loadProductRelations($feedback->getId());
        return $this->feedbackDataLoader->addProductsToFeedbackByIds($feedback, $productIds);
    }


    public function saveProductRelations($feedback): FeedbackProducts
    {
        $productIds = [];

        $products = $feedback->getExtensionAttributes()->getProducts();

        if (is_array($products)) {
            foreach ($products as $product) {
                $productIds[] = $product->getId();
            }
        }


        $this->feedbackProductsResource->saveProductRelations($feedback->getId(), $productIds);

        return $this;
    }

    public function getSkusFromInput()
    {
        $skus = "";
        if ($post = $this->request->getPostValue()) {
            $skus = $post['products_skus'];
        }

        return $skus;
    }
}
