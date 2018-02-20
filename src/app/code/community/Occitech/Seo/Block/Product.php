<?php

class Occitech_Seo_Block_Product extends Mage_Core_Block_Template
{
	protected $_template = 'occitech/seo/microdata.phtml';
	const CACHE_TAG = 'occitech_microdata';
	const CACHE_TIME = 86400;
	const USE_MAGENTO_REVIEW = true;

	protected function _construct()
	{
		parent::_construct();
		$this->addData(array(
			'cache_lifetime' => self::CACHE_TIME,
			'cache_tags'  => array(Mage_Catalog_Model_Product::CACHE_TAG . '_' . $this->_getProduct()->getId(), Mage_Core_Model_Store_Group::CACHE_TAG, self::CACHE_TAG),
		));
	}

	protected function _getProduct()
	{
		return is_null($this->getProduct()) ? Mage::registry('current_product') : $this->getProduct();
	}

	public function getCacheKeyInfo()
	{
		return array(
			self::CACHE_TAG,
			$this->getNameInLayout(),
			Mage::app()->getStore()->getId(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template'),
			$this->_getProduct()->getId()
		);
	}

	public function getMicrodata()
	{
		$product = $this->_getProduct();

		$microdata = array(
			'name' => $product->getName(),
			'description' => $product->getShortDescription(),
			'image' => $product->getImageUrl(),
			'sku' => $product->getSku(),
			'offers' => array(
				array(
					'url' => 'http://schema.org/Offer',
					'props' => array(
						'price' => $this->_formatNumber($product->getFinalPrice()),
						'priceCurrency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
						'availability' => sprintf('http://schema.org/%sStock', $product->isAvailable() ? 'In' : 'OutOf'),
						'inventoryLevel' => $this->_formatNumber($product->getStockItem()->getQty()),
					),
				),
			),
			'reviews' => array()
		);
		$productReviews = $this->_getProductReview($product);

		if (self::USE_MAGENTO_REVIEW && !empty($productReviews)) {
			$microdata['aggregateRating'][] = array(
				'url' => 'http://schema.org/AggregateRating',
				'props' => array(
					'ratingValue' => $product->getRatingSummary()->getRatingSummary(),
					'reviewCount' => $product->getRatingSummary()->getReviewsCount(),
					'bestRating' => 100,
					'worstRating' => 0,
				),
			);

			foreach ($productReviews as $review) {
				$microdata['reviews'][] = array(
					'url' => 'http://schema.org/Review',
					'props' => array(
						'itemReviewed' => $product->getName(),
						'name' => $this->escapeHtml($review->getTitle()),
						'author' => $this->escapeHtml($review->getNickname()),
						'description' => $this->escapeHtml($review->getDetail()),
					),
				);
			}
		}

		if ($this->_isProductOnSale($product)) {
			$microdata = $this->_addSaleMicrodata($product, $microdata);
		}

		if ($this->_isConfigurableProduct($product)) {
			$microdata['offers'] = $this->_getOfferMicrodataForConfigurableProduct($product);
		}

		if ($this->_isGroupedProduct($product)) {
			$microdata['offers'] = $this->_getOfferMicrodataForGroupedProduct($product);
		}

		return $microdata;
	}

	public function sanitizeMetaContent($content)
	{
		return $this->quoteEscape($this->stripTags($content));
	}

	protected function _formatNumber($number)
	{
		return number_format($number, 2, '.', '');
	}

	protected function _getProductReview(Mage_Catalog_Model_Product $product)
	{
		return Mage::getModel('review/review')->getCollection()
			->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
			->addStoreFilter(Mage::app()->getStore()->getId())
			->addEntityFilter('product', $product->getId())
			->setDateOrder()
			->addRateVotes();
	}

	protected function _isProductOnSale(Mage_Catalog_Model_Product $product)
	{
		return $product->getFinalPrice() < $product->getPrice();
	}

	protected function _addSaleMicrodata(Mage_Catalog_Model_Product $product, array $microdata)
	{
		if ($product->getSpecialToDate()) {
			$microdata['offers'][0]['props']['priceValidUntil'] = $product->getSpecialToDate();
		}
		return $microdata;
	}

	protected function _isConfigurableProduct(Mage_Catalog_Model_Product $product)
	{
		return $product->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Configurable;
	}

	protected function _isGroupedProduct(Mage_Catalog_Model_Product $product)
	{
		return $product->getTypeInstance() instanceof Mage_Catalog_Model_Product_Type_Grouped;
	}

	protected function _getOfferMicrodataForConfigurableProduct(Mage_Catalog_Model_Product $product)
	{
		$children = $product->getTypeInstance()->getUsedProducts();
		return $this->generateMicrodataOfferFor($children);
	}

	protected function _getOfferMicrodataForGroupedProduct(Mage_Catalog_Model_Product $product)
	{
		$children = $product->getTypeInstance()->getAssociatedProducts();
		return $this->generateMicrodataOfferFor($children);
	}

	private function generateMicrodataOfferFor($children)
	{
		$offers = array();
		foreach ($children as $child) {
			$offers[] = array(
				'url' => 'http://schema.org/Offer',
				'props' => array(
					'price' => $this->_formatNumber($child->getFinalPrice()),
					'priceCurrency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
					'availability' => sprintf('http://schema.org/%sStock', $child->isAvailable() ? 'In' : 'OutOf'),
					'inventoryLevel' => $this->_formatNumber($child->getStockItem()->getQty()),
					'itemOffered' => array(
						'name' => $child->getName(),
						'description' => $child->getShortDescription(),
						'sku' => $child->getSku(),
					)
				),
			);
		}
		return $offers;
	}
}
