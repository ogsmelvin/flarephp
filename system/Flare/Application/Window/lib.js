if (!String.prototype.trim) {
    String.prototype.trim = function (chr) {
        return this.replace((!chr) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^' + chr + '+|' + chr + '+$', 'g'), '');
    }
}

if (!String.prototype.rtrim) {
    String.prototype.rtrim = function (chr) {
        return this.replace((!chr) ? new RegExp('\\s+$') : new RegExp(chr + '+$'), '');
    }
}

if (!String.prototype.ltrim) {
    String.prototype.ltrim = function (chr) {
        return this.replace((!chr) ? new RegExp('^\\s+') : new RegExp('^' + chr + '+'), '');
    }
}

if (!String.prototype.capitalize) {
    String.prototype.capitalize = function() {
        return this.charAt(0).toUpperCase() + this.slice(1);
    }
}

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf=function(o,i){for(var j=this.length,i=i<0?i+j<0?0:i+j:i||0;i<j&&this[i]!==o;i++);return j<=i?-1:i}
}

if (!String.prototype.bin2hex) {
    String.prototype.bin2hex = function () {
        var i, l, o = "", n;
        var s = this.toString();
        for (i = 0, l = s.length; i < l; i++) {
            n = s.charCodeAt(i).toString(16);
            o += n.length < 2 ? "0" + n : n;
        }
        return o;
    }
}

var flare = { $body : null };

flare.clone = function (obj) {
    return new function () {
        this.prototype = obj;
    }
}

flare.Data = function (content) {
    this.data = content != undefined ? content : {};
    this.each = function (callback) {
        for (i in this.data) {
            callback(i, this.data[i]);
        }
        return this;
    }
}

flare.Collection = function (content) {
    flare.Data.call(this, content);
}

flare.ViewComponent = function () {
    this.name = null;
    this.element = null;
    this.elementId = null;
    this.init = function (methods) {
        for (m in methods) {
            this[m] = methods[m];
        }
    }
    this.onRouteChange = function (route) {
        if (this.element && this.elementId) {
            var element = $(this.elementId);
            if (element.length) {
                this.element = element;
            }
        }
        if (typeof this._onRouteChange == "function") {
            this._onRouteChange(route);
        }
    }
    this._bindTo = function (selector) {
        var selected = $(selector);
        if (selected.length) {
            this.element = selected;
            this.elementId = selector;
            return this.element;
        }
        return false;
    }
}

flare.TabbedPane = function (methods) {
    flare.ViewComponent.call(this);
    this.tabs = "> li > a";
    this.activeClassName = "active";
    this.setActive = function (uri) {
        this.element.find(this.tabs + "[href=\"#" + uri + "\"]")
            .parent()
            .addClass(this.activeClassName)
            .siblings()
            .removeClass(this.activeClassName);
    }
    this.onRouteChange = function (route) {
        this.setActive(route.URI);
    }
    this.bindTo = function (selector) {
        this._bindTo(selector);
    }
    this.init(methods);
}

flare.Form = function (methods) {
    flare.ViewComponent.call(this);
    this._props = {};
    this.async = false;
    this.form = null;
    this.onError = function (error) {}
    this.onSubmit = function (evt) {}
    this.onSuccess = function (data) {}
    this._onSubmit = function (evt) {
        var hasError = false;
        self.onSubmit(evt);
        self.form.each( function (key, elem) {
            var rule = elem.getAttribute("data-rule");
            if ((elem.getAttribute("data-strict") == "required" && !elem.value)
                || (rule && typeof self.ON_SUBMIT_VALIDATION[rule] != "undefined" 
                    && !self.ON_SUBMIT_VALIDATION[rule].test(elem.value)))
            {
                self.onError($(elem), rule);
                hasError = true;
            }
        });
        if (hasError) return false;
        if (self.async && !hasError) {
            var jsonFormData = {};
            var elementsData = self.element.serializeArray();
            for (dataKey in elementsData) {
                jsonFormData[elementsData[dataKey].name] = elementsData[dataKey].value;
            }
            self.onSuccess(jsonFormData);
            return false;
        }
        return true;
    }
    this.bindTo = function (selector) {
        if (!this._bindTo(selector)) return;
        this.name = this.element.prop("name");
        this._props.action = this.element.prop("action");
        this._props.method = this.element.prop("method");
        flare.$body.on("submit", this.elementId, this._onSubmit);
        for (rule in this.RULES) {
            flare.$body.on(
                "keydown",
                this.elementId + " input[data-rule='" + rule + "'], " + this.elementId + " textarea[data-rule='" + rule + "']",
                this.RULES[rule]
            );
        }
    }
    this._onRouteChange = function () {
        if (this.element && this.element.length) {
            this.form = this.element.find("input, textarea, select");
        }
    }
    this.RULE_ATTR_NAME = "rule";
    this.ON_SUBMIT_VALIDATION = {
        letter : /^[a-z ]+$/i,
        numeric : /^[0-9]+$/,
        email : /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,10}$/
    }
    this.RULES = {
        letter : function (e) {
            var code = (e.which) ? e.which : e.keyCode;
            if (code == 46 || code == 8 || code == 9 || code == 27 || code == 32 || code == 13 ||
                (code == 65 && e.ctrlKey === true) ||
                (code >= 35 && code <= 39))
            {
                return;
            } else if (code < 65 || code > 90) {
                e.preventDefault();
            }
        },
        numeric : function (e) {
            var code = (e.which) ? e.which : e.keyCode;
            if (code == 46 || code == 8 || code == 9 || code == 27 || code == 13 ||
                (code == 65 && e.ctrlKey === true) ||
                (code >= 35 && code <= 39))
            {
                return;
            } else if (e.shiftKey === true || code < 48 || (code > 90 && code < 96) || code > 105) {
                e.preventDefault();
            }
        },
        email : function (e) {
            var code = (e.which) ? e.which : e.keyCode;
            if (code == 46 || code == 8 || code == 9 || code == 27 || code == 13 ||
                (code == 65 && e.ctrlKey === true) || code == 189 || code == 190 || code == 110 || code == 109 ||
                (code == 50 && e.shiftKey === true) ||
                (code >= 35 && code <= 39))
            {
                return;
            } else if (e.shiftKey === true || code < 48 || (code > 90 && code < 96) || code > 105) {
                e.preventDefault();
            }
        }
    }
    this.init(methods);
    var self = this;
}

flare.jsonize = function (jsonString) {
    var responseData = {};
    if (typeof JSON.parse != "undefined") {
        try {
            responseData = JSON.parse(jsonString);
        } catch (e) {
            return;
        }
    } else {
        responseData = eval('( ' + jsonString + ' )');
    }
    return responseData;
}

flare.Ajax = new function () {

    function createRequest() {
        var request;
        if (window.XMLHttpRequest) {
            request = new XMLHttpRequest();
        } else {
            request = new ActiveXObject("Microsoft.XMLHTTP");
        }
        return request;
    }

    this.request = function (done) {
        var request = createRequest();
        request.onreadystatechange = function () {
            if (request.readyState == self.COMPLETE) {
                done(request.responseText, request.status);
            }
        }
        return request;
    }

    this.get = function (url, data, done) {
        var params = [];
        if (typeof data == "object") {
            for (i in data) {
                params.push(encodeURIComponent(i) + "=" + encodeURIComponent(data[i]));
            }
        } else if (typeof data == "function") {
            done = data;
        }
        var request = createRequest();
        request.onreadystatechange = function () {
            if (request.readyState == self.COMPLETE && request.status == self.COMPLETE_CODE){
                done(flare.jsonize(request.responseText));
            }
        }
        request.open(self.METHOD_GET, url.rtrim("?") + "?" + params.join("&"), true);
        request.setRequestHeader(self.HEADER_NAME, self.HEADER_VALUE);
        request.send();
    }

    this.post = function (url, data, done) {
        var params = [];
        if (typeof data == "object") {
            for (i in data) {
                params.push(encodeURIComponent(i) + "=" + encodeURIComponent(data[i]));
            }
        } else if (typeof data == "function") {
            done = data;
        }
        var request = createRequest();
        request.onreadystatechange = function () {
            if (request.readyState == self.COMPLETE && request.status == self.COMPLETE_CODE){
                done(flare.jsonize(request.responseText));
            }
        }
        request.open(self.METHOD_POST, url, true);
        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        request.setRequestHeader(self.HEADER_NAME, self.HEADER_VALUE);
        request.send(params.join("&"));
    }

    this.UNINITIALIZED = 0; // uninitialized
    this.LOADING = 1; // loading
    this.LOADED = 2; // loaded
    this.INTERACTIVE = 3; // interactive
    this.COMPLETE = 4; // complete

    this.METHOD_GET = "GET";
    this.METHOD_POST = "POST";
    this.METHOD_PUT = "PUT";
    this.METHOD_DELETE = "DELETE";

    this.COMPLETE_CODE = 200;

    this.HEADER_NAME = "X-Requested-With";
    this.HEADER_VALUE = "XMLHttpRequest";

    var self = this;
}

flare.Application = function () {

    this.Config = {
        controller : {
            defaultController : "index",
            defaultAction : "index",
            actionSuffix : "_action",
            appRunningKey : "__isRunning__",
        },
        model : {
            ApiExtension : ".do",
        },
        view : {
            appScriptType : "text/x-flare",
            tagLayoutReplacement : "div",
            uiComponentAttribute : "ui-component",
            viewTypeAttribute : "view-type"
        }  
    };

    this.use = function (module, onLoad) {
        var head = document.getElementsByTagName('head')[0];
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = this.Config.baseUrl + module + ".js";
        if (typeof onLoad == "function") {
            script.onload = onLoad;
        }
        head.appendChild(script);
        return this;
    }

    this.style = function (css, onLoad) {
        var head = document.getElementsByTagName('head')[0];
        var link = document.createElement('link');
        link.href = this.Config.baseUrl + css + ".css";
        link.rel = 'stylesheet';
        link.type = 'text/css';
        if (typeof onLoad == "function") {
            link.onload = onLoad;
        }
        head.appendChild(link);
        return this;
    }

    var data = {};
    this.set = function (key, val) {
        data[key] = val;
        return this;
    }
    this.get = function (key) {
        return data[key];
    }
    this.remove = function (key) {
        delete data[key];
        return this;
    }
    this.has = function (key) {
        return typeof data[key] == "undefined" ? false : true;
    }

    this.params = (function () {
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

    function AbstractModel(name) {
        this.name = name;
        this.call = function (method, params, callback) {
            if (typeof params == "function" && callback == undefined) {
                callback = params;
                params = [];
            }
            var request = flare.Ajax.request(function (response, status) {
                callback(new flare.Data(flare.jsonize(response)), status);
            });
            var url = self.Config.baseUrl + self.Config.pageId + "/" + (this.name + "/" + method).bin2hex() + self.Config.model.ApiExtension;
            request.open(flare.Ajax.METHOD_POST, url, true);
            request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            request.setRequestHeader(flare.Ajax.HEADER_NAME, flare.Ajax.HEADER_VALUE);
            request.setRequestHeader(self.Config.header, self.Config.token);
            for (i in params) {
                params[i] = "params[]=" + params[i];
            }
            request.send(params.join("&"));
        }
    }

    this.Service = new function () {
        this.post = function (url, data, done) {
            flare.Ajax.post(self.Config.baseUrl + url.ltrim("/"), data, done);
        }
        this.get = function (url, data, done) {
            flare.Ajax.get(self.Config.baseUrl + url.ltrim("/"), data, done);
        }
    }

    this.Model = new function () {
        this.create = function (name, methods) {
            name = name.capitalize();
            if (this[name] == undefined) {
                this[name] = new AbstractModel(name);
            }
            if (methods != undefined) {
                for (m in methods) {
                    this[name][m] = methods[m];
                }
            }
            return this;
        }
    }

    function AbstractController(name) {
        var controllerStorage = {};
        this.current;
        this.name = name;
        this.actions = {};
        this.execute = function (name, evt) {
            this.current = this.actions[name];
            this.current.view.render();
            this.current(evt);
        }
        this.action = function (name, methods) {
            var view;
            if (typeof name == "object") {
                methods = name;
                for (method in methods) {
                    var methodSuffixed = method + self.Config.controller.actionSuffix;
                    var type = typeof this.actions[methodSuffixed];
                    if (type == "object") {
                        view = this.actions[methodSuffixed].view;
                    }
                    this.actions[methodSuffixed] = methods[method];
                    this.actions[methodSuffixed].view = view ? view : self.View.create(this.name, methodSuffixed);
                }
            } else {
                var type = typeof this.actions[name + self.Config.controller.actionSuffix];
                if (type == "object") {
                    view = this.actions[name + self.Config.controller.actionSuffix].view;
                }                
                this.actions[name + self.Config.controller.actionSuffix] = methods;
                this.actions[name + self.Config.controller.actionSuffix].view = view;
            }
            return this;
        }
        this.set = function (key, value) {
            controllerStorage[key] = value;
            return this;
        }
        this.get = function (key) {
            return controllerStorage[key];
        }
        this.remove = function (key) {
            delete controllerStorage[key];
            return this;
        }
        this.has = function (key) {
            return (typeof controllerStorage[key] == "undefined") ? false : true;
        }
    }

    AbstractController.prototype = this.Model;

    this.Controller = new function () {}

    this.View = new function () {
        this.layout = null;
        function Layout(element, id) {
            this.name = id;
            flare.$body = $(element);
            var layoutContentRendered = false;
            var layoutContentStart = null;
            var layoutContentEnd = null;
            var layoutContentInner = null;
            this.render = function () {
                flare.$body.replaceWith(
                    "<" + self.Config.view.tagLayoutReplacement + " id=\"" + this.name + 
                    "\" data-" + self.Config.view.viewTypeAttribute + "=\"" + flare.$body.data(self.Config.view.viewTypeAttribute) + "\">" + flare.$body.html() 
                    + "</" + self.Config.view.tagLayoutReplacement + ">"
                );
                flare.$body = $(document.getElementById(this.name));
            }
            this.setContent = function (content) {
                if (!layoutContentRendered) {
                    var contentContainer = viewClass.get(this.name + this.LAYOUT_CONTENT_ID_SUFFIX);
                    if (contentContainer) {
                        contentContainer.replaceWith(
                            "<script id=\"" + this.name + this.LAYOUT_CONTENT_ID_PREFIX_START + this.LAYOUT_CONTENT_ID_SUFFIX + "\"></script>"
                            + content + "<script id=\"" + this.name + this.LAYOUT_CONTENT_ID_PREFIX_END + this.LAYOUT_CONTENT_ID_SUFFIX + "\"></script>"
                        );
                        layoutContentRendered = true;
                        layoutContentStart = viewClass.get(this.name + this.LAYOUT_CONTENT_ID_PREFIX_START + this.LAYOUT_CONTENT_ID_SUFFIX);
                        layoutContentEnd = viewClass.get(this.name + this.LAYOUT_CONTENT_ID_PREFIX_END + this.LAYOUT_CONTENT_ID_SUFFIX);
                        layoutContentInner = layoutContentStart.nextUntil(layoutContentEnd);
                    }
                    return;
                }
                if (self.Router.previous) self.Router.previous.action.view.setContent(layoutContentInner.html());
                layoutContentInner.html(content);
            }
            this.LAYOUT_CONTENT_ID_SUFFIX = ':content';
            this.LAYOUT_CONTENT_ID_PREFIX_START = ':start';
            this.LAYOUT_CONTENT_ID_PREFIX_END = ':end';
        }
        function ActionView(controller, action) {
            var viewContent;
            this.viewName = controller + "/" + action.substring(0, action.length - self.Config.controller.actionSuffix.length);
            function init() {
                var jqView = viewClass.get(thisClass.viewName);
                if (jqView) {
                    viewContent = jqView.html();
                    jqView.remove();
                }
            }
            this.render = function () {
                this.getLayout().setContent(viewContent);
            }
            this.setContent = function (html) {
                viewContent = html;
            }
            this.getLayout = function () {
                return viewClass.layout;
            }
            var thisClass = this;
            init();
        }
        this.create = function (controller, action) {
            return new ActionView(controller, action);
        }
        this.setLayout = function (layoutId) {
            var layoutIdElement = document.getElementById(layoutId);
            if (layoutIdElement) {
                this.layout = new Layout(layoutIdElement, layoutId);
            }
            return this;
        }
        this.get = function (viewId, withJQuery) {
            withJQuery = withJQuery == undefined ? true : withJQuery;
            var viewObj = document.getElementById(viewId);
            if (viewObj && withJQuery) {
                return $(viewObj);
            }
            return viewObj;
        }
        this.TEMPLATE_TYPE = 'template';
        this.ACTION_TYPE = 'action';
        var viewClass = this;
    }

    this.Component = new function () {
        this.components = {};
        this.bind = function (selector, component) {
            if (this.components[selector] == undefined) this.components[selector] = [ component ];
            else this.components[selector].push(component);
        }
    }

    AbstractController.prototype.goto = function (action) {
        window.location.hash = action.ltrim("/");
    }

    AbstractController.prototype.view = function (action, events) {
        if (events == undefined) {
            events = action;
            action = this.actions;
        } else if (typeof events == "object") {
            if (typeof action == "string") {
                var key = action + self.Config.controller.actionSuffix;
                action = {};
                action[key] = key;
            }
        }
        for (a in action) {
            var view;
            if (this.actions[a] != undefined && this.actions[a].view != undefined) {
                view = this.actions[a].view;
            } else {
                view = self.View.create(this.name, a);
            }
            for (e in events) {
                view[e] = events[e];
            }
            if (this.actions[a] == undefined) {
                this.actions[a] = { view : view };
            } else {
                this.actions[a].view = view;
            }
        }
        return this;
    }

    this.Router = new function () {

        this.EVENT_ROUTER_FOUND = 'found';
        this.EVENT_ROUTER_NOTFOUND = 'notfound';

        this.route = null;
        this.previous = null;
        this._routes = function () {}
        this._hook = {};
        this._hook[this.EVENT_ROUTER_NOTFOUND] = function (evt) {}
        this._hook[this.EVENT_ROUTER_FOUND] = function (route, evt) {}

        function Route(controller, action) {
            this.controller = controller;
            this.action = controller.actions[action];
            this.URI = window.location.hash.substring(1);
        }
        this.init = function (routes) {
            this._routes = routes;
            this._routes();
        }
        this.map = function (id, props) {
            self.Controller[id.capitalize()] = new AbstractController(id);
            return this;
        }
        this.scan = function (evt) {
            var controller = self.Config.controller.defaultController;
            var action = self.Config.controller.defaultAction;
            var uri = window.location.hash.substring(1).split("/");
            if (uri.length > 2) return; 
            if (typeof uri[0] != "undefined" && uri[0]) {
                controller = uri[0];
            }
            if (typeof uri[1] != "undefined" && uri[1]) {
                action = uri[1];
            }
            var capsController = controller.capitalize();
            action = action + self.Config.controller.actionSuffix;
            if (this.route) this.previous = this.route;
            if (typeof self.Controller[capsController] != "undefined" 
                && typeof self.Controller[capsController].actions[action] != "undefined")
            {
                this.route = new Route(self.Controller[capsController], action);
                this._hook[this.EVENT_ROUTER_FOUND](this.route, evt);
                self.Controller[capsController].execute(action, evt);
            } else {
                this.route = null;
                this._hook[this.EVENT_ROUTER_NOTFOUND](evt);
            }
        }
        this[this.EVENT_ROUTER_FOUND] = function (found) {
            this._hook[this.EVENT_ROUTER_FOUND] = found;
        }
        this[this.EVENT_ROUTER_NOTFOUND] = function (notfound) {
            this._hook[this.EVENT_ROUTER_NOTFOUND] = notfound;
        }
    }

    function _run(evt) {
        self.Router.scan(evt);
        for (selector in self.Component.components) {
            for (component in self.Component.components[selector]) {
                if (!self.Component.components[selector][component].element) {
                    self.Component.components[selector][component].bindTo(selector);
                }
                self.Component.components[selector][component].onRouteChange(self.Router.route);
            }
        }
    }

    this.run = function () {
        if (this.get(this.Config.controller.appRunningKey)) {
            return;
        }
        this.set(this.Config.controller.appRunningKey, true);
        $(window).bind("hashchange", _run);
        $(document).ready(function (evt) {
            var layout = $("script[type='" + self.Config.view.appScriptType + "'][data-" + self.Config.view.viewTypeAttribute + "='" + self.View.TEMPLATE_TYPE + "']");
            if (layout.length) {
                self.View.setLayout(layout.attr("id"));
                if (self.View.layout) self.View.layout.render();
            }
            _run(evt);
        });
        return this;
    }

    function DbModel(name) {
        AbstractModel.call(this, name);
        this.all = function () {
            
        }
    }

    this.Db = new function () {
        this.set = function (key, value) {
            window.localStorage.setItem(key, value);
            return this;
        }
        this.has = function (key) {
            return window.localStorage.getItem(key) ? true : false;
        }
        this.get = function (key) {
            return window.localStorage.getItem(key);
        }
        this.remove = function (key) {
            window.localStorage.removeItem(key);
            return this;
        }
        this.size = function () {
            return window.localStorage.length;
        }
        this.onChange = function (callback) {
            $(window).bind("storage", callback);
        }
        this.Model = new function () {
            this.create = function (name, methods) {
                name = name.capitalize();
                if (this[name] == undefined) {
                    this[name] = new DbModel(name);
                }
                if (methods != undefined) {
                    for (method in methods) {
                        this[name][method] = methods[method];
                    }
                }
            }
        }
    }

    AbstractController.prototype.db = this.Db;

    var self = this;
}