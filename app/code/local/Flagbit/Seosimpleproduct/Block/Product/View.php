<?php
/**
 * Product View block
 *
 * @category   Flagbit
 * @package    Flagbit_Seosimpleproduct_Block_Product_View
 * @author     Flagbit GmbH & Co. KG <magento@flagbit.de>
 */
class Flagbit_Seosimpleproduct_Block_Product_View extends Mage_Catalog_Block_Product_View
{
    
    /**
     * set metatitle with metatitle or name of the simple product if is set in Flagbit_Seosimpleproduct_Model_Observer. 
     * otherwise the metatitle or name of the configurable product. also dispatch a event when the layout is completely prepared.
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        # get simple product from registry if is set
        $seoSimple = Mage::registry('seosimple_product');
        if( isset($seoSimple) ) {
            $product = $seoSimple;
        } else {
            $product = $this->getProduct();
        }

        # load header
        $headBlock = $this->getLayout()->getBlock('head');
        $title = $product->getMetaTitle();

        if(!$title) { $title = /*'- '.*/$product->getName(); };
        if ($title) {
            $headBlock->setTitle($title);
        }
        
        Mage::dispatchEvent('catalog_product_view_prepare_layout_complete', array('product'=>$product));
    }
}

