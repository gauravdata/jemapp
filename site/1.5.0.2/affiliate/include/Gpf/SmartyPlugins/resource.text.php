<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     resource.text.php
 * Type:     resource
 * Name:     text
 * Purpose:  Fetches templates from a text given to Smarty object
 * -------------------------------------------------------------
 */
function smarty_resource_text_source($tpl_name, &$tpl_source, &$smarty) {
    $tpl_source = $smarty->getTemplateSource();
    return true;
}

function smarty_resource_text_timestamp($tpl_name, &$tpl_timestamp, &$smarty) {
    $tpl_timestamp = time();
    return true;
}

function smarty_resource_text_secure($tpl_name, &$smarty) {
    // assume all templates are secure
    return true;
}

function smarty_resource_text_trusted($tpl_name, &$smarty) {
    // not used for templates
}
?> 
