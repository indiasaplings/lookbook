<?php
/**
 * 
 * @category: Magepow
 * @Copyright (c) 2014 Magepow  (<https://www.magepow.com>)
 * @authors: Magepow (<magepow<support@magepow.com>>)
 * @date:    2021-05-11 10:21:55
 * @license: <http://www.magepow.com/license-agreement>
 * @github: <https://github.com/magepow> 
 */
namespace Magiccart\Lookbook\Model\Adminhtml\System\Config;


class Col  implements \Magento\Framework\Option\ArrayInterface {
    
  public function toOptionArray()
    {
        return [
            '1'=>   __('1 item(s) /row'),
            '2'=>   __('2 item(s) /row'),
            '3'=>   __('3 item(s) /row'),
            '4'=>   __('4 item(s) /row'),
            '5'=>   __('5 item(s) /row'),
            '6'=>   __('6 item(s) /row'),
            '7'=>   __('7 item(s) /row'),
            '8'=>   __('8 item(s) /row'),
            '9'=>   __('9 item(s) /row'),
        ];
    }
}