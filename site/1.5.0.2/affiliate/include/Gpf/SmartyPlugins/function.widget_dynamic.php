<?php
/**
 * Smarty plugin
 * @package GwtPhpFramework
 */


/**
 * Smarty {widget_dynamic} function plugin
 *
 * Type:     function<br>
 * Name:     widget_dynamic<br>
 *
 * 
 * @author   Andrej Harsani, Michal Bebjak   
 * @param    array
 * @param    Smarty
 * @return   string
 */
function smarty_function_widget_dynamic($params, &$smarty) {
    if (empty($params['id'])) {
        $smarty->trigger_error("widget_dynamic: missing 'id' parameter");
        return;
    } else {
        $id = $params['id'];
    }
    $cssClass = '';
    return '<div id="' . $id . '"' . $cssClass . '></div>';
}

?>
