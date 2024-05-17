<?php
declare(strict_types=1);

namespace Training\Feedback\Helper;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
/**
 *
 */
class Form
{
    const FEEDBACK_AUTHOR_NAME_FIELD = 'author_name';
    const FEEDBACK_AUTHOR_EMAIL_FIELD = 'author_email';
    const FEEDBACK_MESSAGE = 'message';
    const FEEDBACK_REPLY_FIELD = 'reply_text';
    
    
    
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;
    
    /**
     * 
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request) {        
        $this->request = $request;
    }

    /**
     * @param $post
     * @return void
     * @throws LocalizedException
     */
    public function validatePost(array $post) {
        if (!isset($post[self::FEEDBACK_AUTHOR_NAME_FIELD])
                || trim($post[self::FEEDBACK_AUTHOR_NAME_FIELD]) === '') {
            throw new LocalizedException(__('Name is missing'));
        }        
        if (!isset($post[self::FEEDBACK_AUTHOR_EMAIL_FIELD])
                || false === \strpos($post[self::FEEDBACK_AUTHOR_EMAIL_FIELD], '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }
        if (!isset($post[self::FEEDBACK_MESSAGE])
                || trim($post[self::FEEDBACK_MESSAGE]) === '') {
            throw new LocalizedException(__('Comment is missing'));
        }
    }
    
    public function isFormSubmitted(): bool {
        return (bool)$this->request->getPostValue();
    }
    
    public function getFormData(): array {
        return $this->request->getPostValue();
    }
}
