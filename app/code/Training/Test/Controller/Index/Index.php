<?php

namespace Training\Test\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\RawFactory;

class Index implements HttpGetActionInterface
{
    private $resultRawFactory;

    public function __construct(
        Context $context,
        RawFactory $resultRawFactory
    ) {
        $this->resultRawFactory = $resultRawFactory;
    }
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents('simple text');
        return $resultRaw;
    }
}
