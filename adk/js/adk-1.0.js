var Adk = {
    validEmail : function checkEmail(email) {
        var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,10})+$/;
        if (!filter.test(email)) {
            return false;
        }
        return true;
    },
    param : function(key) {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++){
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars[key];
    }
};

(function($){

    var adkGridMethods = {
        reload : function(option){
            setTimeout(function(){
                adkGridMethods.load(option, function(option){
                    adkGridMethods.reload(option);
                });
            }, option.interval);
        },
        load : function(option, cb){
            $.ajax({
                url : option.url,
                data : option.data,
                dataType : option.dataType,
                type : option.type,
                beforeSend : function(xhr){
                    if(typeof option.beforeSend != "undefined"){
                        option.beforeSend(xhr);
                    }
                },
                success : function(data){
                    if(typeof option.success != "undefined"){
                        option.success(data);
                    }
                    cb(option);
                }
            });
        }
    }

    $.fn.adkImageField = function(option){
        if(typeof option != "object" && option == false){
            $(this).unbind("change");
            return;
        }
        $(this).change(function(event){
            event.stopPropagation();
            event.preventDefault();
            var files = event.target.files;
            for (var i = 0, f; f = files[i]; i++) {
                var reader = new FileReader();
                if (!f.type.match('image.*')) {
                    alert("Image only!!");
                    continue;
                }
                reader.onload = (function(theFile){
                    return function(e) {
                        if(typeof option.preview != "undefined"){
                            $(option.preview).attr("src", e.target.result);
                        }
                    };
                })(f);
                reader.readAsDataURL(f);
            }
        });
    }

    $.fn.adkNumericField = function(turnOn){
        turnOn = typeof turnOn == "undefined" ? true : turnOn;
        if(turnOn){
            $(this).keydown(function(e){
                var keyPressed;
                if (!e) var e = window.event;
                if (e.keyCode) keyPressed = e.keyCode;
                else if (e.which) keyPressed = e.which;
                var hasDecimalPoint = (($(this).val().split('.').length-1)>0);
                if ( keyPressed == 46 || keyPressed == 8 ||((keyPressed == 190||keyPressed == 110)&&(!hasDecimalPoint)) || keyPressed == 9 || keyPressed == 27 || keyPressed == 13 ||
                    // Allow: Ctrl+A
                    (keyPressed == 65 && e.ctrlKey === true) ||
                    // Allow: home, end, left, right
                    (keyPressed >= 35 && keyPressed <= 39)) {
                    // let it happen, don't do anything
                    return;
                } else {
                // Ensure that it is a number and stop the keypress
                    if (e.shiftKey || (keyPressed < 48 || keyPressed > 57) && (keyPressed < 96 || keyPressed > 105 )) {
                        e.preventDefault();
                    }
                }
            });
            return;
        }
        $(this).unbind("keydown");
    }

    // url : required
    // interval
    // type
    // data
    // populdate 
    // page
    $.adkGrid = function(option, value){
        if(typeof option == "object"){
            if(typeof option.url == "undefined"){
                console.log("URL is not defined");
                return;
            }

            option.interval = typeof option.interval == "undefined" ? 10000 : option.interval;
            option.type = typeof option.type == "undefined" ? "GET" : option.type;
            option.data = typeof option.data == "undefined" ? {} : option.data;
            option.dataType = typeof option.dataType == "undefined" ? "JSON" : option.dataType;
            option.populate = typeof option.populate == "undefined" ? false : option.populate;
            option.page = typeof option.page == "undefined" ? 1 : option.page;
            option.data.page = option.page;

            if(option.populate){
                adkGridMethods.load(option, function(option){
                    adkGridMethods.reload(option);
                });
                return;
            }
            adkGridMethods.reload(option);
        }
    }

    $.fn.adkImagePreload = function(preloaderOptions){
        preloaderOptions = typeof preloaderOptions != "object" ? {} : preloaderOptions;
        var parent = $(this).parent();
        if(!$(parent).is("a")){
            $(this).wrap("<div class=\"adk-preloader\"></div>");
        }
        if(!$(parent).hasClass("adk-preloader")){
            $(parent).addClass("adk-preloader");
        }
        if(typeof preloaderOptions.url != "undefined"){
            $(parent).css("backround-image", "url(" + preloaderOptions.url + ")");
        }
        $(this).hide();
        $(parent).prepend("<img class=\"preloader\" src=\"/assets/adk/images/adk_preloader.gif\">");
        if($(this).height()){
            $(parent).find(".preloader").css("height", $(this).height());
        }
        if($(this).width()){
            $(parent).find(".preloader").css("width", $(this).width());
        }
        $(this).load(function(event){
            $(parent).find(".preloader").remove();
            $(this).show();
            if(typeof preloaderOptions.success != "undefined"){
                preloaderOptions.success(event);
            }
        });
    }

})(jQuery);