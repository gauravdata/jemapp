<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     resource.template.php
 * Type:     resource
 * Name:     template
 * Purpose:  Fetches templates from a template given to Smarty object
 * -------------------------------------------------------------
 */
function smarty_resource_template_source($tpl_name, &$tpl_source, &$smarty) {
    $tpl_source = $smarty->getTemplateFromFile($tpl_name);
    return true;
}

function smarty_resource_template_timestamp($tpl_name, &$tpl_timestamp, &$smarty) {
    $tpl_timestamp = $smarty->getTemplateTimestamp($tpl_name);
    return true;
}

function smarty_resource_template_secure($tpl_name, &$smarty) {
    // assume all templates are secure
    return true;
}

function smarty_resource_template_trusted($tpl_name, &$smarty) {
    // not used for templates
}
?> 
