<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @package GwtPhpFramework
 *   @author Michal Bebjak
 *   @since Version 1.0.0
 *   $Id: Context.class.php 21019 2008-09-19 12:40:08Z mfric $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the 'License'); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package GwtPhpFramework
 */
class Gpf_Recurrence_Setting_Factory extends Gpf_Object {

    /**
     * @return Gpf_Recurrence_Setting
     */
    public static function getRecurrenceSetting(Gpf_Db_RecurrenceSetting $recurrenceSetting) {
        switch ($recurrenceSetting->getType()) {
            case Gpf_Db_RecurrenceSetting::TYPE_ONCE:
                return new Gpf_Recurrence_Setting_Once($recurrenceSetting);
            case Gpf_Db_RecurrenceSetting::TYPE_EACH:
                return new Gpf_Recurrence_Setting_Repeating($recurrenceSetting);                
            case Gpf_Db_RecurrenceSetting::TYPE_HOUR:
                return new Gpf_Recurrence_Setting_Hour($recurrenceSetting);
            case Gpf_Db_RecurrenceSetting::TYPE_DAY:
                return new Gpf_Recurrence_Setting_Day($recurrenceSetting);
            case Gpf_Db_RecurrenceSetting::TYPE_WEEK:
                return new Gpf_Recurrence_Setting_Week($recurrenceSetting);
            case Gpf_Db_RecurrenceSetting::TYPE_MONTH:
                return new Gpf_Recurrence_Setting_Month($recurrenceSetting);
            case Gpf_Db_RecurrenceSetting::TYPE_YEAR:
                return new Gpf_Recurrence_Setting_Year($recurrenceSetting);
        }
        return null;
    }

}
?>
