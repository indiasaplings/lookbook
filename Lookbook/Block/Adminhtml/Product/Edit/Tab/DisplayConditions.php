<?php

namespace Magiccart\Lookbook\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class DisplayConditions extends Generic implements TabInterface
{
    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $rendererFieldset;

    /**
     * @var \Magento\CatalogWidget\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var \Magiccart\Magicslider\Model\Options\BlockPosition
     */
    protected $blockPosition;

    /**
     * @var \Magiccart\Magicslider\Model\Options\Category
     */    
    protected $displayToCategory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\CatalogWidget\Model\RuleFactory $ruleFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magiccart\Magicslider\Model\Options\BlockPosition $blockPosition
     * @param \Magiccart\Magicslider\Model\Options\Category $blockPosition

     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\CatalogWidget\Model\RuleFactory $ruleFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magiccart\Lookbook\Model\Options\BlockPosition $blockPosition,
        \Magiccart\Lookbook\Model\Options\Category $displayToCategory,

        array $data = []
    ) {
        $this->rendererFieldset  = $rendererFieldset;
        $this->json              = $json;
        $this->layoutFactory     = $layoutFactory;
        $this->ruleFactory       = $ruleFactory;
        $this->conditions        = $conditions;
        $this->blockPosition     = $blockPosition;
        $this->displayToCategory = $displayToCategory;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('\'Where to Display\' Conditions ');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('\'Where to Display\' Conditions ');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Generic
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('\'Where to Display\' Conditions')]);

        $model = $this->_coreRegistry->registry('lookbook');

        $blockPosition = $fieldset->addField(
                'block_position',
                'select',
                [
                    'name' => 'block_position',
                    'label' => __('Block Position'),
                    'title' => __('Block Position'),
                    'required' => false,
                    'values' => $this->blockPosition->toOptionArray()
                ]
        );

        $field = $fieldset->addField(
            'display_to_category',
                'multiselect',
                [
                    'name' => 'display_to_category',
                    'label' => __('Specific Categories'),
                    'title' => __('Specific Categories'),
                    'values' => $this->displayToCategory->toOptionArray(),
                ]
        );

        $fieldsetId = 'rule_conditions_fieldset'. uniqid();
        $formName = 'catalog_rule_form'. uniqid();

        $displayPlace = $model->getData('config');
        $decodeData = json_decode($displayPlace, true);
        $displayPlace = null;
        if(is_array($decodeData)){
            $displayPlace = isset($decodeData['parameters']) ? $decodeData['parameters'] : null;
        }
        $rule         = $this->ruleFactory->create();
        if (is_array($displayPlace)){
            $rule->loadPost($displayPlace);
            $rule->getConditions()->setJsFormObject($fieldsetId);
        }

        $newChildUrl = $this->getUrl(
            'lookbook/product/newConditionHtml/form/' . $fieldsetId,
            ['form_namespace' => $fieldsetId]
        );

        $renderer = $this->rendererFieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
                        ->setNewChildUrl($newChildUrl)
                        ->setFieldSetId($fieldsetId);

        $fieldset = $form->addFieldset(
            $fieldsetId,
            [
                'legend' => __(
                    'Choose the conditions to define what products display block.'
                )
            ]
        )->setRenderer(
            $renderer
        );

        $fieldset->addField(
            'conditions',
            'text',
            ['name' => 'conditions', 'label' => __('conditions'), 'title' => __('conditions'), 'data-form-parts' => $formName]
        )->setRule(
            $rule
        )->setRenderer(
            $this->conditions
        );

        $form->addValues($model->getData());
        $this->setForm($form);

        $blockPosition->setAfterElementHtml(
            '
                <script type="text/javascript">
                require([
                    "jquery",
                    "uiRegistry"
                ],  function($, uiRegistry){
                        jQuery(document).ready(function($) {
                            var tree = $(".rule-tree");
                            var displaycategory = $(".field-display_to_category");
                            tree.hide();
                            displaycategory.hide();
                            var label=$("#rule_block_position :selected").parent().attr("label");
                            switch (label){
                                case "Home Page":
                                    tree.hide();
                                    displaycategory.hide();
                                    break;
                                case "Product Page":
                                    tree.show();
                                    displaycategory.hide();
                                    break;
                                case "Shopping Cart Page":
                                    tree.hide();
                                    displaycategory.hide();
                                    break;
                                case "Category Page":
                                    tree.hide();
                                    displaycategory.show();
                                    break;
                                default:
                                    tree.hide();
                                    displaycategory.hide();
                            }
                            $("#rule_block_position").on("change", function ()
                            {
                                var label=$("#rule_block_position :selected").parent().attr("label");
                                switch (label){
                                    case "Home Page":
                                        tree.hide();
                                        displaycategory.hide();
                                        break;
                                    case "Product Page":
                                        tree.show();
                                        displaycategory.hide();
                                        break;
                                    case "Shopping Cart Page":
                                        tree.hide();
                                        displaycategory.hide();
                                        break;
                                    case "Category Page":
                                        tree.hide();
                                        displaycategory.show();
                                        break;
                                    default:
                                        tree.hide();
                                        displaycategory.hide();
                                }
                            });
                        })
                })
                </script>
            '
        );

        return parent::_prepareForm();
    }
}
