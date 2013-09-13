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

var flare = {};
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

flare.ViewComponent = function (methods) {
    this.element = null;
    function init (methods) {
        for (m in methods) {
            self[m] = methods[m];
        }
    }
    this.bindTo = function (selector) {
        var selected = $(selector);
        if (selected.length) this.element = selected;
    }
    this.onRouteChange = function (route) {}
    var self = this;
    init(methods);
}

flare.TabbedPane = function (methods) {
    flare.ViewComponent.call(this, methods);
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
}

flare.Form = function (methods) {
    flare.ViewComponent.call(this, methods);
    this.async = false;
    this.onSubmit = function (evt) {

    }
    this.submit = function () {
        this.onSubmit(evt);
    }
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
                done(request.responseText);
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
                done(request.responseText);
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
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    return;
                }
                callback(new flare.Data(response), status);
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
            flare.Ajax.post(url, data, done);
        }
        this.get = function (url, data, done) {
            flare.Ajax.get(url, data, done);
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
                    if (type != "undefined" && type == "object") {
                        view = this.actions[methodSuffixed].view;
                    }
                    this.actions[methodSuffixed] = methods[method];
                    this.actions[methodSuffixed].view = view ? view : self.View.create(this.name, methodSuffixed);
                }
            } else {
                var type = typeof this.actions[name + self.Config.controller.actionSuffix];
                if (type != "undefined" && type == "object") {
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
            if (typeof controllerStorage[key] == "undefined") {
                return false;
            }
            return true;
        }
    }

    AbstractController.prototype = this.Model;

    this.Controller = new function () {}

    this.View = new function () {
        this.layout = null;
        function Layout(element, id) {
            this.name = id;
            var templateObj = $(element);
            var layoutContentRendered = false;
            var layoutContentStart = null;
            var layoutContentEnd = null;
            var layoutContentInner = null;
            this.render = function () {
                templateObj.replaceWith(
                    "<" + self.Config.view.tagLayoutReplacement + " id=\"" + this.name + 
                    "\" data-" + self.Config.view.viewTypeAttribute + "=\"" + templateObj.data(self.Config.view.viewTypeAttribute) + "\">" + templateObj.html() 
                    + "</" + self.Config.view.tagLayoutReplacement + ">"
                );
                templateObj = $(document.getElementById(this.name));
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
        this.route = null;
        this.previous = null;
        this._routes = function () {}
        this._hook = { found : function (route, evt) {} , notfound : function (evt) {} };
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
                this._hook['found'](this.route, evt);
                self.Controller[capsController].execute(action, evt);
            } else {
                this.route = null;
                this._hook['notfound'](evt);
            }
        }
        this.found = function (found) {
            this._hook['found'] = found;
        }
        this.notfound = function (notfound) {
            this._hook['notfound'] = notfound;
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
            console.log("Application is already running");
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