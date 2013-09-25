<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: LengthConstraint.class.php 24476 2009-05-19 14:49:33Z mgalik $
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
class Gpf_DbEngine_Row_CompoundConstraint extends Gpf_Object implements Gpf_DbEngine_Row_Constraint {
    private $constraints;
    
    public function __construct() {
        $this->constraints = array();        
    }
    
    public function addConstrain(Gpf_DbEngine_Row_Constraint $constrain, $operator) {
        $this->constraints[] = array('constraint'=>$constrain, 'operator'=>$operator);
    }
    
    public function clearConstraints() {
        $this->constraints = array();
    }
    
    public function getAllConstraints() {
        return $this->constraits;    
    }
    
    public function getConstraint($id) {
        return $this->constraints[$id];     
    }
    
    public function validate(Gpf_DbEngine_Row $row) {
        $result = true;
        $exception = null;
        $counter = 0;
        foreach ($this->constraints as $constraint) {
            try {
                $constraint['constraint']->validate($row);
            } catch (Gpf_DbEngine_Row_ConstraintException $e) {
                $exception = $e;
            }
            if (($constraint['operator']=='AND')&&($exception!=null)) {
                throw $exception;
            } elseif (($constraint['operator']=='OR')&&($exception==null)) {
                return;
            } elseif (((count($this->constraints)-1)==$counter) && ($exception!=null)) {
                throw $exception;
            }
            $exception = null;
            $counter ++;
        }
        return;
    }
}
