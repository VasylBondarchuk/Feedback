<?php

namespace Training\Feedback\Ui\DataProvider\Form;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Training\Feedback\Api\Data\Reply\ReplyRepositoryInterface;
use Training\Feedback\Model\ResourceModel\Feedback\Collection;
use Training\Feedback\Model\ResourceModel\Feedback\CollectionFactory;

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
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
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
        array                    $meta = [],
        array                    $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->replyRepository = $replyRepository;
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
            try {
                $this->loadedData[$feedback->getId()]['reply_text'] =
                    $this->replyRepository->getByFeedbackId($feedback->getId())->getReplyText();
            } catch (LocalizedException $e) {
                $this->loadedData[$feedback->getId()]['reply_text'] = '';
            }
        }
        return $this->loadedData ?? [];
    }
}
