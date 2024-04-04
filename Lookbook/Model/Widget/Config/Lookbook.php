<?php

/**
 * @Author: nguyen
 * @Date:   2021-06-15 09:26:46
 * @Last Modified by:   nguyen
 * @Last Modified time: 2021-06-15 09:27:30
 */

namespace Magiccart\Lookbook\Model\Widget\Config;

class Product implements \Magento\Framework\Option\ArrayInterface
{
	protected $_lookbook;

	public function __construct(
		\Magiccart\Lookbook\Model\Lookbook $lookbook
	)
	{
		$this->_lookbook = $lookbook;
	}

    public function toOptionArray()
    {
		$lookbooks = $this->_lookbook->getCollection()
						->addFieldToFilter('status', '1');
		$options = [];
		foreach ($lookbooks as $item) {
			$options[$item->getIdentifier()] = $item->getTitle() ? $item->getTitle() : $item->getIdentifier();
		}

        return $options;
    }

}
