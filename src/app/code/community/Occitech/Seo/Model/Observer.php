<?php

class Occitech_Seo_Model_Observer
{
	protected $_pageVarName = 'p';

	public function setCorrectCategoryCanonical()
	{
		if (Mage::helper('catalog/category')->canUseCanonicalTag() && $headBlock = $headBlock = Mage::app()->getLayout()->getBlock('head')) {
			$currentPage = $this->_getCurrentPage();

			if ($currentPage > 1) {
				// See Mage_Catalog_Block_Category_View:53
				$headBlock->removeItem('link_rel', Mage::registry('current_category')->getUrl());
				$headBlock->addLinkRel('canonical', $this->_getCanonicalUrl($currentPage));
			}
		}
	}

	protected function _getCanonicalUrl($currentPage)
	{
		$urlParams = array(
			'_current' => false,
			'_use_rewrite' => true,
			'_query' => array(
				$this->_pageVarName => $currentPage
			)
		);
		return $this->__getUrl('*/*/*', $urlParams);
	}

	protected function _getCurrentPage()
	{
		if ($page = (int) $this->__getRequest()->getParam($this->_pageVarName)) {
			return $page;
		}
		return 1;
	}

	private function __getRequest()
	{
		return Mage::app()->getRequest();
	}

	private function __getUrl($route, $params = array())
	{
		return Mage::getModel('core/url')->getUrl($route, $params);
	}
}
