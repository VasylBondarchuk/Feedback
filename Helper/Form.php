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
    
    const RATING_OPTION_CODE_FIELD = 'rating_option_code';
    const RATING_OPTION_NAME_FIELD = 'rating_option_name';
    const RATING_OPTION_MAX_VALUE_FIELD = 'rating_option_max_value';
    const RATING_OPTION_IS_ACTIVE_FIELD = 'is_active';

    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
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
    public function validateFeedbackPost(array $post) {
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
    
    /**
     * @param array $post
     * @return void
     * @throws LocalizedException
     */
    public function validateRatingOptionPost(array $post) {
        if (!isset($post[self::RATING_OPTION_CODE_FIELD]) || trim($post[self::RATING_OPTION_CODE_FIELD]) === '') {
            throw new LocalizedException(__('Rating option code is missing'));
        }
        if (!isset($post[self::RATING_OPTION_NAME_FIELD]) || trim($post[self::RATING_OPTION_NAME_FIELD]) === '') {
            throw new LocalizedException(__('Rating option name is missing'));
        }
        if (!isset($post[self::RATING_OPTION_MAX_VALUE_FIELD]) || !filter_var($post[self::RATING_OPTION_MAX_VALUE_FIELD], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
            throw new LocalizedException(__('Rating option max value must be a positive integer'));
        }
        if (!isset($post[self::RATING_OPTION_IS_ACTIVE_FIELD]) || !in_array($post[self::RATING_OPTION_IS_ACTIVE_FIELD], ['0', '1'], true)) {
            throw new LocalizedException(__('Invalid value for "Is Active" field'));
        }
    }

    public function isFormSubmitted(): bool {
        return (bool)$this->request->getPostValue();
    }

    public function getFormData(): array {
        return $this->request->getPostValue();
    }
}
