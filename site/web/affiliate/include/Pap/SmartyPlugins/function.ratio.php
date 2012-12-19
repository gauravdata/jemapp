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
 * {ratio p1='25' p2='35'}
 * </pre>
 * @author   Andrej Harsani, Michal Bebjak   
 * @param    array
 * @param    Smarty
 * @return   string
 */
function smarty_function_ratio($params, &$smarty) {
	if ($params['p2']!=0) {
		return round($params['p1']/$params['p2'],Pap_Common_Utils_CurrencyUtils::getDefaultCurrencyPrecision());
	} else {
		echo '';
	}
}

?>
