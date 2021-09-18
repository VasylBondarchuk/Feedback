<?php

namespace Training\TestOM\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface
{
    /** @var PageFactory */
    private $pageFactory;

    private $testClass;

    // Instantiating the Context object is no longer required
    public function __construct(
        PageFactory $pageFactory,
        \Training\TestOM\Model\Test $testClass
    ) {
        // Calling parent::__construct() is also no longer needed
        $this->pageFactory = $pageFactory;
        $this->testClass = $testClass;
    }

    public function execute()
    {
        $this->testClass->log();
        $page = $this->pageFactory->create();
        return $page;
    }
}
