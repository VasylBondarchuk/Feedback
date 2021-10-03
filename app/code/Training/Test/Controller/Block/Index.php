<?php

namespace Training\Test\Controller\Block;

class Index extends \Magento\Framework\App\Action\Action
{
    private $layoutFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $layout = $this->layoutFactory->create();
        $block = $layout->createBlock('Training\Test\Block\Test');
        $this->getResponse()->appendBody($block->toHtml());
    }
}
