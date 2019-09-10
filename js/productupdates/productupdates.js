var punDetect = navigator.userAgent.toLowerCase();
var punOS,punBrowser,punVersion,punTotal,punThestring;

function punGetBrowserInfo() {
    if (punCheckIt('konqueror')) {
        punBrowser = "Konqueror";
        punOS = "Linux";
    } else if (punCheckIt('safari')) {
        punBrowser 	= "Safari";
    } else if (punCheckIt('omniweb')) {
        punBrowser 	= "OmniWeb";
    } else if (punCheckIt('opera')) {
        punBrowser 	= "Opera";
    } else if (punCheckIt('webtv')) {
        punBrowser 	= "WebTV";
    } else if (punCheckIt('icab')) {
        punBrowser 	= "iCab";
    } else if (punCheckIt('msie')) {
        punBrowser 	= "Internet Explorer";
    } else if (!punCheckIt('compatible')) {
        punBrowser = "Netscape Navigator";
        punVersion = punDetect.charAt(8);
    } else {
        punBrowser = "An unknown browser";
    }

    if (!punVersion) {
        punVersion = punDetect.charAt(place + punThestring.length);
    }

    if (!punOS) {
        if (punCheckIt('linux')) {
            punOS = "Linux";
        } else if (punCheckIt('x11')) {
            punOS = "Unix";
        } else if (punCheckIt('mac')) {
            punOS = "Mac";
        } else if (punCheckIt('win')) {
            punOS = "Windows";
        } else {
            punOS = "an unknown operating system";
        }
    }
}

function punCheckIt(string) {
    place = punDetect.indexOf(string) + 1;
    punThestring = string;
    return place;
}

/*-----------------------------------------------------------------------------------------------*/

Event.observe(window, 'load', punGetBrowserInfo, false);

var Productupdates = Class.create();
Productupdates.prototype = {
    yPos : 0,
    xPos : 0,
    isLoaded : false,

    initialize: function(ctrl, url) {
        if (url){
            this.content = url;
        } else {
            this.content = ctrl.href;
        }
        ctrl.observe('click', function(event){this.activate();Event.stop(event);}.bind(this));
    },

    activate: function(){
        if (punBrowser == 'Internet Explorer'){
            this.getScroll();
            this.prepareIE('100%', 'hidden');
            this.setScroll(0,0);
            this.hideSelects('hidden');
        }
        this.displayProductupdates("block");
    },

    prepareIE: function(height, overflow){
        bod = document.getElementsByTagName('body')[0];
        bod.style.height = height;
        bod.style.overflow = overflow;

        htm = document.getElementsByTagName('html')[0];
        htm.style.height = height;
        htm.style.overflow = overflow;
    },

    hideSelects: function(visibility){
        selects = document.getElementsByTagName('select');
        for(i = 0; i < selects.length; i++) {
            selects[i].style.visibility = visibility;
        }
    },

    getScroll: function(){
        if (self.pageYOffset) {
            this.yPos = self.pageYOffset;
        } else if (document.documentElement && document.documentElement.scrollTop){
            this.yPos = document.documentElement.scrollTop;
        } else if (document.body) {
            this.yPos = document.body.scrollTop;
        }
    },

    setScroll: function(x, y){
        window.scrollTo(x, y);
    },

    displayProductupdates: function(display){
        $('productupdates-overlay').style.display = display;
        $('productupdates').style.display = display;
        if(display != 'none') this.loadInfo();
    },

    loadInfo: function() {
        $('productupdates').className = "loading";
        var myAjax = new Ajax.Request(
            this.content,
            {method: 'post', parameters: "", onComplete: this.processInfo.bindAsEventListener(this)}
        );

    },

    processInfo: function(response){
        $('punContent').update(response.responseText);
        $('productupdates').className = "done";
        this.isLoaded = true;
    },

    deactivate: function(){
        if ($('productupdates').className == "loading") {
            return;
        }
        if (punBrowser == "Internet Explorer"){
            this.setScroll(0,this.yPos);
            this.prepareIE("auto", "auto");
            this.hideSelects("visible");
        }

        this.displayProductupdates("none");
    }
};

/*-----------------------------------------------------------------------------------------------*/
function addProductupdatesMarkup() {
    bod 				= document.getElementsByTagName('body')[0];
    overlay 			= document.createElement('div');
    overlay.id		= 'prodictupdates-overlay';
    pun					= document.createElement('div');
    pun.id				= 'productupdates';
    pun.className 	= 'loading';
    pun.innerHTML	= '<div id="punLoadMessage">' +
                          '<p>Loading</p>' +
                          '</div>';
    bod.appendChild(overlay);
    bod.appendChild(pun);
}

var ProductupdatesForm = Class.create();
ProductupdatesForm.prototype = {
    initialize: function(form){
        this.form = form;
        if ($(this.form)) {
            this.sendUrl = $(this.form).action;
            $(this.form).observe('submit', function(event){this.send();Event.stop(event);}.bind(this));
        }
        this.loadWaiting = false;
        this.validator = new Validation(this.form);
        this.onSuccess = this.success.bindAsEventListener(this);
        this.onComplete = this.resetLoadWaiting.bindAsEventListener(this);
        this.onFailure = this.resetLoadWaiting.bindAsEventListener(this);
        var container = $('subscribe-login-container');
        if (container && container.style.display == 'none'){
            this._disableEnableAll(container, true);
        }
    },

    send: function(){
        if(!this.validator.validate()) {
            return false;
        }
        this.setLoadWaiting(true);
        var request = new Ajax.Request(
            this.sendUrl,
            {
                method:'post',
                onComplete: this.onComplete,
                onSuccess: this.onSuccess,
                onFailure: this.onFailure,
                parameters: Form.serialize(this.form)
            }
        );
        return true;
    },

    success: function(transport) {
        this.resetLoadWaiting();
        if (transport && transport.responseText){
            try{
                response = eval('(' + transport.responseText + ')');
            } catch (e) {
                response = {};
            }
        }
        if (response.error){
            if (response.error_type == 'no_login'){
                var container = $('subscribe-login-container');
                if (container){
                    container.show();
                    this._disableEnableAll(container, false);
                }
            }
            if ((typeof response.message) == 'string') {
                alert(response.message);
            } else {
                alert(response.message.join("\n"));
            }
            return false;
        }
        $('punContent').update(transport.responseText);
        return true;
    },

    _disableEnableAll: function(element, isDisabled) {
        var descendants = element.descendants();
        for (var k in descendants) {
            descendants[k].disabled = isDisabled;
        }
        element.disabled = isDisabled;
    },

    setLoadWaiting: function(isDisabled) {
        var container = $('subscribe-button-container');
        if (isDisabled){
            container.setStyle({opacity:.5});
            this._disableEnableAll(container, true);
            Element.show('subscribe-please-wait');
            this.loadWaiting = true;
        } else {
            container.setStyle({opacity:1});
            this._disableEnableAll(container, false);
            Element.hide('subscribe-please-wait');
            this.loadWaiting = false;
        }
    },

    resetLoadWaiting: function(transport){
        this.setLoadWaiting(false);
    }
};
