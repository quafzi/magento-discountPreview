<?php
/**
 * @package    Quafzi_DiscountPreview
 * @copyright  Copyright (c) 2013 Thomas Birke
 * @author     Thomas Birke <tbirke@netextreme.de>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Quafzi_DiscountPreview_Model_Observer
{
    public function blockCatalogProductGetPriceHtml(Varien_Object $observer)
    {
        try {
            $block  = $observer->getBlock();
            $helper = Mage::helper('quafzi_discountpreview');
            $helper->setProduct($block->getProduct());

            $block->setTemplate('discountpreview/discount.phtml');
            $block->setDiscountPercent($helper->getDiscountPercent());
            $block->setDiscountAmount($helper->getDiscountAmount());

            $container = $observer->getContainer();
            $html = $container->getHtml() . $block->toHtml();
            $container->setHtml($html);
        } catch (Exception $e) {
            //Debugging position
        }
    }
}
