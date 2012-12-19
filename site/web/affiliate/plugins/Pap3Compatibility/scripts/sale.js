
var SaleTracker = function(lid) {
	this._lid = lid;
	
	var trackingUrl = new String(document.getElementById('pap_x2s6df8d').src);
	this._trackingUrl = trackingUrl.substr(0, Math.max(trackingUrl.lastIndexOf('\\'), trackingUrl.lastIndexOf('/'))+1);
	
	this._trackingMethod = '1';
	
	this._salerIndex = _salers.length;
    _salers[this._salerIndex] = this;

	this._cookies = new Array();

	var cookie = new Object;
	cookie.name = 'PAPCookie_Sale'; //'POSTAff2Cookie';
	cookie.postname = 'fsc';
	cookie.value = '';
	cookie.del = false;
	this._cookies[0] = cookie;

	var cookie = new Object;
	cookie.name = 'PAPCookie_Click'; //'POSTAff2TimeCookie';
	cookie.postname = 'fstc';
	cookie.value = '';
	cookie.del = false;
	this._cookies[1] = cookie;

	var cookie = new Object;
	cookie.name = 'PAPCookie_OriginalReferrer'; //'PAPR_0';
	cookie.postname = 'forc';
	cookie.value = '';
	cookie.del = false;
	this._cookies[2] = cookie;
	
	var cookie = new Object;
	cookie.name = 'PAPCookie_ClickData'; //'POSTAff2ClickCookie';
	cookie.postname = 'fscc';
	cookie.value = '';
	cookie.del = false;
	this._cookies[3] = cookie;

	var cookie = new Object;
	cookie.name = 'POSTAff2Cookie';
	cookie.postname = 'fsc';
	cookie.value = '';
	cookie.del = false;
	this._cookies[4] = cookie;
};

SaleTracker.prototype._getFlashVersion = function() {
	var version = "", n=navigator;
	if (n.plugins && n.plugins.length) {
		for (var i=0; i < n.plugins.length;i++) {
			if (n.plugins[i].name.indexOf('Shockwave Flash')!=-1) {
	    		version = n.plugins[i].description.split('Shockwave Flash ')[1];
	    		break;
	   		}
	  	}
	 } else if (window.ActiveXObject) {
	 	for (var i=10; i>=4; i--) {
	   	try {
	    	var result = eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash."+i+"');");
	    	if (result) {
	    		version = i + '.0';
	    		break;
	    	}
	   	}
	   	catch(e) {}
	  	}
	 }
	 return version;
}

SaleTracker.prototype._isFlashActive = function() {
	var version = this._getFlashVersion();
	var ns4 = document.layers;
    var ns6 = document.getElementById && !document.all || (navigator.userAgent.indexOf('Opera') >= 0);
    var ie4 = document.all;
	if(!ns4 && !ns6 && ie4 && (this._saleType == 1)) {
	   return false;
	}
	return !(version == "" || version < 5);
}

SaleTracker.prototype._getNormalCookie = function(name) {
    var nameequals = name + "=";
    var beginpos = 0;
    var beginpos2 = 0;
    while (beginpos < document.cookie.length) {
        beginpos2 = beginpos + name.length + 1;
        if (document.cookie.substring(beginpos, beginpos2) == nameequals) {
            var endpos = document.cookie.indexOf (";", beginpos2);
            if (endpos == -1)
                endpos = document.cookie.length;
            return unescape(document.cookie.substring(beginpos2, endpos));
        }
        beginpos = document.cookie.indexOf(" ", beginpos) + 1;
        if (beginpos == 0) break;
    }

    return null;
};

SaleTracker.prototype._getFlashParams = function() {
	var params = "";
	for(var i=0; i < this._cookies.length; i++) {
		params += "&amp;n" + i + "=" + this._cookies[i].name;
		if (this._cookies[i].del == true) {
			params += "&amp;d" + i + "=1";
		}
	}
	return "?a=r" + params;
}

SaleTracker.prototype._getSaleParams = function() {
	var params = "";
	if(this._cookies[0].value == '') {
		this._cookies[0].value = this._cookies[4].value;
	}
	for(var i=0; i < 3; i++) {
		params += "&" + this._cookies[i].postname + "=" + this._cookies[i].value;
	}
	return params;
}

SaleTracker.prototype._loadFirstPartyCookies = function() {
	for(var i=0; i < this._cookies.length; i++) {
	    var cookieValue = this._getNormalCookie(this._cookies[i].name);
	    if (cookieValue != null) {
	       this._cookies[i].value = cookieValue;
	       this._trackingMethod = '1';
	    }
	}
}

SaleTracker.prototype._setCookie = function(name, value, type) {
    for(var i=0; i < this._cookies.length; i++) {
        if (this._cookies[i].name == name) {
            this._cookies[i].value = value;
            this._trackingMethod = type;
        }
	}
}

SaleTracker.prototype._writeImg = function() {
    document.write('<img id="pap_0x25s6ds" src="'+this._trackingUrl+'pix.gif" width="1" height="1">');
}

SaleTracker.prototype._loadFlashCookies = function() {
    if(this._isFlashActive()) {
		document.write("<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" " +
		"codebase=\"" + ((this._trackingUrl.substr(0, 5) == "https") ? "https" : "http") + "://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" " +
		"width=\"1px\" height=\"1px\"> " +
		"<param name=\"allowScriptAccess\" value=\"always\" />" +
		"<param name=\"movie\" value=\"" + this._trackingUrl + "pap.swf"+ this._getFlashParams() +"\" /> " +
		"<embed src=\"" + this._trackingUrl + "pap.swf"+ this._getFlashParams() +"\" width=\"1px\" height=\"1px\" allowScriptAccess=\"always\"/> " +
		"</object>");
	}
}

SaleTracker.prototype._deleteFirstPatyCookies = function() {
	for(var i=0; i < this._cookies.length; i++) {
        if (this._cookies[i].del ) {
            document.cookie = this._cookies[i].name+'=;expires=Thu, 01-Jan-1970 00:00:01 GMT;path=/';
        }
	}
	
}

SaleTracker.prototype.setCookieToBeDeleted = function(name) {
	if (this._saleType == 1) return;
	for(var i=0; i < this._cookies.length; i++) {
        if (this._cookies[i].name == name) {
            this._cookies[i].del = true;
        }
	}
}

SaleTracker.prototype.fillCustomFields = function() {
	var cookieValue = this._cookies[0].value;
	if(cookieValue == '') {
		cookieValue = this._cookies[4].value;
	}
	var customInputs = this.getElementsById("pap_dx8vc2s5");
	for (i=0;i<customInputs.length;i++) {
   		if (customInputs[i].id == "pap_dx8vc2s5") {
       		customInputs[i].value = cookieValue;
   		}
	}	
}

SaleTracker.prototype.trackNext = function() {
    switch (this._saleType) {
        case 0:
	       document.getElementById("pap_0x25s6ds").src = this._trackingUrl + "sale.php" +
	    	      "?lid=" + this._lid + "&ptm=" + this._trackingMethod + "&TotalCost=" + this._totalCost + "&OrderID=" + this._orderID + "&ProductID=" + this._productID +
                  "&data1=" + this._data1 + "&data2=" + this._data2 + "&data3=" + this._data3 + this._getSaleParams();
	       break;
	    case 1:
    	   this.fillCustomFields();
	       break;
    }
}

SaleTracker.prototype.getElementsById = function(id) {
	var nodes = new Array();
	var tmpNode = document.getElementById(id);
	while(tmpNode) {
		nodes.push(tmpNode);
		tmpNode.id = "";
		tmpNode = document.getElementById(id);
	}
	for(var x=0; x<nodes.length; x++) {
		nodes[x].id = id;
    }
    
    return nodes;
}

SaleTracker.prototype.processFlashCookies = function(cookies) {
    var flashCookies = cookies.split('_,_');
    for(var i=0; i< flashCookies.length; i++) {
        var splitIndex = flashCookies[i].indexOf('=');
        if (splitIndex < 0) continue;
        if ((flashCookies[i].substr(splitIndex+1) == null) || (flashCookies[i].substr(splitIndex+1) == '')) continue;
        this._setCookie(flashCookies[i].substr(0, splitIndex), flashCookies[i].substr(splitIndex+1), 'F');
    }
}

SaleTracker.prototype.preSale = function() {
	if (_saler._saleType == 0) {
		document.write('<script src="' + this._trackingUrl + 'presale.php?lid=' + this._lid + '&salerIndex=' + this._salerIndex + '"></script>');
	} else {
		this._loadFirstPartyCookies();
		this.fillCustomFields();
		document.write('<script src="' + this._trackingUrl + 'presale.php?lid=' + this._lid + '&salerIndex=' + this._salerIndex + '&loadCookies=1"></script>');
	}
}

SaleTracker.prototype.sale = function() {
    this._writeImg();
    this._loadFirstPartyCookies();
    if (this._isFlashActive()) {
        this._loadFlashCookies();
        setTimeout('trackNext('+this._salerIndex+')', 1000);
    } else {
        this.trackNext();
    }
    this._deleteFirstPatyCookies();
}

var _saler;
try {
    var pap_tmp = _salers.length
} catch (err) {
    var _salers = new Array();
} 

function trackNext(salerIndex) {
    _salers[salerIndex].trackNext();
}

function rpap(cookies) {
    for (i=0;i<_salers.length;i++) {
        _salers[i].processFlashCookies(cookies);
    }
}

function set3rdPartyCookie(salerIndex, name, value) {
    _salers[salerIndex]._setCookie(name, value, 1);
}

function setCookieToBeDeleted(salerIndex, name) {
	_salers[salerIndex].setCookieToBeDeleted(name);
}

function finishSale(salerIndex) {
    _salers[salerIndex].sale();
}

function papSale() {
    try { _saler = new SaleTracker(_lid);  } catch (err) { _saler = new SaleTracker(''); }
    try { _saler._totalCost = TotalCost;   } catch (err) { _saler._totalCost = 0; }
    try { _saler._orderID = OrderID;       } catch (err) { _saler._orderID = ''; }
    try { _saler._productID = ProductID;   } catch (err) { _saler._productID = ''; }
    try { _saler._data1 = Data1;           } catch (err) { _saler._data1 = ''; }
    try { _saler._data2 = Data2;           } catch (err) { _saler._data2 = ''; }
    try { _saler._data3 = Data3;           } catch (err) { _saler._data3 = ''; }
    try { _saler._secureConnection = _sc;  } catch (err) { _saler._secureConnection = false; }
	_saler._saleType = 0;
	_saler.preSale();
}

function paypalSale() {
    try {
	    _saler = new SaleTracker(_lid);
    } catch (err) {
        _saler = new SaleTracker('');
    }
    try { _saler._secureConnection = _sc;  } catch (err) { _saler._secureConnection = false; }
	_saler._totalCost = '';
	_saler._orderID = '';
	_saler._productID = '';
    _saler._data1 = '';
    _saler._data2 = '';
    _saler._data3 = '';
	_saler._saleType = 1;
	_saler.preSale();
}
