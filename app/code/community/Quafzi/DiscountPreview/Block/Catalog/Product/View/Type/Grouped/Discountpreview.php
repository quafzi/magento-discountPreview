<?php
/**
 * @package    Quafzi_DiscountPreview
 * @copyright  Copyright (c) 2014 Thomas Birke
 * @author     Thomas Birke <tbirke@netextreme.de>
 * @license    MIT
 */

/**
 * Grouped Product Discount Preview Block
 *
 * @category   Catalog
 * @package    Quafzi_DiscountPreview
 * @author     Thomas Birke <tbirke@netextreme.de>
 */
class Quafzi_DiscountPreview_Block_Catalog_Product_View_Type_Grouped_Discountpreview
	extends Mage_Catalog_Block_Product_View_Abstract
{
	/**
	 * Set the module translaton namespace
	 */
	public function _construct()
	{
		$this->setData('module_name', 'Mage_Catalog');
        $this->_calculateDiscount();
	}

	/**
     * Returns product price block html
     *
     * @param Mage_Catalog_Model_Product $product
     * @param boolean $displayMinimalPrice
     */
    protected function _calculateDiscount()
    {
        $firstChild = current($this->getProduct()->getTypeInstance()->getAssociatedProducts());

        $helper = Mage::helper('quafzi_discountpreview');
        $helper->setProduct($firstChild);
        $this->setDiscountPercent($helper->getDiscountPercent());

        foreach ($this->getProduct()->getTypeInstance()->getAssociatedProducts() as $child) {
            $helper->setProduct($firstChild);
            if ($helper->getDiscountPercent() < $this->getDiscountPercent()) {
                $this->setDiscountPercent($helper->getDiscountPercent());
                $this->setIsMinDiscount(true);
            }
        }
    }
}
