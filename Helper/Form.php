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
        if (!isset($post['author_name']) || trim($post['author_name']) === '') {
            throw new LocalizedException(__('Name is missing'));
        }
        if (!isset($post['message']) || trim($post['message']) === '') {
            throw new LocalizedException(__('Comment is missing'));
        }
        if (!isset($post['author_email']) || false === \strpos($post['author_email'], '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }        
    }
    
    public function isFormSubmitted(): bool {
        return (bool)$this->request->getPostValue();
    }
    
    public function getFormData(): array {
        return $this->request->getPostValue();
    }
}
