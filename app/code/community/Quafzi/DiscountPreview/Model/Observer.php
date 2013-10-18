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
        $block  = $observer->getBlock();
        $helper = Mage::helper('quafzi_discountpreview');
        $helper->setProduct($block->getProduct());

        $block->setTemplate('discountpreview/discount.phtml');
        $block->setDiscountPercent($helper->getDiscountPercent());
        $block->setDiscountAmount($helper->getDiscountAmount());


        $container = $observer->getContainer();
        $html = $container->getHtml() . $block->toHtml();
        $container->setHtml($html);
    }

    protected function _getDiscountInfo(Mage_Catalog_Model_Product $_product)
    {
        $tmpQuoteItem = Mage::getModel('sales/quote_item');
        $tmpQuoteItem->setProduct($_product);

        $tmpQuote = Mage::getModel('sales/quote');
        $tmpQuote
            ->getBillingAddress()
            ->addItem($tmpQuoteItem);
        $tmpQuote->addItem($tmpQuoteItem);

        $ruleValidator = Mage::getModel('salesrule/validator');
        $ruleValidator->init(
            Mage::app()->getStore()->getWebsiteId(),
            Mage::helper('customer')->getCustomer()->getGroupId(),
            null
        );
        $tmpQuote->collectTotals();
        $ruleValidator->process($tmpQuoteItem);

        if ($tmpQuoteItem->getDiscountAmount()) {
            return sprintf(
                'Sie erhalten %s Rabatt auf diesen Preis!',
                Mage::helper('core')->formatPrice($tmpQuoteItem->getDiscountAmount())
            );
        } elseif ($tmpQuoteItem->getDiscountPercent()) {
            return sprintf(
                'Sie erhalten %s%% Rabatt auf diesen Preis!',
                $tmpQuoteItem->getDiscountPercent()
            );
        }
        return 'Sie erhalten keinen Rabatt';
    }
}
