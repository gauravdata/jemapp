<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Widget.class.php 18000 2008-05-13 16:00:48Z aharsani $
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
class Gpf_Ui_TemplatePanel extends Gpf_Ui_Widget {
    private $templateHtml;
    protected $templateName;

    public function __construct($templateName, $panel='') {
        parent::__construct(null);
        $this->templateName = $templateName;
        $template = new Gpf_Templates_Template($templateName, $panel);
        $this->templateHtml = $template->getHTML();
    }

    public function render() {
        return $this->templateHtml;
    }

    public function add($widget, $id) {
        $startDivIndex = strpos($this->templateHtml, '<div id="'. $id .'"');
        if (!$startDivIndex) {
            return;
        }
        $startHtml = substr($this->templateHtml, 0, $startDivIndex-1);

        $endHtml = substr($this->templateHtml, $startDivIndex);
        $endHtml = substr($endHtml, strpos($endHtml, '</div>')+6);

        $this->templateHtml = $startHtml.$widget.$endHtml;
    }

    public function addWidget($widget) {
        $this->templateHtml .= $widget;
    }

    public function containsId($id) {
        return strpos($this->templateHtml, '<div id="'. $id .'"') !== false;
    }
}

?>
