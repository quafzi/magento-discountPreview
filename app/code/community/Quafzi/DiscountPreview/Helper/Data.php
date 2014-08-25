<?php
/**
 * @package    Quafzi_DiscountPreview
 * @copyright  Copyright (c) 2013 Thomas Birke
 * @author     Thomas Birke <tbirke@netextreme.de>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Quafzi_DiscountPreview_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $discountPercent;
    protected $discountAmount;

    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_prepareDiscountInfo($product);
    }

    public function getDiscountPercent()
    {
        return $this->discountPercent;
    }

    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    protected function _prepareDiscountInfo($_product)
    {
        $this->discountAmount = null;
        $this->discountPercent = null;
        $_product->load($_product->getId());

        $tmpQuoteItem = Mage::getModel('sales/quote_item');
        $tmpQuoteItem->setProduct($_product);
        if (false == is_object($tmpQuoteItem)) {
            return;
        }
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

        if ($tmpQuoteItem->getDiscountPercent()) {
            $this->discountPercent = $tmpQuoteItem->getDiscountPercent();
            $this->discountAmount  = $this->discountPercent / 100 * $tmpQuoteItem->getProduct()->getPrice();
        }
        if ($tmpQuoteItem->getDiscountAmount()) {
            $this->discountAmount = $tmpQuoteItem->getDiscountAmount();
        }
    }
}
