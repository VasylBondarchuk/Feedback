<?php

namespace Training\TestOM\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface
{
    /** @var PageFactory */
    private $pageFactory;

    private $testClass;

    private $playWithTest;

    // Instantiating the Context object is no longer required
    public function __construct(
        PageFactory $pageFactory,
        \Training\TestOM\Model\PlayWithTest $playWithTest,
         \Training\TestOM\Model\Test $testClass
    ) {
        $this->pageFactory = $pageFactory;
        $this->playWithTest = $playWithTest;
        $this->testClass = $testClass;
    }

    public function execute()
    {
       $this->testClass->log();
        //$this->playWithTest->run();
        $page = $this->pageFactory->create();
        return $page;
    }
}
