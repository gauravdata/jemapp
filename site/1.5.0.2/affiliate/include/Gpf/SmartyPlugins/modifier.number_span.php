<?php
/**
 * Smarty plugin
 * @package GwtPhpFramework
 */


/**
 * Smarty |number_span modifier
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
function smarty_modifier_number_span($number)
{
    return '<span class="NumberData">'.Gpf_Common_NumberUtils::toStandardNumberFormat($number, 0).'</span>';
}
?>
