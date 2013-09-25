<?php
/**
 * Smarty plugin
 * @package GwtPhpFramework
 */


/**
 * Smarty |number modifier
 *
 * Type:     function<br>
 * Name:     localize<br>
 *
 * Examples:
 * <pre>
 * {$allCommission|number}
 * </pre>
 * @author   Michal Bebjak
 * @param    string
 * @return   string
 */
function smarty_modifier_number($number)
{
    return Gpf_Common_NumberUtils::toStandardNumberFormat($number, 0);
}
?>
