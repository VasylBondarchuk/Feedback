<?php

namespace Training\Feedback\Controller\Adminhtml\Index;

use Magento\Backend\Model\View\Result\ForwardFactory;

class NewAction extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';
    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
