<?php
/**
 * @package    Quafzi_DiscountPreview
 * @copyright  Copyright (c) 2014 Thomas Birke
 * @author     Thomas Birke <tbirke@netextreme.de>
 * @license    MIT
 */

/**
 * Discount Preview Block
 *
 * @category   Catalog
 * @package    Quafzi_DiscountPreview
 * @author     Thomas Birke <tbirke@netextreme.de>
 */
class Quafzi_DiscountPreview_Block_Catalog_Product_Discount
    extends Mage_Core_Block_Template
{
    protected $_template = 'discountpreview/discount.phtml';

    /**
     * cache this block depending on product and customer group
     *
     * @return string
     */
    public function getCacheKey()
    {
        return 'DISCOUNT_PREVIEW_'
            . Mage::getSingleton('customer/session')->getCustomerGroupId()
            . '_FOR_PRODUCT_'
            . $this->getProduct()->getId();
    }

    public function getCacheLifetime()
    {
        return 24 * 3600;
    }

    public function getCacheTags()
    {
        return array(Mage_Catalog_Model_Product::CACHE_TAG);
    }

    /**
     * Calculate discount
     */
    public function _toHtml()
    {
        $helper = Mage::helper('quafzi_discountpreview');
        $helper->setProduct($this->getProduct());
        $this->setDiscountPercent($helper->getDiscountPercent());
        $this->setDiscountAmount($helper->getDiscountAmount());
        return parent::_toHtml();
    }
}
