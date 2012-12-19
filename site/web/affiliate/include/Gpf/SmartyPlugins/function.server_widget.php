<?php
/**
 * Smarty plugin
 * @package GwtPhpFramework
 */


/**
 * Smarty {server_widget} function plugin
 *
 * Type:     function<br>
 * Name:     server_widget<br>
 *
 * Examples:
 * <pre>
 * {server_widget class="Gpf_Ui_ServerWidget"}
 * </pre>
 * @author   Andrej Harsani, Michal Bebjak   
 * @param    array
 * @param    Smarty
 * @return   string
 */
function smarty_function_server_widget($params, &$smarty) {
    if (empty($params['class'])) {
        $smarty->trigger_error("server_widget: missing 'class' parameter");
        return;
    } else {
        $class = $params['class'];
    }
    
    return $smarty->renderServerWidget($class);
}

?>
