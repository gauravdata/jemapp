//------------------------------------------------
//action (sale / lead / custom action) object
//------------------------------------------------

var PostAffAction = function(actionCode) {
	this._actionCode = actionCode;
	this._totalCost = '';
	this._fixedCost = '';
	this._orderID = '';
	this._productID = '';
	this._data1 = '';
	this._data2 = '';
	this._data3 = '';
	this._data4 = '';
	this._data5 = '';
	this._affiliateID = '';
	this._campaignID = '';
	this._channelID = '';
	this._customCommission = '';
	this._status = '';
	this._currency = '';
	this._couponCode = '';
}

PostAffAction.prototype.correctSumChars = function(value) {
	var strValue = new String(value);
	strValue = strValue.replace(/,/g, ".");
	strValue = strValue.replace(/[^0-9\.]/gi, "");
	return strValue;
}

PostAffAction.prototype.correctCustomParameterChars = function(value) {
	var strValue = new String(value);
	strValue = strValue.replace(/,/g, ".");
	strValue = strValue.replace(/[^0-9\.\%]/gi, "");
	strValue = strValue.replace(/%/gi, "%25");
	return strValue;
}

PostAffAction.prototype.correctTextChars = function(value) {
	var strValue = new String(value);
	strValue = strValue.replace(/ /gi, "+");
	strValue = strValue.replace(/:/gi, "");
	return strValue;
}

PostAffAction.prototype.setTotalCost = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._totalCost = this.correctSumChars(value);
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setCoupon = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._couponCode = this.correctTextChars(value);
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setFixedCost = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._fixedCost = this.correctCustomParameterChars(value);
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setOrderID = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._orderID = this.correctTextChars(value);
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setProductID = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._productID = this.correctTextChars(value);
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setAffiliateID = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._affiliateID = this.correctTextChars(value);
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setCampaignID = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._campaignID = this.correctTextChars(value);
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setChannelID = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._channelID = this.correctTextChars(value);
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setCurrency = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._currency = this.correctTextChars(value);
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setCustomCommission = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._customCommission = this.correctCustomParameterChars(value);
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setStatus = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._status = value;
		}
	} catch (err) {
	}
}

PostAffAction.prototype.setData1 = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._data1 = this.correctTextChars(value);
		}
	} catch (err) {
		this._data1 = 0;
	}
}

PostAffAction.prototype.setData2 = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._data2 = this.correctTextChars(value);
		}
	} catch (err) {
		this._data2 = 0;
	}
}

PostAffAction.prototype.setData3 = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._data3 = this.correctTextChars(value);
		}
	} catch (err) {
		this._data3 = 0;
	}
}

PostAffAction.prototype.setData4 = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._data4 = this.correctTextChars(value);
		}
	} catch (err) {
		this._data4 = 0;
	}
}

PostAffAction.prototype.setData5 = function(value) {
	try {
		if (typeof (value) != 'undefined') {
			this._data5 = this.correctTextChars(value);
		}
	} catch (err) {
		this._data5 = 0;
	}
}


// ------------------------------------------------
// real tracker object
// ------------------------------------------------

var PostAffTrackerObject = function() {
	var trackingUrl = new String(document.getElementById('pap_x2s6df8d').src);
	this._trackingUrl = trackingUrl.substr(0, Math.max(trackingUrl
			.lastIndexOf('\\'), trackingUrl.lastIndexOf('/')) + 1);

	this._visitorIdCookieName = 'PAPVisitorId';
	this._visitorId = null;
	this._trackingMethod = '';
	
	this._flashStarted = false;
	this._actionObjects = new Array();

	this._loadFirstPartyCookies();
	this.track();
}

PostAffTrackerObject.prototype.createAction = function(actionCode) {
	var obj = new PostAffAction(actionCode);
	var index = this._actionObjects.length;
	this._actionObjects[index] = obj;

	return obj;
}

PostAffTrackerObject.prototype.track = function() {
	if (this._isFlashActive()) {	
		this._insertFlashObject(true);
		setTimeout('trackNext()', 1000);
	} else {	
		this._trackNext();
	}
}

PostAffTrackerObject.prototype.notifySale = function() {
	this.writeValueToAttribute('pap_dx8vc2s5', 'value', 'cookie');
}

PostAffTrackerObject.prototype._checkIfToRunFlash = function(value) {
	if (this._isFlashActive() && this._flashStarted == false) {
		return true;
	}
	return false;
}

PostAffTrackerObject.prototype._getFlashVersion = function() {
	var version = "", n = navigator;
	if (n.plugins && n.plugins.length) {
		for ( var i = 0; i < n.plugins.length; i++) {
			if (n.plugins[i].name.indexOf('Shockwave Flash') != -1) {
				version = n.plugins[i].description.split('Shockwave Flash ')[1];
				break;
			}
		}
	} else if (window.ActiveXObject) {
		for ( var i = 10; i >= 4; i--) {
			try {
				var result = eval("new ActiveXObject('ShockwaveFlash.ShockwaveFlash."
						+ i + "');");
				if (result) {
					version = i + '.0';
					break;
				}
			} catch (e) {
			}
		}
	}
	return version;
}

PostAffTrackerObject.prototype._isFlashActive = function() {
	var version = this._getFlashVersion();
	var ns4 = document.layers;
	var ns6 = document.getElementById && !document.all
			|| (navigator.userAgent.indexOf('Opera') >= 0);
	var ie4 = document.all;
	if (!ns4 && !ns6 && ie4) {
		return false;
	}
	return !(version == "" || version < 5);
}

PostAffTrackerObject.prototype._getFlashParams = function(read) {
	return "?a=" + (read ? "r" : "w") + "&amp;n0=" + this._visitorIdCookieName;
}

PostAffTrackerObject.prototype._insertFlashObject = function(read) {
	if (this._checkIfToRunFlash()) {
		this._flashStarted = true;

		document
				.write("<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" "
						+ "codebase=\""
						+ ((this._trackingUrl.substr(0, 5) == "https") ? "https"
								: "http")
						+ "://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" "
						+ "width=\"1px\" height=\"1px\"> "
						+ "<param name=\"allowScriptAccess\" value=\"always\" />"
						+ "<param name=\"movie\" value=\""
						+ this._trackingUrl
						+ "pap.swf"
						+ this._getFlashParams(read)
						+ "\" /> "
						+ "<embed src=\""
						+ this._trackingUrl
						+ "pap.swf"
						+ this._getFlashParams(read)
						+ "\" width=\"1px\" height=\"1px\" allowScriptAccess=\"always\"/> "
						+ "</object>");
	}
}

PostAffTrackerObject.prototype._processFlashCookies = function(cookies) {
	readFromFlash = false;
	var flashCookies = cookies.split('_,_');
	for ( var i = 0; i < flashCookies.length; i++) {
		var splitIndex = flashCookies[i].indexOf('=');
		if (splitIndex < 0)
			continue;
		if ((flashCookies[i].substr(splitIndex + 1) == null)
				|| (flashCookies[i].substr(splitIndex + 1) == ''))
			continue;
		this._setCookie(flashCookies[i].substr(0, splitIndex), flashCookies[i]
				.substr(splitIndex + 1), 'F');
		readFromFlash = true;
	}
	if (!readFromFlash && this._visitorId != null) {
		this.saveFlashCookie();
	}
}

PostAffTrackerObject.prototype._setCookie = function(name, value, type) {
	if (name == this._visitorIdCookieName) {
		this._visitorId = value;
		this._trackingMethod = type;
		if (this._trackingMethod == 'F') {
			this.saveCookie();
		}
	}
}

PostAffTrackerObject.prototype._loadFirstPartyCookies = function() {
	var cookieValue = this._getNormalCookie(this._visitorIdCookieName);
	if (cookieValue != null) {
		this._visitorId = cookieValue;
		this._trackingMethod = '1';
	}
}

PostAffTrackerObject.prototype._getNormalCookie = function(name) {
	var nameequals = name + "=";
	var beginpos = 0;
	var beginpos2 = 0;
	while (beginpos < document.cookie.length) {
		beginpos2 = beginpos + name.length + 1;
		if (document.cookie.substring(beginpos, beginpos2) == nameequals) {
			var endpos = document.cookie.indexOf(";", beginpos2);
			if (endpos == -1)
				endpos = document.cookie.length;
			return unescape(document.cookie.substring(beginpos2, endpos));
		}
		beginpos = document.cookie.indexOf(" ", beginpos) + 1;
		if (beginpos == 0)
			break;
	}

	return null;
}



PostAffTrackerObject.prototype._trackNext = function() {
	var trackNextScript = document.createElement('script');
	trackNextScript.id = 'pap_x2c8rf47';
	trackNextScript.type = 'text/javascript';
	trackNextScript.src = this._trackingUrl + "track.php"
			+ this._getParameters();
		
	scriptElement = document.getElementById('pap_x2s6df8d');
	scriptElement.parentNode.insertBefore(trackNextScript,
			scriptElement.nextSibling);
}

PostAffTrackerObject.prototype._getParameters = function() {
	params = "?";
	if (this._visitorId != null && this._visitorId != '') {
		params += "visitorId=" + this._visitorId;	
		params += '&';
	} 
	
	params += 'url='+escape(window.location.protocol + "//" + window.location.host + "/" + window.location.pathname);
	params += '&referrer='+escape(document.referrer);
	params += '&getParams='+escape(document.location.search);
	params += '&anchor='+escape(document.location.hash.substring(1));
	
	
	var length = this._actionObjects.length;
	if (length > 0) {
		for (i = 0; i < length; i++) {
			obj = this._actionObjects[i];
			
			saleParams = "sale" + i + "=";
			if (obj._actionCode != null ) {
				saleParams += 'actionCode='+obj._actionCode+';'; 
			}
			if (obj._totalCost != null) {
				saleParams += 'totalCost='+obj._totalCost+';'; 
			}
			if (obj._fixedCost != null) {
				saleParams += 'fixedCost='+obj._fixedCost+';'; 
			}
			if (obj._orderId != null) {
				saleParams += 'orderId='+obj._orderId+';'; 
			}
			if (obj._productId != null) {
				saleParams += 'productId='+obj._productId+';'; 
			}
			if (obj._data1 != null) {
				saleParams += 'data1='+obj._data1+';'; 
			}
			if (obj._data2 != null) {
				saleParams += 'data2='+obj._data2+';'; 
			}
			if (obj._data3 != null) {
				saleParams += 'data3='+obj._data3+';'; 
			}
			if (obj._data4 != null) {
				saleParams += 'data4='+obj._data4+';'; 
			}
			if (obj._data5 != null) {
				saleParams += 'data5='+obj._data5+';'; 
			}
			if (obj._affiliateId != null) {
				saleParams += 'affiliateId='+obj._affiliateId+';';
			}
			if (obj._campaignId != null) {
				saleParams += 'campaignId='+obj._campaignId+';'; 
			}
			if (obj._channelId != null) {
				saleParams += 'channelId='+obj._channelId+';'; 
			}
			if (obj._customCommmission != null) {
				saleParams += 'customCommmission='+obj._customCommmission+';';
			}
			if (obj._status != null) {
				saleParams += 'status='+obj._status+';'; 
			}
			if (obj._currency != null) {
				saleParams += 'currency='+obj._currency+';'; 
			}
			if (obj._couponCode != null) {
				saleParams += 'couponCode='+obj._couponCode+';'; 
			}
			params += saleParams;
			
			/*
			escape(
					+
					'='+obj._totalCost + ';'+
					'='+obj._fixedCost + ';'+
					'='+obj._orderID + ';'+
					'='+obj._productID + ';'+
					'='+obj._data1 + ';'+
					'data2='+obj._data2 + ';'+
					'date3='+obj._data3 + ';'+
					'data4='+obj._data4 + ';'+
					'data5='+obj._data5 + ';'+
					'affiliateId='+obj._affiliateID + ';'+
					'campaignId='+obj._campaignID + ';'+
					'channelId='+obj._channelID + ';'+
					'customCommmission='+obj._customCommission + ';'+
					'status='+obj._status + ';' +
					'currency='+obj._currency + ';'+
					'couponCode='+obj._couponCode + ';'
			);
			*/
		}
	}
	
	
	
	return params;
}

PostAffTrackerObject.prototype._getParamFromLink = function(link, name) {
	name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regexS = "[\\?&]" + name + "=([^&#]*)";
	var regex = new RegExp(regexS);
	var results = regex.exec(link);
	if (results == null)
		return null;
	else
		return results[1];
}

PostAffTrackerObject.prototype._encodeRegExp = function(re) {
	if (re == null) {
		return '';
	}
	return re.replace(/([.*+?^${}()|[\]\/\\])/g, '\\$1');
}

PostAffTrackerObject.prototype._getLinkWithValue = function(link,
		parameterName, value, separator, storedBefore) {
	value = escape(value);

	if (link == null || link == '') {
		return '?' + value;
	}

	if (separator == null) {
		separator = '||';
	}

	var oldParam = this._getParamFromLink(link, parameterName);

	if (storedBefore) {
		if (link.indexOf(separator) == -1) {
			link = link.replace(this._encodeRegExp(oldParam), '');
		} else {
			link = link.replace(this._encodeRegExp(separator) + '*', '');
		}
		oldParam = this._getParamFromLink(link, parameterName);
	}

	if (oldParam != null) {
		if (oldParam == '') {
			return link.replace(this._encodeRegExp(parameterName + '='),
					parameterName + '=' + value);
		} else {
			return link.replace(this._encodeRegExp(parameterName + '='
					+ oldParam), parameterName + '=' + oldParam + separator
					+ value);
		}
	} else {
		var joinChar;
		if (link.indexOf('?') == -1) {
			joinChar = '?';
		} else {
			joinChar = '&';
		}
		return link + joinChar + parameterName + '=' + value;
	}
}

PostAffTrackerObject.prototype.getElementsById = function(id) {
	var nodes = new Array();
	var tmpNode = document.getElementById(id);
	while (tmpNode) {
		nodes.push(tmpNode);
		tmpNode.id = "";
		tmpNode = document.getElementById(id);
		for ( var x = 0; x < nodes.length; x++) {
			if (nodes[x] == tmpNode) {
				tmpNode = false;
			}
		}

	}
	for ( var x = 0; x < nodes.length; x++) {
		nodes[x].id = id;
	}

	return nodes;
}

PostAffTrackerObject.prototype._replaceHttpInText = function(text) {
	text = text.replace("http://", "H_");
	text = text.replace("https://", "S_");
	return text;
}

PostAffTrackerObject.prototype.saveCookie = function() {
	document.cookie = this._visitorIdCookieName + '=' + this._visitorId + ';';
}

PostAffTrackerObject.prototype.setVisitorId = function(visitorId) {
	this._visitorId = visitorId;
	this.saveCookie();
	this.saveFlashCookie();
}

// SAVE FLASH
PostAffTrackerObject.prototype._getFlashParameters = function(){
	params = "";
    params += "&n0=" + this._visitorIdCookieName;
    params += "&v0=" + this._visitorId;
    params += "&ne0=1";

    return "?a=w" + params;
}

PostAffTrackerObject.prototype.saveFlashCookie = function() {
    trackingUrl = new String(document.getElementById('pap_x2s6df8d').src);
    _trackingUrl = trackingUrl.substr(0, Math.max(trackingUrl.lastIndexOf('\\'), trackingUrl.lastIndexOf('/'))+1);

// TODO: check if setAttribute works fine under Internet Explorer 6
    obj = document.createElement("object"); 
    obj.setAttribute('classid', 'clsid:d27cdb6e-ae6d-11cf-96b8-444553540000');
    obj.setAttribute('codebase', document.location.protocol + "://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" ");
    obj.setAttribute('width', '1px');
    obj.setAttribute('height', '1px');
    
    param1 = document.createElement('param');
    param1.setAttribute('name', 'allowScriptAccess');
    param1.setAttribute('value', 'always');
    obj.appendChild(param1);
    
    param2 = document.createElement('param');
    param2.setAttribute('name', 'movie');
    param2.setAttribute('value', _trackingUrl + "pap.swf"+ this._getFlashParameters());
    obj.appendChild(param2);
   
    embed = document.createElement('embed');
    embed.setAttribute('src',  _trackingUrl + "pap.swf"+ this._getFlashParameters());
    embed.setAttribute('width', '1px');
    embed.setAttribute('height', '1px');
    embed.setAttribute('allowScriptAccess', 'always');
    obj.appendChild(embed);
    
    scr = document.getElementById('pap_x2s6df8d');
    scr.appendChild(obj);
    
    scriptElement = document.getElementById('pap_x2s6df8d');
    scriptElement.parentNode.insertBefore(obj, scriptElement.nextSibling);
}


/**
 * add value to html element
 * 
 * @params id(element id); attributeName(href, value, action); value(cookie,
 *         affiliate, campaign), urlParamName - name for url parameter(e.g:
 *         papCookie), null if it is not link separator - separation for adding
 *         pap value, default null = old value is overwritten
 * 
 */
PostAffTrackerObject.prototype.writeValueToAttribute = function(id,
		attributeName, value, urlParamName, separator) {
	if (this._isFlashActive()) {
		// write 1st party cookies immediately
		var storedBefore = this._writeValueToAttribute(id, attributeName,
				value, urlParamName, separator, false);
		// get flash cookies on delay
		this._insertFlashObject();

		var self = this;
		setTimeout( function(ms) {
			self._writeValueToAttribute(id, attributeName, value, urlParamName,
					separator, storedBefore)
		}, 1000);
	} else {
		this._writeValueToAttribute(id, attributeName, value, urlParamName,
				separator, false);
		;
	}
}

PostAffTrackerObject.prototype._writeValueToAttribute = function(id,
		attributeName, value, urlParamName, separator, storedBefore) {
	if (this._visitorId == null || this._visitorId == '') {
		return false;
	}

	return this._writeValue(id, attributeName, this._visitorId, urlParamName,
			separator, storedBefore);
}

PostAffTrackerObject.prototype._getValue = function(oldValue, newValue,
		urlParamName, separator, storedBefore) {
	if (urlParamName == null) {
		if (separator == null) {
			return newValue;
		}

		if (storedBefore) {
			indexOfSeparator = oldValue.indexOf(separator);
			oldValue = oldValue.substring(0, indexOfSeparator);
		}

		return oldValue + separator + newValue;
	}
	return this._getLinkWithValue(oldValue, urlParamName, newValue, separator,
			storedBefore);
}

PostAffTrackerObject.prototype._writeValue = function(id, attributeName, value,
		urlParamName, separator, storedBefore) {
	if (value == null || value == '') {
		return false;
	}

	var elements = this.getElementsById(id);
	for (i = 0; i < elements.length; i++) {
		if (elements[i].id == id) {
			// SWITCH CASES are used because of Internet Explorer 6 doesn't
			// support setAttribute in correct way
			switch (attributeName) {
			case 'href':
				elements[i].href = this._getValue(elements[i].href, value,
						urlParamName, separator, storedBefore);
				break;
			case 'value':
				elements[i].value = this._getValue(elements[i].value, value,
						urlParamName, separator, storedBefore);
				break;
			case 'action':
				elements[i].action = this._getValue(elements[i].action, value,
						urlParamName, separator, storedBefore);
				break;
			default:
				elements[i].setAttribute(attributeName, this._getValue(
						elements[i].getAttribute(attributeName), value,
						urlParamName, separator, storedBefore));
				break;
			}
		}
	}
	return true;
}

// ------------------------------------------------
// singleton tracker object
// ------------------------------------------------

var PostAffTracker = new function(lid) {
	this._instance = new PostAffTrackerObject();
	this._separator;

	this.createSale = function() {
		return this._instance.createAction('');
	}

	this.createAction = function(actionCode) {
		return this._instance.createAction(actionCode);
	}

	this.register = function() {
		return this._instance.track();
	}
	
	this.track = function() {
		return this._instance.track();
	}

	this.notifySale = function() {
		return this._instance.notifySale();
	}

	this.setLid = function(value) {
		return this._instance.setLid(value);
	}

	this.setChannel = function(value) {
		return this._instance.setChannel(value);
	}

	this.setCookieValue = function(value) {
		return this._instance.setCookieValue(value);
	}

	this._processFlashCookies = function(cookies) {
		return this._instance._processFlashCookies(cookies);
	}

	this._setCookieToBeDeleted = function(name) {
		return this._instance._setCookieToBeDeleted(name);
	}

	this.setAppendValuesToField = function(separator) {
		return this._separator = separator;
	}

	this._trackNext = function() {
		return this._instance._trackNext();
	}

	// TODO: compatibility with writing affiliateId/cookie
	this.writeCookieToCustomField = function(id) {
		return this._instance.writeValueToAttribute(id, 'value', this._visitorId,
				null, this._separator);
	}

	this.writeAffiliateToCustomField = function(id) {
		return this._instance.writeValueToAttribute(id, 'value', this._visitorId,
				null, this._separator);
	}

	this.writeCampaignToCustomField = function(id) {
		return this._instance.writeValueToAttribute(id, 'value', this._visitorId,
				null, this._separator);
	}

	this.writeCookieToLink = function(id, urlParamName, separator) {
		return this._instance.writeValueToAttribute(id, 'href', this._visitorId,
				urlParamName, separator == null ? this._separator : separator);
	}

	this.writeAffiliateToLink = function(id, urlParamName, separator) {
		return this._instance.writeValueToAttribute(id, 'href', this._visitorId,
				urlParamName, separator == null ? this._separator : separator);
	}

	this.writeValueToAttribute = function(id, attributeName, value, type,
			separator) {
		return this._instance.writeValueToAttribute(id, attributeName, value,
				type, separator == null ? this._separator : separator);
	}
	
	this.setVisitorId = function(v) {
		this._instance.setVisitorId(v);
	}
	
	this.createSale = function() {
		return this._instance.createAction('');
	}
}

// ------------------------------------------------
// global functions
// ------------------------------------------------

function rpap(cookies) {
	PostAffTracker._processFlashCookies(cookies);
}

function trackNext() {
	PostAffTracker._trackNext();
}

function papTrack() {
	PostAffTracker.track();
}

function setVisitor(v) {
	PostAffTracker.setVisitorId(v);
}