<?php

namespace Training\Feedback\Console;

use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Training\Feedback\Api\Data\Feedback\FeedbackRepositoryInterface;
use Symfony\Component\Console\Helper\Table;
use Psr\Log\LoggerInterface;

/**
 * Class provides functionality to get feedback details by its id in console
 * bin/magento feedback:display --feedbackId=<feddbackNumber>
 */
class DisplayFeedbackDetailsById extends Command
{
    const FEEDBACK_ID = 'feedbackId';
    const HEADERS = ['ID', 'Author name', 'Author Email', 'Message', 'Status', 'Created' , 'Modified', 'Reply Notification', 'Replied'];
<<<<<<< HEAD
    
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
=======

>>>>>>> origin/master
    /**
     * @var FeedbackRepositoryInterface
     */
    private $feedbackRepository;

    /**
     * @param FeedbackRepositoryInterface $feedbackRepository
     */
    public function __construct(
        FeedbackRepositoryInterface $feedbackRepository,
        LoggerInterface           $logger    
    )
    {
        $this->feedbackRepository = $feedbackRepository;
        $this->logger = $logger;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::FEEDBACK_ID,
                null,
                InputOption::VALUE_REQUIRED,
                'Feedback ID'
            )
        ];

        $this->setName('feedback:display')
            ->setDescription('Display feedbacks details by its id')
            ->setDefinition($options);

        parent::configure();
    }

    /**
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $feedbackId = (int)$input->getOption(self::FEEDBACK_ID);

        try {            
                $table = new Table($output);
                $table->setHeaders(self::HEADERS);
                $feedback = $this->feedbackRepository->getById($feedbackId);
                $table->addRow([
                    $feedback->getFeedbackId(),
                    $feedback->getAuthorName(),
                    $feedback->getAuthorEmail(),
                    $feedback->getMessage(),
                    $feedback->getIsPublished(),
                    $feedback->getCreationTime(),
                    $feedback->getUpdateTime(),
                    $feedback->getReplyNotification(),
                    $feedback->getIsReplied()
                ]);
                $table->render();            
        } catch (LocalizedException $exception) {
            $this->displayErrorMessage($output,'There is no feedback, corresponding to entered id');
             $this->logger->error($exception->getLogMessage());
        }
        return $this;
    }

    /**
     * @param OutputInterface $output
     * @param string $errorMessage
     * @return void
     */
    private function displayErrorMessage (OutputInterface $output, string $errorMessage)
    {
        $output->writeln('<error>' . $errorMessage . '<error>');
    }
}
