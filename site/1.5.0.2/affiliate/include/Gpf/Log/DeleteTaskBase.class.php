<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Milos Jancovic
 *   @package PostAffiliatePro
 *   @since Version 1.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
abstract class Gpf_Log_DeleteTaskBase extends Gpf_Tasks_LongTask {

    /**
     * @param $daysCount
     * @return Gpf_DateTime
     */
    protected function getLastDate($daysCount) {
        $date = new Gpf_DateTime();
        $date->addDay($daysCount * -1);
        return $date;
    }
}
