<?php

namespace Training\Feedback\Ui\DataProvider\Form;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Training\Feedback\Model\ResourceModel\Feedback\Collection;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;
use Training\Feedback\Api\Data\Reply\ReplyInterface;
use Magento\Backend\Model\Auth\Session;
use Psr\Log\LoggerInterface;

/**
 *
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;
    /**
     * @var
     */
    protected $loadedData;

    /**
     * @var ReplyRepositoryInterface
     */
    private ReplyRepositoryInterface $replyRepository;

    /**
     * @var Session
     */
    protected Session $authSession;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param ReplyRepositoryInterface $replyRepository
     * @param Session $authSession
     * @param LoggerInterface $logger
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        string                   $name,
        string                   $primaryFieldName,
        string                   $requestFieldName,
        CollectionFactory        $collectionFactory,
        DataPersistorInterface   $dataPersistor,
        ReplyRepositoryInterface $replyRepository,
        Session $authSession,
        LoggerInterface $logger,
        array                    $meta = [],
        array                    $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->replyRepository = $replyRepository;
        $this->authSession = $authSession;
        $this->logger  = $logger;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $data = $this->dataPersistor->get('training_feedback');
        if (!empty($data)) {
            $feedback = $this->collection->getNewEmptyItem();
            $feedback->setData($data);
            $this->loadedData[$feedback->getId()] = $feedback->getData();
            $this->dataPersistor->clear('training_feedback');
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $feedback) {
            $this->loadedData[$feedback->getId()] = $feedback->getData();
            $this->loadedData[$feedback->getId()][ReplyInterface::REPLY_TEXT] =
                $this->getReplyText((int)$feedback->getId());
        }
        return $this->loadedData ?? [];
    }

    /**
     * @param int $feedbackId
     * @return string|null
     */
    private function getReplyText(int $feedbackId) : ?string
    {
        $replyText = '';
        try {
            $replyText = $this->replyRepository->getByFeedbackId($feedbackId)->getReplyText();
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return $replyText;
    }
}
