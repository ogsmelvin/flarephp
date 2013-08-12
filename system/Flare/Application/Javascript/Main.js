var Flare = {};

Flare.trim = function (str, chr) {
	var rgxtrim = (!chr) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^'+chr+'+|'+chr+'+$', 'g');
	return str.replace(rgxtrim, '');
}

Flare.rtrim = function (str, chr) {
	var rgxtrim = (!chr) ? new RegExp('\\s+$') : new RegExp(chr+'+$');
	return str.replace(rgxtrim, '');
}

Flare.ltrim = function (str, chr) {
	var rgxtrim = (!chr) ? new RegExp('^\\s+') : new RegExp('^'+chr+'+');
	return str.replace(rgxtrim, '');
}

Flare.getElement = function (selector) {
	var element;
	if (selector.indexOf("#") === 0) {
		element = document.getElementById(Flare.ltrim(selector, "#"));
	} else if (selector.indexOf(".") === 0) {
		
	} else {
		element = document.getElementsByTagName(selector);
	}
	return element;
}

Flare.GET = (function () {
	var match,
		pl     = /\+/g,  // Regex for replacing addition symbol with a space
		search = /([^&=]+)=?([^&]*)/g,
		decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
		query  = window.location.search.substring(1);

	urlParams = {};
	while (match = search.exec(query))
		urlParams[decode(match[1])] = decode(match[2]);
	return urlParams;
}());

Flare.Ajax = function (method, data, doneCallback) {

	var done;
	var requestUrl;
	var params = [];
	var requestMethod;
	var requestHeaders = [];
	
	function init(method, data, doneCallback) {
		self.setRequestMethod(method == undefined ? "GET" : method);
		if (typeof data == "function") {
			self.done(data);
		} else if (typeof data == "object") {
			self.setParams(data);
			self.done(doneCallback);
		}
	}
	
	this.getParams = function () {
		return params;
	}

	this.setParams = function (parameters) {
		for (i in parameters) {
			self.setParam(i, parameters[i]);
		}
		return self;
	}
	
	this.setParam = function (key, value) {
		params[encodeURIComponent(key)] = encodeURIComponent(value);
		return self;
	}
	
	this.setRequestMethod = function (method) {
		requestMethod = method.toUpperCase();
		if (requestMethod == "POST") {
			self.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		}
		return self;
	}
	
	this.getRequestMethod = function () {
		return requestMethod;
	}
	
	this.setRequestHeader = function (header, value) {
		if (header instanceof Array) {
			for (i in header) {
				requestHeaders[header[i].header] = header[i].value;
			}
		} else {
			requestHeaders[header] = value;
		}
		return self;
	}
	
	this.getRequestHeader = function () {
		return requestHeaders;
	}
	
	this.setRequestUrl = function (url) {
		requestUrl = url;
		return self;
	}
	
	this.getRequestUrl = function () {
		return requestUrl;
	}
	
	this.done = function (callback) {
		done = callback;
		return self;
	}
	
	this.reset = function () {
		done = undefined;
		requestUrl = undefined;
		params = [];
		requestMethod = "GET";
		requestHeaders = [];
	}
	
	this.send = function () {
		if (!requestUrl) {
			console.log("Request URL must be defined. Use setRequestUrl.");
			return;
		}

		var xhr;
		var qstring = [];
		for (i in params) qstring.push(i + "=" + params[i]);
		
		if (window.XMLHttpRequest) {
			xhr = new XMLHttpRequest();
		} else {
			xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}
		
		if (requestMethod == "GET") {
			xhr.open(requestMethod, requestUrl + "?" + qstring.join("&"), true);
		} else if (requestMethod == "POST") {
			xhr.open(requestMethod, requestUrl, true);
		}
		
		for (header in requestHeaders) xhr.setRequestHeader(header, requestHeaders[header]);

		xhr.onreadystatechange = function () {
			if (xhr.readyState == 4) {
				if (done != undefined) done(xhr.responseText);
			}
		}
		xhr.send(requestMethod == "POST" ? qstring.join("&") : "");
	}
	
	var self = this;
	init(method, data, doneCallback);
};

Flare.Events = new function () {
	
	function _bind(elem, event, callback) {
		if (window.addEventListener) {
			elem.addEventListener(event, callback, false);
		} else if (window.attachEvent) {
			elem.attachEvent("on" + event, callback);
		}
	}
	
	this.bind = function (selector, event, callback) {
		var selected = Flare.getElement(selector);
		if (selected instanceof Array) {
			for (i in selected) {
				_bind(selected[i], event, callback);
			}
			return self;
		}
		_bind(selected, event, callback);
		return self;
	}
	
	var self = this;
};

Flare.App = new function () {
	var host;
	this.connect = function (url, events) {
		host = url;
		if (events != undefined) {
			self.bindings(events);
		}
	}
	this.bindings = function (events) {
		for (elem in events) {
			for (evt in events[elem]) {
				Flare.Events.bind(elem, events[elem][evt], function () {
					var ajax = new Flare.Ajax();
					ajax.setRequestUrl(host + "flare.js?request=")
						.setRequestMethod("GET")
						.done( function (response) {
							console.log(response);
						})
						.send();
				});
			}
		}
	}
	var self = this;
}