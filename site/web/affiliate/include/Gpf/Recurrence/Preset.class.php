<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Michal Bebjak
 *   @package GwtPhpFramework
 *   @since Version 1.0.0
 *   $Id: Gadget.class.php 19129 2008-07-15 09:35:17Z mjancovic $
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
class Gpf_Recurrence_Preset extends Gpf_Db_RecurrencePreset {

    /**
     * @param timestamp $lastTimestamp
     * @return next date timestamp or null if there is no other date
     */
    public function getNextDate($lastTimestamp) {
        if ($this->getStartDate() != null && $this->getStartDate() > Gpf_Common_DateUtils::getDateTime(time())) {
            return null;
        }
        if ($this->getEndDate() != null && $this->getEndDate() < Gpf_Common_DateUtils::getDateTime(time())) {
            return null;
        }
        $recurrenceSetting = new Gpf_Db_RecurrenceSetting();
        $recurrenceSetting->setRecurrencePresetId($this->getId());
        $nextDate = 0;
        foreach ($recurrenceSetting->loadCollection() as $recurrenceSetting) {
            $setting = Gpf_Recurrence_Setting_Factory::getRecurrenceSetting($recurrenceSetting);
            $settingNextDate = $setting->getNextDate($lastTimestamp);
            if ($nextDate < $settingNextDate) {
                $nextDate = $settingNextDate;
            }
        }
        if ($nextDate == 0) {
            return null;
        }
        return $nextDate;
    }   
}

?>
