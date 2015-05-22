<?php

class Occitech_Seo_Block_Contact extends Mage_Core_Block_Template
{
	const CACHE_TAG = 'occitech_contact';
	const CACHE_TIME = 86400;

	protected $_template = 'occitech/seo/contact.phtml';

	protected function _construct()
	{
		parent::_construct();
		$this->addData(array(
			'cache_lifetime' => self::CACHE_TIME,
			'cache_tags'  => array(Mage_Core_Model_Store_Group::CACHE_TAG, self::CACHE_TAG),
		));
	}

	public function getCacheKeyInfo()
	{
		return array(
			self::CACHE_TAG,
			$this->getNameInLayout(),
			Mage::app()->getStore()->getId(),
			Mage::getDesign()->getPackageName(),
			Mage::getDesign()->getTheme('template'),
		);
	}


}
