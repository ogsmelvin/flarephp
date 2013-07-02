String.prototype.trim = function(chr){
    return this.replace((!chr) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^'+chr+'+|'+chr+'+$', 'g'), '');
}

String.prototype.rtrim = function(chr){
    return this.replace((!chr) ? new RegExp('\\s+$') : new RegExp(chr+'+$'), '');
}

String.prototype.ltrim = function(chr){
    return this.replace((!chr) ? new RegExp('^\\s+') : new RegExp('^'+chr+'+'), '');
}

clone = function(obj){
    return new function(){
        this.prototype = obj;
    }
}

//-----------------------------------------------------------------------
ImagePreview = function(selector, previewElement){
    var previewElem = $(previewElement);
    var elem = $(selector);
    var self = this;
    var filename;
    var imageSrc;
    function init(){
        elem.change(function(e){
            e.preventDefault();
            var file = e.target.files[0];
            if(!file.type.match('image.*')){
                error(e);
                return;
            }
            var reader = new FileReader();
            reader.onload = (function(theFile){
                return function(e){
                    filename = theFile.name;
                    imageSrc = e.target.result;
                    previewElem.attr("src", imageSrc);
                    preview(e.target.result, theFile, e);
                };
            })(file);
            reader.readAsDataURL(file);
        });
    }
    function error(e){
        console.log("invalid image");
    }
    function preview(result, filename, event){
        console.log("preview");
    }
    this.onError = function(evt){
        error = evt;
        return self;
    }
    this.onPreview = function(evt){
        preview = evt;
        return self;
    }
    this.getFilename = function(){
        return filename;
    }
    this.getPreview = function(){
        return previewElem;
    }
    this.hasImage = function(){
        return filename == undefined || !filename ? false : true;
    }
    this.getImage = function(){
        return imageSrc;
    }
    init();
}

WebForm = function(){
    
}

EmailField = function(element){
    var emailField = $(element);
    this.isValid = function(){
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,10})+$/;
        if(!filter.test(emailField.val())){
            return false;
        }
        return true;
    }
};

//-----------------------------------------------------------------------

(function($){
    $.fn.ImagePreview = function(previewElement){
        return new ImagePreview($(this), previewElement);
    }
    $.fn.EmailField = function(){
        return new EmailField($(this));
    }
}(jQuery));

Router = function(){
    var routes = {}
    this.set = function(id, uri, evt){
        if(uri != "/"){
            uri = uri.rtrim("/");
        }
        evt.path = uri;
        evt.id = id;
        routes[id] = {
            id : id,
            path : uri,
            callback : evt
        };
        return self;
    }
    this.all = function(){
        return routes;
    }
    this.remove = function(key){
        delete routes[key];
        return self;
    }
    this.clearAll = function(){
        routes = {};
    }
    this.getByPath = function(path){
        if(path != "/"){
            path = path.rtrim("/");
        }
        for(i in routes){
            if(routes[i].path == path){
                return routes[i];
            }
        }
    }
    this.getById = function(id){
        return routes[id];
    }
    var self = this;
}

Storage = function(){

}

Registry = function(){
    var content;
    var self = this;
    this.init = function(){
        content = {};
    }
    this.set = function(key, value){
        content[key] = value;
        return self;
    }
    this.get = function(key){
        return content[key];
    }
    this.remove = function(key){
        delete content[key];
        return self;
    }
    this.clear = function(){
        self.init();
    }
    this.all = function(){
        return content;
    }
    this.init();
}

var App = new function(){
    var self = this;
    var containerId;
    var container;
    var afterRenderCallback;
    var beforeRenderCallback;
    var currentRoute;
    this.registry;
    this.router;
    this.body = $("body");
    function setCurrentRoute(route){
        currentRoute = route;
    }
    this.enableOnLoad = function(){
        $(window).load(function(e){
            self.run();
        });
        $(window).bind("popstate", function(){
            
        });
        return self;
    }
    this.init = function(){
        self.registry = new Registry();
        self.router = new Router();
    }
    this.setContainerId = function(id){
        containerId = "#" + id.ltrim("#");
        container = $(containerId);
        return self;
    }
    this.set = function(id, uri, evt){
        self.router.set(id, uri, evt);
        return self;
    }
    this.run = function(params){
        var route = self.router.getByPath(window.location.pathname);
        if(route != undefined){
            window.history.pushState({url : route.path, title : route.id}, route.id, route.path);
            setCurrentRoute(route);
            route.callback(params);
        }
        return self;
    }
    this.use = function(id, params){
        var route = self.router.getById(id);
        if(route != undefined){
            window.history.pushState({url : route.path, title : id}, id, route.path);
            setCurrentRoute(route);
            route.callback(params);
        }
        return self;
    }
    this.render = function(ajaxUrl, callback){
        if(!containerId){
            console.log("Container must be set first");
            return;
        }
        container.data("url", ajaxUrl);
        if(beforeRenderCallback != undefined){
            beforeRenderCallback();
        }
        container.load(ajaxUrl, function(response, textStatus, xhr){
            if(currentRoute){
                window.history.replaceState({url : currentRoute.path, title : currentRoute.id, content : response}, currentRoute.id, ajaxUrl);
            }
            if(textStatus == "error"){
                alert("Error occured : " + xhr.status + " " + xhr.statusText);
            } else if(typeof callback != "undefined"){
                callback(response, textStatus, xhr);
            }
            if(afterRenderCallback != undefined){
                afterRenderCallback(response, textStatus, xhr);
            }
        });
        return self;
    }
    this.script = function(src, attributes){
        var strAttributes = "";
        if(attributes != undefined){
            
        }
        if($.isArray(src)){
            for(i in src){
                src[i] = src[i].rtrim(".js") + ".js";
                self.body.append("<script src=\"" + src[i] + "\"></script>");
            }
        } else {
            src = src.rtrim(".js") + ".js";
            self.body.append("<script src=\"" + src + "\"></script>");
        }
    }
    this.style = function(href){
        
    }
    this.beforeRender = function(evt){
        beforeRenderCallback = evt;
        return self;
    }
    this.afterRender = function(evt){
        afterRenderCallback = evt;
        return self;
    }
    this.init();
}