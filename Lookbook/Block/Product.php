<?php
namespace Magiccart\Lookbook\Block;

use Magento\Framework\App\Filesystem\DirectoryList;

class Product extends \Magiccart\Lookbook\Block\Widget\Product
{
    public function getLookbook()
    {
        $store = $this->_storeManager->getStore()->getStoreId();
        $identifier = $this->getIdentifier();
        // var_dump($identifier);
        // die();
        $collection = $this->lookbookFactory->create()->getCollection()->addFieldToSelect('*')
                        ->addFieldToFilter('identifier', $identifier)
                        ->addFieldToFilter('type_id', $this->_typeId)
                        ->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)))
                        ->setPageSize(1);
        $collection->getSelect()->order('order','ASC');

        $this->_lookbook = $collection->getFirstItem();

        $config          = $this->_lookbook->getData('config');
        if($config){
            $config      = $this->json->unserialize($config);
            if(is_array($config))$this->_lookbook = $this->_lookbook->addData($config);
        }

        return  $this->_lookbook;
    }
}
