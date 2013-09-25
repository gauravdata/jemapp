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
 * {localize str='message'}
 * </pre>
 * @author   Andrej Harsani, Michal Bebjak   
 * @param    array
 * @param    Smarty
 * @return   string
 */
function smarty_function_localize($params, &$smarty) {
    if (empty($params['str'])) {
        $smarty->trigger_error("localize: missing 'str' parameter");
        return;
    } else {
        $str = stripslashes($params['str']);
    }
    return $smarty->localize(ltrim($str, "'"));
}

?>
