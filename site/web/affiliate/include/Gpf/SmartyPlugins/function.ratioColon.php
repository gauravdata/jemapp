<?php
/**
 * Smarty plugin
 * @package GwtPhpFramework
 */


/**
 * Smarty {localize} function plugin
 *
 * Type:     function<br>
 * Name:     localize<br>
 *
 * Examples:
 * <pre>
 * {ratioColon p1='25' p2='35'}
 * </pre>
 * @author   Andrej Harsani, Michal Bebjak   
 * @param    array
 * @param    Smarty
 * @return   string
 */
function smarty_function_ratioColon($params, &$smarty) {
    return $params['p1'] . ' : ' . $params['p2'];
}

?>
