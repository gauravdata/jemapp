<?php
/**
 * Smarty plugin
 * @package GwtPhpFramework
 */


/**
 * Smarty |date_span modifier
 *
 * Type:     function<br>
 * Name:     localize<br>
 *
 * Examples:
 * <pre>
 * {$dateInserted|date_span}
 * </pre>
 * @author   Michal Bebjak
 * @param    string
 * @return   string
 */
function smarty_modifier_date($date)
{
    return Gpf_Common_DateUtils::formatByUnit(new Gpf_DateTime(strtotime($date)), '');
}
?>
