<?php
declare(strict_types=1);

namespace Training\Feedback\Plugin;

use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\UrlInterface;
use Training\Feedback\Helper\FeedbackConfigProvider;


/**
 * Class generates top menu link
 */
class Topmenu
{
    private const TOP_MENU_LINK_PATH = 'training_feedback/index/';    
    private const TOP_MENU_LINK_DEFAULT_NAME = 'Store Feedback';

    /**
     * @var NodeFactory
     */
    private NodeFactory $nodeFactory;

    /**
     * @var UrlInterface
     */
    private UrlInterface $urlBuilder;
    /**
     * @var ScopeConfigInterface
     */
    private FeedbackConfigProvider $feedbackConfigProvider;

    /**
     * 
     * @param NodeFactory $nodeFactory
     * @param UrlInterface $urlBuilder
     * @param FeedbackConfigProvider $feedbackConfigProvider
     */
    public function __construct(
        NodeFactory $nodeFactory,
        UrlInterface $urlBuilder,
        FeedbackConfigProvider $feedbackConfigProvider
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->urlBuilder = $urlBuilder;
        $this->feedbackConfigProvider = $feedbackConfigProvider;
    }

    /**
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return void
     */
    public function beforeGetHtml(
            \Magento\Theme\Block\Html\Topmenu $subject,
            string $outermostClass = '',
            string $childrenWrapClass = '',
            int $limit = 0): void
    {
        $menuNode = $this->nodeFactory->create(
            ['data' => $this->getNodeAsArray(
                $this->getTopMenuLinkName(),
                "index"
            ),
            'idField' => 'id',
            'tree' => $subject->getMenu()->getTree()]
        );
        
        $subject->getMenu()->addChild($menuNode);
    }

    /**
     * @param $name
     * @param $id
     * @return array
     */
    private function getNodeAsArray($name, $id): array
    {
        //here you can add url as per your choice of menu
        $url = $this->urlBuilder->getUrl(self::TOP_MENU_LINK_PATH . $id); 
        return ['name' => __($name),
            'id' => $id,
            'url' => $url,
            'has_active' => false,
            'is_active' => false,];
    }

    /**
     * Gets name of the link in the top menu
     *
     * @return string|null
     */
    public function getTopMenuLinkName(): ?string
    {
        return $this->feedbackConfigProvider->getTopMenuLinkName()
                ?? self::TOP_MENU_LINK_DEFAULT_NAME;
    }
}
