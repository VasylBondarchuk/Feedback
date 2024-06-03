<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Ratingoption;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Training\Feedback\Model\RatingOptionFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;

class Delete extends Action
{
    /**
     * @var RatingOptionFactory
     */
    protected $ratingOptionFactory;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Context $context
     * @param RatingOptionFactory $ratingOptionFactory
     * @param RedirectFactory $resultRedirectFactory
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        RatingOptionFactory $ratingOptionFactory,
        RedirectFactory $resultRedirectFactory,
        ManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->ratingOptionFactory = $ratingOptionFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->messageManager = $messageManager;
    }

    /**
     * Execute action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('rating_option_id');
        
        if ($id) {
            try {
                $ratingOption = $this->ratingOptionFactory->create()->load($id);
                if (!$ratingOption->getId()) {
                    throw new LocalizedException(__('This rating option no longer exists.'));
                }

                $ratingOption->delete();
                $this->messageManager->addSuccessMessage(__('The rating option has been deleted.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('An error occurred while deleting the rating option.')
                );
            }

            return $resultRedirect->setPath('*/*/');
        }
        
        $this->messageManager->addErrorMessage(__('We can\'t find a rating option to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}
