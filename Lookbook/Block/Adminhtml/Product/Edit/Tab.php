<?php
namespace Magiccart\Lookbook\Block\Adminhtml\Product\Edit;

class Tab extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('lookbook_product_edit_tab');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Lookbook Pro'));
    }
}