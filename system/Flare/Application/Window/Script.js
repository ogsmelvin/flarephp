String.prototype.trim = function (chr) {
    return this.replace((!chr) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^' + chr + '+|' + chr + '+$', 'g'), '');
}

String.prototype.rtrim = function (chr) {
    return this.replace((!chr) ? new RegExp('\\s+$') : new RegExp(chr + '+$'), '');
}

String.prototype.ltrim = function (chr) {
    return this.replace((!chr) ? new RegExp('^\\s+') : new RegExp('^' + chr + '+'), '');
}

function clone(obj) {
    return new function () {
        this.prototype = obj;
    }
}

function require(script) {
    
}

function addEvent(elem, event, listener) {
    if (typeof event == "string" && typeof elem == "string") {
        elem = document.getElementById(elem);
    } else if (typeof event == "function" && listener == undefined) {
        listener = event;
        event = elem;
        elem = this;
    }
    if (window.addEventListener) {
        elem.addEventListener(event, listener, false);
    } else if (window.attachEvent) {
        elem.attachEvent("on" + event, listener);
    }
}

HTMLElement.prototype.addEvent = addEvent;

flare = {};
flare.Ajax = (function () {

    function createRequest() {
        var request;
        if (window.XMLHttpRequest) {
            request = new XMLHttpRequest();
        } else {
            request = new ActiveXObject("Microsoft.XMLHTTP");
        }
        return request;
    }

    this.get = function (url, data, done) {
        var params = [];
        var request = this.createRequest();
        if (typeof data == "object") {
            for (i in data) {
                params.push(encodeURIComponent(i) + "=" + encodeURIComponent(data[i]));
            }
        } else if (typeof data == "function") {
            done = data;
        }
        request.onreadystatechange = function () {
            if (request.readyState == self.COMPLETE && request.status == 200){
                done(request.responseText);
            }
        }
        request.open("GET", url.rtrim("?") + "?" + params.join("&"), true);
        request.send();
    }

    this.post = function (url, data, done) {
        var params = [];
        var request = this.createRequest();
        if (typeof data == "object") {
            for (i in data) {
                params.push(encodeURIComponent(i) + "=" + encodeURIComponent(data[i]));
            }
        } else if (typeof data == "function") {
            done = data;
        }
        request.onreadystatechange = function () {
            if (request.readyState == self.COMPLETE && request.status == 200){
                done(request.responseText);
            }
        }
        request.open("POST", url, true);
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        request.send(params.join("&"));
    }

    this.UNINITIALIZED = 0; // uninitialized
    this.LOADING = 1; // loading
    this.LOADED = 2; // loaded
    this.INTERACTIVE = 3; // interactive
    this.COMPLETE = 4; // complete

    var self = this;
})();

flare.Application = function () {

    this.GET = (function () {
        var match,
            pl     = /\+/g,
            search = /([^&=]+)=?([^&]*)/g,
            decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
            query  = window.location.search.substring(1);

        var urlParams = {};
        while (match = search.exec(query))
            urlParams[decode(match[1])] = decode(match[2]);
        return urlParams;
    })();

    function Action(callback) {
        var params = {};
        this.execute = callback;
        this.GET = self.GET;
        this.model = function () {

        }
        this.setParam = function (key, val) {
            params[key] = decodeURIComponent(val);
            return this;
        }
        this.getParam = function (key) {
            return params[key] != undefined ? params[key] : console.log("Undefined param '" + key + "'");
        }
        this.getParams = function () {
            return params;
        }
    }

    this.routes = {};
    this.route = function (url, action) {
        this.routes[url] = new Action(action);
    }

    function _scanRoutes(evt) {
        var url = window.location.hash.substring(1);
        url = !url ? "/" : "/" + url.ltrim("/");
        if (self.routes[url] != undefined) {
            var action = self.routes[url];
            action.execute(evt);
        }
    }

    this.run = function () {
        window.addEvent("hashchange", _scanRoutes);
        window.addEvent("load", _scanRoutes);
    }

    var self = this;
}

App = new flare.Application();
App.run();

