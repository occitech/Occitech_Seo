<?php

class Occitech_Seo_Helper_Data extends Mage_Core_Helper_Abstract
{
	const SCHEMA_ORG_URL = 'http://schema.org';

	const ITEMSCOPE_ATTR = 'itemscope';
	const ITEMTYPE_ATTR = 'itemtype';
	const ITEMPROP_ATTR = 'itemprop';

	const WEBPAGE_TYPE = 'WebPage';
	const WEBSITE_TYPE = 'WebSite';
	const PRODUCT_TYPE = 'Product';

	public function itemscope($itemScopeValue = '')
	{
		return $this->getFormattedAttribute(self::ITEMSCOPE_ATTR, $itemScopeValue);
	}

	private function getFormattedAttribute($attributeName, $attributeValue = '')
	{
		$html = $attributeName;
		if (!empty($attributeValue)) {
			$html .= $this->__('="%s"', $attributeValue);
		}

		return $html;
	}

	public function itemtype($itemTypeValue)
	{
		return $this->getFormattedAttribute(self::ITEMTYPE_ATTR, $this->generateItemTypeUrl($itemTypeValue));
	}

	public function itemprop($itemPropertyValue = '')
	{
		return $this->getFormattedAttribute(self::ITEMPROP_ATTR, $itemPropertyValue);
	}

	public function webPageElementRoot()
	{
		return $this->itemscope() . ' ' . $this->itemtype(self::WEBPAGE_TYPE);
	}

	public function webSiteElementRoot()
	{
		return $this->itemscope() . ' ' . $this->itemtype(self::WEBSITE_TYPE);
	}

	private function generateItemTypeUrl($itemType)
	{
		return self::SCHEMA_ORG_URL . DIRECTORY_SEPARATOR . $itemType;
	}
}
