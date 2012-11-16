<?php

class Flagbit_Seosimpleproduct_Model_Observer
{

    /**
     * Get the current product and check if it is a simple product. If is so, load the parent configurable product and return it
     * so the frontend will show the configurable product.
     * Event: catalog_controller_product_init_before
     *
     * @param Varien_Event_Observer $observer
     */
    public function redirectSimpleToConfig(Varien_Event_Observer $observer)
    {
        // get product wich added to cart
        $_productId = $observer->getEvent()->getControllerAction()->getRequest()->getParam('id');

        $product = Mage::getModel('catalog/product')->setStoreId(Mage::app()->getStore()->getId())->load($_productId);

        Mage::register('seosimple_product', $product);

        # parameters of simple product
        $confParams = array("conf_size" => $product->getData('conf_size'), "conf_color" => $product->getData('conf_color'), "conf_material" => $product->getData('conf_material'));

        # If product is simple, get the configurable of it
        if ($product->type_id == "simple") {
            $parentId = $product->loadParentProductIds()->getData('parent_product_ids');

            if (isset($parentId[0])) {
                $oldproduct = $product;
                $product = Mage::getModel('catalog/product')->load($parentId[0]);
                if ($product->getStatus() == "2") {
                    $product = $oldproduct;
                    unset($oldproduct);
                } else {
                    unset($oldproduct);
                }
            }
        }

        # set configurable productId as id so the controller-function use the new id
        $observer->getEvent()->getControllerAction()->getRequest()->setParam('id', $product->getId());

        # set the parameters to set at configurable product by default
        foreach ($confParams as $key => $value) {
            $observer->getEvent()->getControllerAction()->getRequest()->setParam($key, ($value?$value:0));
        }
    }
}
