<?php
declare(strict_types=1);

namespace Training\Feedback\Controller\Adminhtml\Ratingoption;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Psr\Log\LoggerInterface;

use Training\Feedback\Api\Data\RatingOption\RatingOptionInterface;
use Training\Feedback\Api\Data\RatingOption\RatingOptionRepositoryInterface;
use Training\Feedback\Model\RatingOptionFactory;
use Training\Feedback\Helper\Form;

/**
 * Saves feedbacks
 */
class Save extends Action implements HttpGetActionInterface {

    /**
     *
     */
    const ADMIN_RESOURCE = 'Training_Feedback::feedback_save';

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var DataPersistorInterface
     */
    private DataPersistorInterface $dataPersistor;

    /**
     * @var FeedbackInterface
     */
    private RatingOptionFactory $ratingOptionFactory;
    
    /**
     * 
     * @var RatingOptionRepositoryInterface
     */
    private RatingOptionRepositoryInterface $ratingOptionRepository;   

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;    

    /**
     * 
     * @var Form
     */
    private Form $form; 
    
    public function __construct(
            Context $context,
            ManagerInterface $messageManager,
            ResultFactory $resultFactory,
            DataPersistorInterface $dataPersistor,                        
            RatingOptionRepositoryInterface $ratingOptionRepository,
            RatingOptionFactory $ratingOptionFactory,            
            LoggerInterface $logger,
            RequestInterface $request,            
            Form $form            
    ) {
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->dataPersistor = $dataPersistor;        
        $this->ratingOptionRepository = $ratingOptionRepository;
        $this->ratingOptionFactory = $ratingOptionFactory; 
        $this->logger = $logger;
        $this->request = $request;        
        $this->form = $form;       
        parent::__construct($context);
    }

    /**
     * @return Redirect|ResultInterface
     * @throws LocalizedException
     */
    public function execute() {
        if ($this->form->isFormSubmitted()) {
            $post = $this->form->getFormData();
            try {
                $this->form->validateRatingOptionPost($post);                
                    $this->saveRatingOption($post); 
                    $this->messageManager->addSuccessMessage(__('You saved the rating option.'));                
                
                $this->dataPersistor->clear('training_feedback_rating_options');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                        __('An error occurred while saving the rating option. %1', $e->getMessage()));
                $this->logger->error($e->getLogMessage());
            }
            $this->dataPersistor->set('training_feedback_rating_options', $post);            
            return $this->redirect($post);
        }
    }    

    /**
     * 
     * @param array $post
     * @return void
     */
    private function saveRatingOption(array $post): void {        
        if (empty($post[RatingOptionInterface::RATING_OPTION_ID])) {
            $post[RatingOptionInterface::RATING_OPTION_ID] = null;
        }        
        $ratingOption = $this->getRatingOptionModel($post);
        $ratingOption->setData($post);        
        $this->ratingOptionRepository->save($ratingOption);        
    }  
    
    /**
     * 
     * @param array $post
     * @return RatingOptionInterface
     */
    private function getRatingOptionModel(array $post): RatingOptionInterface {
        $ratingOptionId = $post[RatingOptionInterface::RATING_OPTION_ID] ?? null;

        if ($ratingOptionId) {
            $ratingOption = $this->ratingOptionRepository->getById((int)$ratingOptionId);
        } else {
            $ratingOption = $this->ratingOptionFactory->create();
        }
        return $ratingOption;
    }

    /**
     * 
     * @param type $post
     * @return mixed
     */
    private function redirect(array $post): mixed {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $ratingOption = $this->getRatingOptionModel($post);
        $redirect = $post['back'] ?? 'close';
        if ($redirect === 'continue') {
            $resultRedirect->setPath('*/*/edit', [RatingOptionInterface::RATING_OPTION_ID => $ratingOption->getId()]);
        } elseif ($redirect === 'close') {
            $resultRedirect->setPath('*/*/');
        }
        return $resultRedirect;
    }    
}
