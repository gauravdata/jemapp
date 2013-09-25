<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: FormField.class.php 25424 2009-09-18 11:16:55Z rdohan $
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
class Gpf_DateTime_Range {
    /**
     *
     * @var Gpf_DateTime
     */
    private $from;
    
    /**
     *
     * @var Gpf_DateTime
     */
    private $to;
    
    public function __construct(Gpf_DateTime $from = null, Gpf_DateTime $to = null) {
        if($from === null && $to === null) {
            $this->setAllTime();
            return;
        }
        $this->from = $from;
        $this->to = $to;
    }
    
    public function isAllTime() {
        return $this->from->toTimeStamp() == Gpf_DateTime::min()->toTimeStamp()
            && $this->to->toTimeStamp() == Gpf_DateTime::min()->toTimeStamp();
    }
    
    public function setAllTime() {
        $this->from = Gpf_DateTime::min();
        $this->to = Gpf_DateTime::min();
    }
    
    /**
     *
     * @return Gpf_DateTime
     */
    public function getFrom() {
        return $this->from;
    }
    
    /**
     *
     * @return Gpf_DateTime
     */
    public function getTo() {
        return $this->to;
    }

    public function setFrom(Gpf_DateTime $from) {
        $this->from = $from;
    }

    public function setTo(Gpf_DateTime $to) {
        $this->to = $to;
    }
}

?>
