<?php
/**
 * Magiccart 
 * @lookbook    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Create Date: 2018-05-16 10:40:51
 * @@Modify Date: 2018-06-27 16:11:41
 * @@Function:
 */

namespace Magiccart\Lookbook\Block\Adminhtml\Product\Edit\Tab;


class Content extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;
    /**
     * @var \Magiccart\Lookbook\Model\Lookbook
     */

    protected $_lookbook;
    protected $_yesNo;
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;
    protected $_trueFalse;
    protected $_row;
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_wysiwygConfig;
    protected $_storeManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Config\Model\Config\Source\Yesno $yesNo,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        \Magiccart\Lookbook\Model\Lookbook $lookbook,
        \Magiccart\Lookbook\Model\Adminhtml\System\Config\Col $row,
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_lookbook = $lookbook;
        $this->_yesNo = $yesNo;
        $this->json       = $json;
        $this->_trueFalse = ['true' => __('True'), 'false' => __('False')];
        $this->_row = $row;
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_storeManager  = $context->getStoreManager();
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('lookbook');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('magic_');

        $fieldset = $form->addFieldset('content_list_lookbook_fieldset', ['legend' => __('Content List LookBook Slider Configuration')]);

        if ($model->getId()) {
            $fieldset->addField('lookbook_id', 'hidden', ['name' => 'lookbook_id']);
        }

        $fieldset->addField('slide', 'select',
            [
                'label' => __('Slide'),
                'title' => __('Slide'),
                'name' => 'slide',
                'options' => $this->_yesNo->toArray(),
                'value' => 1,
            ]
        );

        // Option with value TRUE or FALSE
        $vertical = $fieldset->addField('vertical', 'select',
            [
                'label' => __('Slide Vertical'),
                'title' => __('Slide Vertical'),
                'name' => 'vertical',
                'options' => $this->_trueFalse,
                'value' => 'false',
            ]
        );

        $fieldset->addField('vertical-Swiping', 'select',
            [
                'label' => __('Vertical Swiping'),
                'title' => __('Vertical Swiping'),
                'name' => 'vertical-Swiping',
                'options' => $this->_trueFalse,
                'value' => 'false',
            ]
        );

        

        $fieldset->addField('infinite', 'select',
            [
                'label' => __('Infinite'),
                'title' => __('Infinite'),
                'name' => 'infinite',
                'options' => $this->_trueFalse,
            ]
        );

        $fieldset->addField('autoplay', 'select',
            [
                'label' => __('Auto Play'),
                'title' => __('Auto Play'),
                'name' => 'autoplay',
                'options' => $this->_trueFalse,
            ]
        );        

        $fieldset->addField('arrows', 'select',
            [
                'label' => __('Arrows'),
                'title' => __('Arrows'),
                'name' => 'arrows',
                'options' => $this->_trueFalse,
            ]
        );

        $fieldset->addField('dots', 'select',
            [
                'label' => __('Dots'),
                'title' => __('Dots'),
                'name' => 'dots',
                'options' => $this->_trueFalse,
                'value' => 'false',
            ]
        );

        $fieldset->addField(
            'adaptive-height', 
            'select',
            [
                'label' => __('Adaptive Height'),
                'title' => __('Adaptive Height'),
                'name' => 'adaptive-height',
                'options' => $this->_trueFalse,
                'value' => 'false',
            ]
        );

        $fieldset->addField(
            'center-mode', 
            'select',
            [
                'label' => __('Center Mode'),
                'title' => __('Center Mode'),
                'name' => 'center-mode',
                'options' => $this->_trueFalse,
                'value' => 'false',
            ]
        );

        $fieldset->addField(
            'fade', 
            'select',
            [
                'label' => __('Fade'),
                'title' => __('Fade'),
                'name' => 'fade',
                'options' => $this->_trueFalse,
                'value' => 'false',
            ]
        );

        $fieldset->addField('slides-to-show', 'select',
            [
                'label' => __('Slides to show'),
                'title' => __('Slides to show'),
                'name' => 'slides-to-show',
                'options' => $this->_row->toOptionArray(),
                'value' => '4',
            ]
        );

        $fieldset->addField('rows', 'select',
            [
                'label' => __('Rows'),
                'title' => __('Rows'),
                'name' => 'rows',
                'options' => $this->_row->toOptionArray(),
                'value' => '1',
            ]
        );

        // End option with value TRUE or FALSE

        // Option Text
        $fieldset->addField('speed', 'text',
            [
                'label' => __('Speed'),
                'title' => __('Speed'),
                'name'  => 'speed',
                'required' => true,
                'class' => 'validate-zero-or-greater',
                'value' => 300,
            ]
        );

        $fieldset->addField('autoplay-speed', 'text',
            [
                'label' => __('Autoplay Speed'),
                'title' => __('Autoplay Speed'),
                'name'  => 'autoplay-speed',
                'required' => true,
                'class' => 'validate-zero-or-greater',
                'value' => 3000,
            ]
        );

        $fieldset->addField('padding', 'text',
            [
                'label' => __('Padding'),
                'title' => __('Padding'),
                'name'  => 'padding',
                'required' => true,
                'class' => 'validate-zero-or-greater',
                'value' => 15,
            ]
        );

        
        $form->addValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Content List LookBook Slider Configuration');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
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

}
