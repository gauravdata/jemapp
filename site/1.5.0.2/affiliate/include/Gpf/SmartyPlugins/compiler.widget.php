<?php
/**
 * Smarty plugin
 * @package GwtPhpFramework
 */


/**
 * Smarty {widget} compiler plugin
 *
 * Type:     compiler<br>
 * Name:     widget<br>
 *
 * Examples:
 * <pre>
 * {compiler id="WidgetId" class="CssClass"}
 * </pre>
 * @author   Andrej Harsani, Michal Bebjak   
 * @param    array
 * @param    Smarty
 * @return   string
 */
function smarty_compiler_widget($tag_attrs, &$compiler) {
    $_params = $compiler->_parse_attrs($tag_attrs);
    
    if (empty($_params['id'])) {
        $compiler->_syntax_error("widget: missing 'id' parameter");
        return;
    } else {
        $id = trim($_params['id'], "'");
    }
    $cssClass = "";
    if (!empty($_params['class'])) {
        $cssClass = ' class=\"' . trim($_params['class'], "'") . '\"';
    } 

    return 'echo "<div id=\"' . $id . '\"' . $cssClass . '></div>";';
}

?>
