function rendererConditions(el,url)
{
   return createRequest('customercredit_rules_edit_tabs_conditions_section_content',el.value,url);
}
function rendererActions(el,url)
{
   return createRequest('customercredit_rules_edit_tabs_actions_section_content',el.value,url);
}

function createRequest(elId,value,url)
{
    
    new Ajax.Updater(
        $(elId), 
        url, 
        { 
            method: "post", 
            evalScripts: true,
            parameters: {'current_rule_type':value}
        }
    );
}
