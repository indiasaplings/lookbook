<?php
/**
 * 
 * @category: Magepow
 * @Copyright (c) 2014 Magepow  (<https://www.magepow.com>)
 * @authors: Magepow (<magepow<support@magepow.com>>)
 * @date:    2021-04-27 13:45:02
 * @license: <http://www.magepow.com/license-agreement>
 * @github: <https://github.com/magepow> 
 */
namespace Magiccart\Lookbook\Block;

class Lookbook extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Widget\Block\BlockInterface
{
  
    protected $lookbookFactory;
    protected $_storeManager;
    protected $productCollectionFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    public $json;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magiccart\Lookbook\Model\LookbookFactory $lookbookFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magento\Framework\Serialize\Serializer\Json $json,
        array $data = []
    ) {
        $this->lookbookFactory          = $lookbookFactory;
        $this->_storeManager            = $storeManager; 
        $this->productCollectionFactory = $productCollectionFactory; 
        $this->json                     = $json;
        parent::__construct($context, $data);
    }


    public function getLookbookCollection()
    {
    	$store = $this->_storeManager->getStore()->getStoreId();
        $lookbook = $this->lookbookFactory->create()->getCollection()
                        ->addFieldToFilter('stores',array( array('finset' => 0), array('finset' => $store)))
                        ->addFieldToSelect('*')->addFieldToFilter('status', 1);

        return $lookbook;
    }


     public function getPinImageUrl($image)
    {
       $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).$image;
        return $resizedURL;
    }


    public function getProductCollection($producIds)
    {
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addAttributeToSelect('*')
                          ->addAttributeToFilter('entity_id', array('in' => $producIds))
                          ->addStoreFilter()
                          ->addMinimalPrice()
                          ->addFinalPrice()
                          ->addTaxPercents();
        return $productCollection;
     
     }

       /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getProductPrice(\Magento\Catalog\Model\Product $product)
    {
        $priceRender = $this->getPriceRender();

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }
      /**
     * Specifies that price rendering should be done for the list of products
     * i.e. rendering happens in the scope of product list, but not single product
     *
     * @return \Magento\Framework\Pricing\Render
     */
    protected function getPriceRender()
    {
        return $this->getLayout()->getBlock('product.price.render.default')
            ->setData('is_product_list', true);
    }
}