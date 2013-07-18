function trim(str, replace) {
    return str.replace((!replace) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^'+replace+'+|'+replace+'+$', 'g'), '');
}

String.prototype.trim = function(chr){
    return this.replace((!chr) ? new RegExp('^\\s+|\\s+$', 'g') : new RegExp('^'+chr+'+|'+chr+'+$', 'g'), '');
}

function rtrim(str, replace) {
    return str.replace((!replace) ? new RegExp('\\s+$') : new RegExp(replace+'+$'), '');
}

String.prototype.rtrim = function(chr){
    return this.replace((!chr) ? new RegExp('\\s+$') : new RegExp(chr+'+$'), '');
}

function ltrim(str, replace) {
    return str.replace((!replace) ? new RegExp('^\\s+') : new RegExp('^'+replace+'+'), '');
}

String.prototype.ltrim = function(chr){
    return this.replace((!chr) ? new RegExp('^\\s+') : new RegExp('^'+chr+'+'), '');
}

function str_pad(str, width, chr) {
    chr = chr || ' ';
    return str.length >= width ? str : new Array(width - str.length + 1).join(chr) + str;
}

String.prototype.pad = function (width, z) {
    return str_pad(this.toString(), width, z);
}

function clone(obj) {
    return new function() {
        this.prototype = obj;
    }
}

Object.prototype.clone = function () {
    return clone(this);
}

var Flare = new function () {

    var self = this;

    Application = function () {

    }
    
    this.addEvent = function(element, event, handler) {
        if (typeof element == "string") {
            element = document.getElementById(element);
        }
        if (typeof handler != "function") {
            console.log("Event handler should be a function");
        }
        handler.prototype = element;
        if (element.addEventListener) {
            element.addEventListener(event, handler, false);
        } else if (element.attachEvent) {
            event = "on" + ltrim(event, "on").toLowerCase();
            element.attachEvent(event, handler);
        } else {
            console.log("No event attached");
        }
        return self;
    }

    this.require = function (module, success) {
        return self;
    }

    this.createApplication = function (module) {

    }
}

Controller = function () {
}

var app = Flare.createApplication(Controller);
app.events.bind("btn", "click", function () {
    app.events.fire("click");
});

HTMLElement.prototype.addEvent = function (event, handler) {
    Flare.addEvent(this, event, handler);
}