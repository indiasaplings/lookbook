<?php
namespace Magiccart\Lookbook\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Widget\Model\Layout\UpdateFactory;
use Magiccart\Lookbook\Model\Options\BlockPosition;
/**
 *  AddLayoutUpdateHandleObserver
 */
class AddLayout implements ObserverInterface
{
    const BLOCK        = 'Magiccart\Lookbook\Block\Product';

    const TEMPLATE     = 'Magiccart_Lookbook::product.phtml';

    const RELATED_NAME = 'catalog.product.related';

    const UPSELL_NAME  = 'product.info.upsell';

    const CROSSEL_NAME = 'checkout.cart.crosssell';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magento\CatalogWidget\Model\RuleFactory
     */
    protected $catalogWidgetRuleFactory;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $_httpContext;

    /**
     * @var \Magento\Rule\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magiccart\Lookbook\Model\LookbookFactory
     */
    protected $lookbookFactory;

    /**
     * @var \Magiccart\Magicslider\Helper\Data
     */
    protected $helper;

    private $template = null;
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\CatalogWidget\Model\RuleFactory $catalogWidgetRuleFactory,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magiccart\Lookbook\Model\LookbookFactory $lookbookFactory,
        \Magiccart\Lookbook\Helper\Data $helper
    ){
        $this->request                  = $request;
        $this->coreRegistry             = $coreRegistry;
        $this->json                     = $json;
        $this->catalogWidgetRuleFactory = $catalogWidgetRuleFactory;
        $this->_httpContext             = $httpContext;
        $this->sqlBuilder               = $sqlBuilder;
        $this->storeManager             = $storeManager;
        $this->lookbookFactory          = $lookbookFactory;
        $this->helper                   = $helper;
    }

    /**
     * Add handles to the page.
     *
     * @param Observer $observer
     * @event layout_load_before
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var LayoutInterface $layout */
        $layout = $observer->getData('layout');

        $type = '';
        $typeId = '';
        $actionName = $this->request->getFullActionName();
        if($actionName == 'cms_index_index'){
            $type = 'home';
        }elseif($actionName == 'checkout_cart_index'){
            $type = 'cart';
        }elseif($actionName == 'catalog_product_view'){
            $type = 'product';
            $typeId = $this->coreRegistry->registry('current_product')->getId();
        }elseif($actionName == 'catalog_category_view'){
            $type = 'category';
            $typeId = $this->coreRegistry->registry('current_category')->getId();
        }

        if(!$type) return;

        $layoutUpdate  = $layout->getUpdate();
        $store         = $this->storeManager->getStore()->getStoreId();
        $lookbook   = $this->lookbookFactory->create()->getCollection()
                            ->addFieldToFilter('stores', [['finset' => 0], ['finset' => $store]])
                            ->addFieldToFilter('status', 1);

        $ruleIdBlockPosition = [];
        foreach ($lookbook as $rule) {
            if($rule->getData('type_id')!=1)continue;

            $decodeData = json_decode($rule->getData('config'), true);
            if(is_array($decodeData)){
                unset($decodeData['lookbook_id']);
                $rule->addData($decodeData);
            }

            $blockPosition = $rule->getData('block_position');

            if(in_array($blockPosition, $ruleIdBlockPosition)) continue;

            if( !strlen(strstr($blockPosition, $type)) > 0 )
                continue;

            if($type == 'product'){
                $displayPlace = $this->checkDisplayPlace($rule);
                if(!$displayPlace)
                    continue;
            }
            if($type == 'category'){
                $categoryIds = $rule->getData('display_to_category');
                if(!$categoryIds || !in_array($typeId, $categoryIds))
                    continue;
            }

            $xml = $this->getXml($rule);

            if($xml) $layoutUpdate->addUpdate($xml);

            $ruleIdBlockPosition[] = $blockPosition;
        }
    }

    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    public function getXml($rule)
    {
        $blockPosition = $rule->getData('block_position');

        $nameInLayout  = strtolower($this->helper->getModuleName()) . '_' . $blockPosition . '_' . $rule->getData('lookbook_id');
        $xml = '';
        switch ($blockPosition) {
            case 'product_content_tab':
                $xml = '<referenceBlock name="product.info.details">
                            <block class="' . self::BLOCK . '" name="' . $nameInLayout . '" template="' . self::TEMPLATE . '" group="detailed_info">
                                <arguments>
                                    <argument name="identifier" xsi:type="string">' . $rule->getData('identifier') . '</argument>
                                    <argument name="sort_order" xsi:type="string">100</argument>
                                </arguments>
                            </block>
                        </referenceBlock>';
                break;
            case 'product_after_info_main':
                $xml = '<referenceContainer name="product.info.main">
                            <block class="' . self::BLOCK . '" name="' . $nameInLayout . '" template="' . self::TEMPLATE . '" after="-">
                                <arguments>
                                    <argument name="identifier" xsi:type="string">' . $rule->getData('identifier') . '</argument>
                                </arguments>
                            </block>
                        </referenceContainer>';
                break;
            default:
                $xml = '<referenceContainer name="content.top">
                            <block class="' . self::BLOCK . '" name="' . $nameInLayout . '" template="' . self::TEMPLATE . '">
                                <arguments>
                                    <argument name="identifier" xsi:type="string">' . $rule->getData('identifier') . '</argument>
                                </arguments>
                            </block>
                        </referenceContainer>';
                $destination = $this->getContainerByPosition($blockPosition);
                if($destination) $xml .= '<move element="' . $nameInLayout . '" destination="' . $destination . '" ' . $this->getPositionAttribute($blockPosition) . ' />';
        }

        return $xml;      
    }

    protected function getRule($conditions)
    {
        $rule = $this->catalogWidgetRuleFactory->create();
        if(is_array($conditions)) $rule->loadPost($conditions);
        return $rule;
    }

    public function checkDisplayPlace($rule)
    {
        $displayPlace = $rule->getData('parameters');
        $rule         = $this->getRule($displayPlace);

        return $rule->getConditions()->validate($this->getProduct());
    }

    protected function getPositionAttribute($position)
    {
        switch ($position) {
            case BlockPosition::PRODUCT_AFTER_UPSELL:
                $positionAttribute = 'after="' . self::UPSELL_NAME . '"';
                break;
            case BlockPosition::PRODUCT_AFTER_RELATED:
                $positionAttribute = 'after="' . self::RELATED_NAME . '"';
                break;
            case BlockPosition::PRODUCT_BEFORE_RELATED:
                $positionAttribute = 'before="' . self::RELATED_NAME . '"';
                break;
            case BlockPosition::PRODUCT_BEFORE_TAB:
                $positionAttribute = 'before="product.info.details"';
                break;
            case BlockPosition::PRODUCT_BEFORE_UPSELL:
                $positionAttribute = 'before="' . self::UPSELL_NAME . '"';
                break;
            case BlockPosition::CART_AFTER_CROSSSEL:
                $positionAttribute = 'after="'.self::CROSSEL_NAME.'"';
                break;
            case BlockPosition::CART_BEFORE_CROSSSEL:
                $positionAttribute = 'before="'.self::CROSSEL_NAME.'"';
                break;
            case BlockPosition::CATEGORY_SIDEBAR_BOTTOM:
            case BlockPosition::PRODUCT_SIDEBAR_BOTTOM:
            case BlockPosition::PRODUCT_CONTENT_BOTTOM:
            case BlockPosition::CATEGORY_CONTENT_BOTTOM:
            case BlockPosition::CART_CONTENT_BOTTOM:
            case BlockPosition::HOME_CONTENT_BOTTOM:
                $positionAttribute = 'after="-"';
                break;
            case BlockPosition::PRODUCT_INTO_RELATED:
            case BlockPosition::PRODUCT_INTO_UPSELL:
            case BlockPosition::CART_CONTENT_TOP:
            case BlockPosition::CATEGORY_CONTENT_TOP:
            case BlockPosition::CATEGORY_SIDEBAR_TOP:
            case BlockPosition::PRODUCT_SIDEBAR_TOP:
            case BlockPosition::PRODUCT_CONTENT_TOP:
            case BlockPosition::HOME_CONTENT_TOP:
                $positionAttribute = 'before="-"';
                break;
            default:
                $positionAttribute = '';
        }

        return $positionAttribute;
    }

    protected function getTemplateByPosition($position)
    {
        switch ($position) {
            case BlockPosition::CATEGORY_SIDEBAR_BOTTOM:
            case BlockPosition::CATEGORY_SIDEBAR_TOP:
            case BlockPosition::PRODUCT_SIDEBAR_BOTTOM:
            case BlockPosition::PRODUCT_SIDEBAR_TOP:
                $template = self::SIDEBAR_TEMPLATE;
                break;
            default:
                $template = self::TEMPLATE;
        }

        return $template;
    }

    protected function getContainerByPosition($position)
    {
        switch ($position) {
            case BlockPosition::PRODUCT_CONTENT_TOP:
            case BlockPosition::CART_CONTENT_TOP:
                $container = 'content.top';
                break;
            case BlockPosition::CATEGORY_CONTENT_TOP:
                $container = 'category.view.container';
                break;
            case BlockPosition::CART_CONTENT_BOTTOM:
            case BlockPosition::CATEGORY_CONTENT_BOTTOM:
            case BlockPosition::PRODUCT_CONTENT_BOTTOM:
                $container = 'content.bottom';
                break;
            case BlockPosition::CATEGORY_SIDEBAR_BOTTOM:
            case BlockPosition::PRODUCT_SIDEBAR_BOTTOM:
                $container = 'sidebar.additional';
                break;
            case BlockPosition::CATEGORY_SIDEBAR_TOP:
            case BlockPosition::PRODUCT_SIDEBAR_TOP:
                $container = 'sidebar.main';
                break;
            case BlockPosition::PRODUCT_INTO_RELATED:
            case BlockPosition::PRODUCT_INTO_UPSELL:
            case BlockPosition::PRODUCT_AFTER_RELATED:
            case BlockPosition::PRODUCT_BEFORE_RELATED:
            case BlockPosition::PRODUCT_AFTER_UPSELL:
            case BlockPosition::PRODUCT_BEFORE_UPSELL:
                $container = 'content.aside';
                break;
            case BlockPosition::CART_AFTER_CROSSSEL:
            case BlockPosition::CART_BEFORE_CROSSSEL:
                $container = 'checkout.cart.items';
                break;
            case BlockPosition::HOME_CONTENT_TOP:
            case BlockPosition::HOME_CONTENT_BOTTOM:
            case BlockPosition::PRODUCT_BEFORE_TAB:
                $container = 'content';
                break;
            case BlockPosition::PRODUCT_BEFORE_TAB:
                $container = 'product.info.main';
            default:
                $container = '';
        }

        return $container;
    }
}
