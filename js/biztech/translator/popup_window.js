
function getPopupHTML(elId, field, languageToFullName, langFullName){

    html = '<div class="popup-div dialog popup-window" id="admin-popup-'+ field.id+'">';
    html += '<div class="top table_window"><div class="magento_title">'+Translator.translate('Biztech Translator:')+'</div></div>';
    html += '<div class="magento_close" id="widget_window_close" onclick="closeWindow(\''+elId+'\')"> </div>';
    html += '<div class="magento_content"><div class="popup-content">'+langFullName+'</div><div class="popup-content-right">'+languageToFullName+'</div><textarea readonly="true" class="translated-textarea" id="admin-popup-translated-text-'+elId+'" style="width:390px !important;"></textarea>';
    html += '<textarea readonly="true" class="old-text" id="old-text-'+elId+'" style="width: 340px !important;"></textarea>';
    html += '<button onclick="saveTranslate(\''+elId+'\')" title="'+Translator.translate("Apply Translate")+'" type="button" class="scalable btn-apply-translate"><span><span><span>'+Translator.translate("Apply Translate")+'</span></span></span></button>';
    html += '</div></div>';
    return html;
}


function getPopupReviewHTML(elId, field, languageToFullName, langFullName){

    html = '<div class="popup-div dialog popup-window" id="admin-popup-'+ field.id+'">';
    html += '<div class="top table_window"><div class="magento_title">'+Translator.translate('Biztech Translator:')+'</div></div>';
    html += '<div class="magento_close" id="widget_window_close" onclick="closeWindow(\''+elId+'\')"> </div>';
    html += '<div class="magento_content"><div class="popup-content">'+langFullName+'</div><div class="popup-content-right">'+languageToFullName+'</div><textarea readonly="true" class="translated-textarea" id="admin-popup-translated-text-'+elId+'" style="width:390px !important;"></textarea>';
    html += '<textarea readonly="true" class="old-text" id="old-text-'+elId+'" style="width: 340px !important;"></textarea>';
    html += '<button onclick="saveReviewTranslate(\''+elId+'\')" title="'+Translator.translate("Apply Translate")+'" type="button" class="scalable btn-apply-translate"><span><span><span>'+Translator.translate("Apply Translate")+'</span></span></span></button>';
    html += '</div></div>';
    return html;
}

function saveTranslate(ele){
    if(!tinyMCE.get(ele)){
        $(ele).value = $('admin-popup-translated-text-'+ele).value;    
    }else{
        $(ele).value = tinyMCE.get(ele).setContent($('admin-popup-translated-text-'+ele).value);
    }
    if($(ele+'_default')){
        if($(ele+'_default').checked == true){
            $(ele+'_default').click();
        }
    }
    closeWindow(ele);
}

function saveReviewTranslate(ele){

    $(ele).value = $('admin-popup-translated-text-'+ele).value;    
    if($(ele+'_default')){
        if($(ele+'_default').checked == true){
            $(ele+'_default').click();
        }
    }
    closeWindow(ele);
}

function closeWindow(ele){
    $('admin-popup-'+ele).style.display = 'none';
    //$('admin-popup-overlay').hide();

    if($('page_tabs_content_section_content')){
        if($('page_tabs_content_section_content').getStyle('display')=='block')
            $('admin-popup-overlay').hide();
        else
            $('admin-popup-overlay-meta').hide();
    }else{
        $('admin-popup-overlay').hide();
    }

    /*if($('page_tabs_content_section_content')){
        if($('page_tabs_content_section_content').getStyle('display')=='block')
            $('admin-popup-overlay').hide();
        else
            $('admin-popup-overlay-main').hide();
    }else{
        $('admin-popup-overlay').hide();
    }*/
}