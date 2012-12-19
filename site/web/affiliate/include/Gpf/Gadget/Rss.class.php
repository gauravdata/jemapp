<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Uwa.class.php 18112 2008-05-20 07:17:10Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Gadget_Rss extends Gpf_Gadget  {

    public function __construct() {
        parent::__construct();
        $this->setType('R');
    }

    protected function getTemplateName() {
        return "gadget_rss.stpl";
    }

    public function toText() {
        if ($this->panelWidth == 0) {
            $this->panelWidth = $this->getWidth();
        }
        if ($this->panelHeight == 0) {
            $this->panelHeight = $this->getHeight();
        }
        $template = new Gpf_Templates_Template($this->getTemplateName());
        $template->setDelimiter('/*{', '}*/');
        $template->assign('id', $this->getId());
        $template->assign('name', $this->getName());
        $template->assign('url', $this->getUrl());
        $template->assign('width', $this->getWidth());
        $template->assign('height', $this->getHeight());
        $template->assign('panelWidth', $this->panelWidth - 10);
        $template->assign('panelHeight', $this->panelHeight - 10);
        $template->assign('properties', $this->getProperties());
        $template->assign('rssEntries', $this->getRssEntries());
        $template->assign('rssEntryImgUrl', Gpf_Paths::getInstance()->getImageUrl('icon-rss-small.png'));
        $template->assign('rssImgUrl', Gpf_Paths::getInstance()->getImageUrl('icon-rss-big.png'));
        return $template->getHTML();
    }

    private function getRssEntries() {
        //download RSS
        require_once "XML/RSS.php";

        $rss = new XML_RSS($this->getUrl(), 'UTF-8', 'UTF-8');
        if (PEAR::isError($rss)) {
            return "Failed to read RSS feed";
        }

        if (!$rss->parse()) {
            return "Failed to parse RSS feed";
        }

        $str = "<ul class='rssEntries'>\n";

        foreach ($rss->getItems() as $item) {
            if (!isset($item['description'])) {
                $item['description'] = '';
            }
            if (!isset($item['pubdate'])) {
                $item['pubdate'] = '';
            }
            
            $str .= "<li class='rssEntry'><a class='rssLink' href=\"" . $item['link'] . "\" target=\"blank\">" . 
            $item['title'] . "</a><div class='rssPublished'>" . $item['pubdate'] . "</div><div class=\"rssDescription\">" . 
            $item['description'] . "</div></li>\n";
        }

        $str .= "</ul>\n";
        return $str;
    }


    public function loadConfiguration($configurationContent) {
        require_once "XML/RSS.php";

        $rss = new XML_RSS($configurationContent, 'UTF-8', 'UTF-8');
        if (PEAR::isError($rss)) {
            throw new Gpf_Exception("Not rss feed gadget");
        }

        $this->setWidth(400);
        $this->setHeight(300);
    }
}
?>
