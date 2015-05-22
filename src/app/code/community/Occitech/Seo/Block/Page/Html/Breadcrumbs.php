<?php

class Occitech_Seo_Block_Page_Html_Breadcrumbs extends Mage_Page_Block_Html_Breadcrumbs
{
    protected function _toHTML()
    {
        $html = parent::_toHTML();
        $helper = Mage::helper('occitech_seo');

        if (strpos($html, $helper->itemprop('breadcrumb')) === false) {
            $html = sprintf('<div %s>%s</div>', $helper->itemprop('breadcrumb'), $html);
        }

        return $html;
    }
}
