<?php

namespace Magiccart\Lookbook\Model\Options;

use Magiccart\Lookbook\Model\Options\RuleType;
use Magento\Framework\Data\OptionSourceInterface;

class BlockPosition implements OptionSourceInterface
{
    public const PRODUCT_BEFORE_RELATED = 'product_before_related';

    public const PRODUCT_AFTER_RELATED = 'product_after_related';

    public const PRODUCT_INTO_RELATED = 'product_into_related';

    public const PRODUCT_BEFORE_UPSELL = 'product_before_upsell';

    public const PRODUCT_AFTER_UPSELL = 'product_after_upsell';

    public const PRODUCT_INTO_UPSELL = 'product_into_upsell';

    public const PRODUCT_CONTENT_TOP = 'product_content_top';

    public const PRODUCT_CONTENT_TAB = 'product_content_tab';

    public const PRODUCT_AFTER_INFO_MAIN = 'product_after_info_main';

    public const PRODUCT_BEFORE_TAB = 'product_before_tab';

    public const PRODUCT_CONTENT_BOTTOM = 'product_content_bottom';

    public const PRODUCT_SIDEBAR_TOP = 'product_sidebar_top';

    public const PRODUCT_SIDEBAR_BOTTOM = 'product_sidebar_bottom';

    public const CART_BEFORE_CROSSSEL = 'cart_before_crosssel';

    public const CART_AFTER_CROSSSEL = 'cart_after_crosssel';

    public const CART_INTO_CROSSSEL = 'cart_into_crosssel';

    public const CART_CONTENT_TOP = 'cart_content_top';

    public const CART_CONTENT_BOTTOM = 'cart_content_bottom';

    public const CATEGORY_CONTENT_TOP = 'category_content_top';

    public const CATEGORY_CONTENT_BOTTOM = 'category_content_bottom';

    public const CATEGORY_SIDEBAR_TOP = 'category_sidebar_top';

    public const CATEGORY_SIDEBAR_BOTTOM = 'category_sidebar_bottom';

    public const HOME_CONTENT_TOP = 'home_content_top';

    public const HOME_CONTENT_BOTTOM = 'home_content_bottom';

    public const CUSTOM = 'custom';

    /**
     * @var \Magiccart\Magicslider\Model\Options\RuleType
     */
    private $ruleType;

    public function __construct(RuleType $ruleType)
    {
        $this->ruleType = $ruleType;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            // ['value' => "widget", 'label' => __('Display by Widget')],
            ['value' => " ", 'label' => __('Please select an option')],
            [
                'label' => __('Home Page'),
                'value' => [
                    ['value' => self::HOME_CONTENT_TOP, 'label' => __('Content Top')],
                    ['value' => self::HOME_CONTENT_BOTTOM, 'label' => __('Content Bottom')],
                ]
            ],
            [
                'label' => __('Product Page'),
                'value' => [
                    ['value' => self::PRODUCT_BEFORE_RELATED, 'label' => __('Before Native Related Block')],
                    ['value' => self::PRODUCT_AFTER_RELATED, 'label' => __('After Native Related Block')],
                    // ['value' => self::PRODUCT_INTO_RELATED, 'label' => __('Add into Native Related Block')],
                    ['value' => self::PRODUCT_AFTER_INFO_MAIN, 'label' => __('After Product Infomation Block')],
                    ['value' => self::PRODUCT_BEFORE_UPSELL, 'label' => __('Before Native Up-sells Block')],
                    ['value' => self::PRODUCT_AFTER_UPSELL, 'label' => __('After Native Up-sells Block')],
                    // ['value' => self::PRODUCT_INTO_UPSELL, 'label' => __('Add into Native Up-sells Block')],
                    ['value' => self::PRODUCT_CONTENT_TOP, 'label' => __('Content Top')],
                    ['value' => self::PRODUCT_CONTENT_TAB, 'label' => __('Into Native Tab Block')],
                    ['value' => self::PRODUCT_BEFORE_TAB, 'label' => __('Before Native Tab Block')],
                    ['value' => self::PRODUCT_CONTENT_BOTTOM, 'label' => __('Content Bottom')],
                    ['value' => self::PRODUCT_SIDEBAR_TOP, 'label' => __('Sidebar Top')],
                    ['value' => self::PRODUCT_SIDEBAR_BOTTOM, 'label' => __('Sidebar Bottom')],
                ]
            ],
            [
                'label' => __('Shopping Cart Page'),
                'value' => [
                    // ['value' => self::CART_BEFORE_CROSSSEL, 'label' => __('Before Native Cross-sells Block')],
                    // ['value' => self::CART_AFTER_CROSSSEL, 'label' => __('After Native Cross-sells Block')],
                    // ['value' => self::CART_INTO_CROSSSEL, 'label' => __('Add into Native Cross-sells Block')],
                    ['value' => self::CART_CONTENT_TOP, 'label' => __('Content Top')],
                    ['value' => self::CART_CONTENT_BOTTOM, 'label' => __('Content Bottom')],
                ]
            ],
            [
                'label' => __('Category Page'),
                'value' => [
                    ['value' => self::CATEGORY_CONTENT_TOP, 'label' => __('Content Top')],
                    ['value' => self::CATEGORY_CONTENT_BOTTOM, 'label' => __('Content Bottom')],
                    ['value' => self::CATEGORY_SIDEBAR_TOP, 'label' => __('Sidebar Top')],
                    ['value' => self::CATEGORY_SIDEBAR_BOTTOM, 'label' => __('Sidebar Bottom')],
                ]
            ]
        ];
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function getTypeByValue($value)
    {
        switch ($value) {
            case self::PRODUCT_AFTER_RELATED:
            case self::PRODUCT_AFTER_UPSELL:
            case self::PRODUCT_BEFORE_RELATED:
            case self::PRODUCT_BEFORE_UPSELL:
            case self::PRODUCT_INTO_RELATED:
            case self::PRODUCT_INTO_UPSELL:
            case self::PRODUCT_CONTENT_TOP:
            case self::PRODUCT_CONTENT_TAB:
            case self::PRODUCT_BEFORE_TAB:
            case self::PRODUCT_CONTENT_BOTTOM:
            case self::PRODUCT_SIDEBAR_BOTTOM:
            case self::PRODUCT_SIDEBAR_TOP:
                $result = $this->ruleType->getNameByValue(RuleType::PRODUCT);
                break;
            case self::CART_BEFORE_CROSSSEL:
            case self::CART_AFTER_CROSSSEL:
            case self::CART_INTO_CROSSSEL:
            case self::CART_CONTENT_TOP:
            case self::CART_CONTENT_BOTTOM:
                $result = $this->ruleType->getNameByValue(RuleType::CART);
                break;
            case self::CATEGORY_CONTENT_TOP:
            case self::CATEGORY_CONTENT_BOTTOM:
            case self::CATEGORY_SIDEBAR_TOP:
            case self::CATEGORY_SIDEBAR_BOTTOM:
                $result = $this->ruleType->getNameByValue(RuleType::CATEGORY);
                break;
            case self::CUSTOM:
            default:
                $result = $this->ruleType->getNameByValue(RuleType::CUSTOM);
        }

        return $result;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getNameByValue($value)
    {
        $result = '';
        foreach ($this->toOptionArray() as $item) {
            if(is_array($item['value'])){
                foreach ($item['value'] as $childItem) {
                    if($childItem['value']==$value){
                        $result = $childItem['label']->getText();
                    }
                }
            }
            elseif ($item['value'] == $value) {
                $result = $item['label']->getText();
            }
        }

        return $result;
    }
}
