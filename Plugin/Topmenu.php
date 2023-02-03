<?php
declare(strict_types=1);

namespace Training\Feedback\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class generates top menu link
 */
class Topmenu
{
    private const TOP_MENU_LINK_NAME_PATH =
        'feedback_configuration/feedback_configuration_general/top_menu_link_name';

    /**
     * @var NodeFactory
     */
    protected NodeFactory $nodeFactory;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;
    /**
     * @var ScopeConfigInterface
     */
    protected ScopeConfigInterface $scopeConfig;

    /**
     * @param NodeFactory $nodeFactory
     * @param UrlInterface $urlBuilder
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        NodeFactory $nodeFactory,
        UrlInterface $urlBuilder,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->nodeFactory = $nodeFactory;
        $this->urlBuilder = $urlBuilder;
        $this->scopeConfig = $scopeConfig;
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
        /*$menuNode->addChild($this->nodeFactory->create(['data' => $this->getNodeAsArray("MainMenu", "mnuMyMenu"),
            'idField' => 'id',
            'tree' => $subject->getMenu()->getTree(),]));*/
        $subject->getMenu()->addChild($menuNode);
    }

    /**
     * @param $name
     * @param $id
     * @return array
     */
    protected function getNodeAsArray($name, $id): array
    {
        $url = $this->urlBuilder->getUrl("training_feedback/index/" . $id); //here you can add url as per your choice of menu
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
        return $this->scopeConfig->getValue(self::TOP_MENU_LINK_NAME_PATH, ScopeInterface::SCOPE_STORE);
    }
}
