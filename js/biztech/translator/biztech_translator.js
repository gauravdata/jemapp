BiztechTranslatorForm = Class.create();
BiztechTranslatorForm.prototype = new varienForm();

BiztechTranslatorForm.prototype.initialize = (function(superConstructor) {

    return function(formId, BiztechTranslatorConfig) {

        superConstructor.call();

        BiztechTranslatorConfig = JSON.parse(BiztechTranslatorConfig);

        this.translateURL = BiztechTranslatorConfig.url;
        this.fullFromLanguageName = BiztechTranslatorConfig.fullFromLanguageName;
        this.languageToFullName = BiztechTranslatorConfig.languageToFullName;
        this.languageToCode = BiztechTranslatorConfig.languageToCode;
        this.fullFromCode = BiztechTranslatorConfig.fullFromCode;
        this.translateBtnText = BiztechTranslatorConfig.translateBtnText;

        this.popupOverlay = $('admin-popup-overlay');
        this.errorOverlay = $('error-overlay');

        if (BiztechTranslatorConfig.translatedFieldsNames) {
            translatedFieldsNames = BiztechTranslatorConfig.translatedFieldsNames.split(',');
            translatedFields = new Array();
            for (j = 0; j < translatedFieldsNames.size(); j++) {
                if ($(translatedFieldsNames[j])) {
                    translatedFields.push($(translatedFieldsNames[j]));
                }
            }

            var i = 0;
            translatedFields.each(function(field) {
                i++;
                var elId = field.readAttribute('id');
                popup = getPopupHTML(elId, field, this.languageToFullName, this.fullFromLanguageName);
                if (this.languageToCode == 'no-language' || this.languageToCode == 'null' || this.languageToCode == 'undefined')
                    button = "<span style='padding-right: 10px;'><i>" + Translator.translate('Select Language for this store in System->Config->Translator') + "</i></span>";
                else
                    button = "<button id=\"" + i + "\" title=\"" + Translator.translate('Translate to ') + this.languageToFullName + "\" type=\"button\" class=\"scalable btn-translate\" onclick=\"translator._submit('" + this.translateURL + "','" + elId + "')\" style=\"margin: 0px 5px 0px 0px;\"><span><span><span>" + Translator.translate(this.translateBtnText + ' ') + this.languageToFullName + "</span></span></span></button>";
                field.insert({after: button});
                field.insert({after: popup});
            }.bind(this));
        }

    };
})(varienForm.prototype.initialize);

BiztechTranslatorForm.prototype._submit = function(url, el) {



    var formdata = new Object();
    formdata['langto'] = this.languageToCode;
    formdata['langfrom'] = this.fullFromCode;
    formdata['id'] = el;
    if (tinyMCE.get(el)) {
        formdata['value'] = tinyMCE.get(el).getContent();
    }
    else {
        formdata['value'] = $(el).value;
    }


    new Ajax.Request(url, {
        method: 'post',
        parameters: formdata,
        onComplete: this._processResult.bind(this),
        onFailure: function() {
            this.errorOverlay.style.display = 'block';
            $('popup-error').style.display = 'block';
            $('error-popup-text').update(Translator.translate('Unknown Error!'));
        }
    });
}

BiztechTranslatorForm.prototype._processResult = function(transport) {

    var response = '';
    try {

        response = transport.responseText.evalJSON();
        if (response.status == 'success') {
            $('admin-popup-' + response.id).style.display = 'block';
            $('admin-popup-translated-text-' + response.id).update(response.value.text);
            if (($(response.id).value) == "") {
                if (tinyMCE.get(response.id)) {
                    $('old-text-' + response.id).update(tinyMCE.get(response.id).getContent());
                } else {
                    $('old-text-' + response.id).update($(response.id).value);
                }
            } else {
                $('old-text-' + response.id).update($(response.id).value);
            }
            this.popupOverlay.style.display = 'block';
        }
        else {
            this.errorOverlay.style.display = 'block';
            $('popup-error').style.display = 'block';
            $('error-popup-text').update(response.value.text);
        }
    }
    catch (e) {
        this.errorOverlay.style.display = 'block';
        $('popup-error').style.display = 'block';
        $('error-popup-text').update(e);
    }
};

BiztechTranslatorCmsPage = Class.create();
BiztechTranslatorCmsPage.prototype = new varienForm();
BiztechTranslatorCmsPage.prototype.initialize = (function(superConstructor) {
    return function(formId, BiztechTranslatorConfig) {

        superConstructor.call();
        BiztechTranslatorConfig = JSON.parse(BiztechTranslatorConfig);

        this.translateURL = BiztechTranslatorConfig.url;
        this.fullFromLanguageName = BiztechTranslatorConfig.fullFromLanguageName;
        this.languageToFullName = BiztechTranslatorConfig.languageToFullName;
        this.languageToCode = BiztechTranslatorConfig.languageToCode;
        this.fullFromCode = BiztechTranslatorConfig.fullFromCode;
        this.translateBtnText = BiztechTranslatorConfig.translateBtnText;

        this.popupOverlay = $('admin-popup-overlay');
        this.errorOverlay = $('error-overlay');

        if (BiztechTranslatorConfig.translatedFieldsNames) {
            translatedFieldsNames = BiztechTranslatorConfig.translatedFieldsNames.split(',');
            translatedFields = new Array();
            for (j = 0; j < translatedFieldsNames.size(); j++) {
                if ($(translatedFieldsNames[j])) {
                    translatedFields.push($(translatedFieldsNames[j]));
                }
            }
            var i = 0;
            translatedFields.each(function(field) {
                i++;

                var elId = field.readAttribute('id');
                popup = getPopupHTML(elId, field, this.languageToFullName, this.fullFromLanguageName);
                if (this.languageToCode == 'no-language' || this.languageToCode == 'null' || this.languageToCode == 'undefined')
                    button = "<span style='padding-right: 10px;'><i>" + Translator.translate('Select Language for this store in System->Config->Translator') + "</i></span>";
                else
                    button = "<button id=\"" + i + "\" title=\"" + Translator.translate('Translate to ') + this.languageToFullName + "\" type=\"button\" class=\"scalable btn-translate\" onclick=\"translator._submit('" + this.translateURL + "','" + elId + "')\" style=\"margin: 0px 5px 0px 0px;\"><span><span><span>" + Translator.translate(this.translateBtnText + ' ') + this.languageToFullName + "</span></span></span></button>";
                field.insert({after: button});
                field.insert({after: popup});
            }.bind(this));
        }



    };
})(varienForm.prototype.initialize);

BiztechTranslatorCmsPage.prototype._submit = function(url, el) {

    var formdata = new Object();
    formdata['langto'] = this.languageToCode;
    formdata['langfrom'] = this.fullFromCode;
    formdata['id'] = el;
    if (tinyMCE.get(el)) {
        formdata['value'] = tinyMCE.get(el).getContent();
    }
    else {
        formdata['value'] = $(el).value;
    }
    new Ajax.Request(url, {
        method: 'post',
        parameters: formdata,
        onComplete: this._processResult.bind(this),
        onFailure: function() {
            this.errorOverlay.style.display = 'block';
            $('popup-error').style.display = 'block';
            $('error-popup-text').update(Translator.translate('Unknown Error!'));
        }
    });
}

BiztechTranslatorCmsPage.prototype._processResult = function(transport) {
    var response = '';
    try {
        response = transport.responseText.evalJSON();
        if (response.status == 'success') {
            $('admin-popup-' + response.id).style.display = 'block';
            $('admin-popup-translated-text-' + response.id).update(response.value.text);
            if (($(response.id).value) == "") {
                if (tinyMCE.get(response.id)) {
                    $('old-text-' + response.id).update(tinyMCE.get(response.id).getContent());
                } else {
                    $('old-text-' + response.id).update($(response.id).value);
                }
            } else {
                $('old-text-' + response.id).update($(response.id).value);
            }

            if ($('page_tabs_content_section_content')) {
                if ($('page_tabs_content_section_content').getStyle('display') == 'block')
                    this.popupOverlay = $('admin-popup-overlay');
                else
                    this.popupOverlay = $('admin-popup-overlay-meta');
            } else {
                this.popupOverlay = $('admin-popup-overlay');
            }

            /*if ($('page_tabs_main_section_content')) {
                if ($('page_tabs_main_section_content').getStyle('display') == 'block')
                    this.popupOverlay = $('admin-popup-overlay');
                else
                    this.popupOverlay = $('admin-popup-overlay-main');
            } else {
                this.popupOverlay = $('admin-popup-overlay');
            }*/

            this.popupOverlay.style.display = 'block';
        }
        else {
            this.errorOverlay.style.display = 'block';
            $('popup-error').style.display = 'block';
            $('error-popup-text').update(response.value.text);
        }
    }
    catch (e) {
        this.errorOverlay.style.display = 'block';
        $('popup-error').style.display = 'block';
        $('error-popup-text').update(e);
    }
};



BiztechTranslatorReviewPage = Class.create();
BiztechTranslatorReviewPage.prototype = new varienForm();
BiztechTranslatorReviewPage.prototype.initialize = (function(superConstructor) {
    return function(formId, BiztechTranslatorConfig) {

        superConstructor.call();
        BiztechTranslatorConfig = JSON.parse(BiztechTranslatorConfig);

        this.translateURL = BiztechTranslatorConfig.url;
        this.fullFromLanguageName = BiztechTranslatorConfig.fullFromLanguageName;
        this.languageToFullName = BiztechTranslatorConfig.languageToFullName;
        this.languageToCode = BiztechTranslatorConfig.languageToCode;
        this.fullFromCode = BiztechTranslatorConfig.fullFromCode;
        this.translateBtnText = BiztechTranslatorConfig.translateBtnText;

        this.popupOverlay = $('admin-popup-overlay');
        this.errorOverlay = $('error-overlay');

        translatedFieldsNames = BiztechTranslatorConfig.translatedFieldsNames.split(',');
        translatedFields = new Array();
        for (j = 0; j < translatedFieldsNames.size(); j++) {
            if ($(translatedFieldsNames[j])) {
                translatedFields.push($(translatedFieldsNames[j]));
            }
        }
        var i = 0;
        translatedFields.each(function(field) {
            i++;

            var elId = field.readAttribute('id');
            popup = getPopupReviewHTML(elId, field, this.languageToFullName, this.fullFromLanguageName);
            if (this.languageToCode == 'no-language' || this.languageToCode == 'null' || this.languageToCode == 'undefined')
                button = "<span style='padding-right: 10px;'><i>" + Translator.translate('Select Language for this store in System->Config->Translator') + "</i></span>";
            else
                button = "<button id=\"" + i + "\" title=\"" + Translator.translate('Translate to ') + this.languageToFullName + "\" type=\"button\" class=\"scalable btn-translate\" onclick=\"translator._submit('" + this.translateURL + "','" + elId + "')\" style=\"margin: 0px 5px 0px 0px;\"><span><span><span>" + Translator.translate(this.translateBtnText + ' ') + this.languageToFullName + "</span></span></span></button>";
            field.insert({after: button});
            field.insert({after: popup});
        }.bind(this));


    };
})(varienForm.prototype.initialize);

BiztechTranslatorReviewPage.prototype._submit = function(url, el) {

    var formdata = new Object();
    formdata['langto'] = this.languageToCode;
    formdata['langfrom'] = this.fullFromCode;
    formdata['id'] = el;
    formdata['value'] = $(el).value;

    new Ajax.Request(url, {
        method: 'post',
        parameters: formdata,
        onComplete: this._processResult.bind(this),
        onFailure: function() {
            this.errorOverlay.style.display = 'block';
            $('popup-error').style.display = 'block';
            $('error-popup-text').update(Translator.translate('Unknown Error!'));
        }
    });
}

BiztechTranslatorReviewPage.prototype._processResult = function(transport) {
    var response = '';
    try {
        response = transport.responseText.evalJSON();
        if (response.status == 'success') {
            $('admin-popup-' + response.id).style.display = 'block';
            $('admin-popup-translated-text-' + response.id).update(response.value.text);
            $('old-text-' + response.id).update($(response.id).value);
            this.popupOverlay.style.display = 'block';
        }
        else {
            this.errorOverlay.style.display = 'block';
            $('popup-error').style.display = 'block';
            $('error-popup-text').update(response.value.text);
        }
    }
    catch (e) {
        this.errorOverlay.style.display = 'block';
        $('popup-error').style.display = 'block';
        $('error-popup-text').update(e);
    }
};