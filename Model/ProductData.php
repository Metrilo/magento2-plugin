<?php

namespace Metrilo\Analytics\Model;

class ProductData
{
    public $chunkItems = \Metrilo\Analytics\Helper\Data::chunkItems;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
    ) {
        $this->productCollection = $productCollection;
    }

    public function getProductQuery($storeId)
    {
        return $this->productCollection
                    ->create()
                    ->addUrlRewrite()
                    ->addAttributeToSelect(['entity_id','type_id','sku','created_at','updated_at','name','image','price','request_path', 'visibility'])
                    ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                    ->addStoreFilter($storeId)
                    ->setDataToAll('store_id', $storeId);
    }

    public function getProducts($storeId, $chunkId)
    {
        return $this->getProductQuery($storeId)
                    ->setPageSize($this->chunkItems)
                    ->setCurPage($chunkId + 1);
    }
    
    public function getProductChunks($storeId)
    {
        $totalProducts = $this->getProductQuery($storeId)->getSize();
        return (int) ceil($totalProducts / $this->chunkItems);
    }
    
    public function getProductWithRequestPath($productId, $storeId) {
        $productObject = $this->productCollection
            ->create()
            ->addStoreFilter($storeId)
            ->addAttributeToSelect(['name','price','image'])
            ->joinTable(
                ['url' => 'url_rewrite'],
                'entity_id = entity_id',
                ['request_path', 'store_id', 'metadata'],
                ['entity_id' => $productId,
                    'entity_type' => 'product',
                    'store_id' => $storeId,
                    'metadata' => array('null' => true)]
            )
            ->getFirstItem();
        
        $productObject->setStoreId($storeId);
        
        return $productObject;
    }
}