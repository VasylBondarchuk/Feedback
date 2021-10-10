<?php

namespace Training\Test\Controller\Action;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;

class Index implements HttpGetActionInterface
{
    private $resultRawFactory;
    private $layoutFactory;

    public function __construct(
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    public function execute()
    {
        $layout = $this->layoutFactory->create();
        $block = $layout->createBlock(\Magento\Framework\View\Element\Template::class);
        $block->setTemplate('Training_Test::test.phtml');
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents($block->toHtml()) ;
    }
}


