<?php
/**
 * Magiccart 
 * @category    Magiccart 
 * @copyright   Copyright (c) 2014 Magiccart (http://www.magiccart.net/) 
 * @license     http://www.magiccart.net/license-agreement.html
 * @Author: DOng NGuyen<nguyen@dvn.com>
 * @@Function:
 */
namespace Magiccart\Lookbook\Model\Adminhtml\System\Config;

class Layout implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value' => 'grid',   	'label'=>__('Grid')),
            array('value' => 'tab',   'label'=>__('Tab')),
               
        );
    }
}