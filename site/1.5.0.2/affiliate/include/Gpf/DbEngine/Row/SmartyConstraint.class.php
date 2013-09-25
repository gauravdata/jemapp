<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Maros Galik
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
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
class Gpf_DbEngine_Row_SmartyConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {

    private $columnName;
    private $message;

    /**
     * @param array $columnNames
     */
    public function __construct($columnName, $message = "") {
        $this->columnName = $columnName;
        $this->message = $message;
    }

    /**
     * Validate Db_Row
     *
     * @param Gpf_DbEngine_Row $row
     * @throws Gpf_DbEngine_Row_ConstraintException
     */
    public function validate(Gpf_DbEngine_Row $row) {
        $template = new Gpf_Templates_Template($row->get($this->columnName), '', Gpf_Templates_Template::FETCH_TEXT);
        if (!$template->isValid()) {
            $this->throwException();
        }
    }

    private function throwException() {
        if ($this->message == "") {
            throw new Gpf_DbEngine_Row_ConstraintException($this->columnName,
            $this->columnName. $this->_(' has not valid Smarty Syntax. More information: ') .
            Gpf_Application::getInstance()->getHelpUrl() . 'troubleshooting/invalid-smarty-syntax.html');
        }
        throw new Gpf_DbEngine_Row_ConstraintException($this->columnName, $this->message);
    }
}
