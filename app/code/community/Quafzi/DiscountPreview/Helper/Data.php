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
    protected $_item;

    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        if ('configurable' == $product->getTypeId()) {
            $childSelected = false;
            $children = $product->getTypeInstance()->getUsedProducts(null, $product);
            if (is_array($children) && count($children)) {
                foreach ($children as $childProduct) {
                    if ($childProduct->isSalable()) {
                        $product = $childProduct;
                        $childSelected = true;
                        break;
                    }
                }
            }

            if ($childSelected === false) {
                Mage::throwException('No in stock children for product %s found', $product->getId());
            }
        }
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

        $tmpQuote = $this->_getTemporaryQuote($_product);
        foreach ($tmpQuote->getItemsCollection() as $_item) {
            $this->_processCartRules($_item);
        }

        $item = $tmpQuote->getItemByProduct($_product);
        if ($item->getDiscountPercent()) {
            $this->discountPercent = $item->getDiscountPercent();
            $this->discountAmount  = $this->discountPercent / 100 * $item->getProduct()->getPrice();
        }
    }

    protected function _processCartRules($_item)
    {
        $ruleValidator = Mage::getModel('salesrule/validator');
        $ruleValidator->init(
            Mage::app()->getStore()->getWebsiteId(),
            Mage::helper('customer')->getCustomer()->getGroupId(),
            null
        );
        $ruleValidator->process($_item);

    }

    protected function _getTemporaryQuote($_product)
    {
        /** @var $tmpQuote Mage_Sales_Model_Quote */
        $tmpQuote = Mage::getModel('sales/quote');

        $cart = Mage::getSingleton('checkout/session')->getQuote();
        foreach ($cart->getItemsCollection() as $cartItem) {
            $item = $tmpQuote->addProductAdvanced($cartItem->getProduct(), $cartItem->getQty());
            if ($item instanceof Mage_Sales_Model_Quote_Item) {
                $tmpQuote->getShippingAddress()->addItem($item);
            }
        }
        $item = $tmpQuote->addProductAdvanced($_product, 1);
        if ($item instanceof Mage_Sales_Model_Quote_Item) {
            $tmpQuote->getShippingAddress()->addItem($item);
            $tmpQuote->collectTotals();
        }

        return $tmpQuote;
    }
}
