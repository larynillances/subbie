//create new form
jQuery(document).ready(function ($) {
    var imageLoading = new Image();
    imageLoading.src = bu + 'images/loading.gif';

    $.fn.newForm = {
        //add overlay to page
        addOverlay: function(){
            $('body').append('<div id="overlay"></div>');
            var ov = $('#overlay');
            ov.css({'display':'inline','opacity':'0','filter':'(opacity=0)'});
            ov.animate({opacity:.8},200);
        },
        //remove overlay to the page
        removeOverlay: function(option){
            var defaults = {
                time: 500,
                delay: 0
            };
            // merge
            var options = $.extend({}, defaults, option);

            $('#overlay')
                .delay(options.delay)
                .fadeOut(
                    options.time,
                    function(e){
                        $("body").css("overflow", "auto");
                        $('#overlay').remove();
                    }
                );
        },
        //close the form
        removeForm: function(option){
            var defaults = {
                callBack: function(e){

                }
            };
            // merge
            var options = $.extend({}, defaults, option);

            $('#closedCustomForm').on('click',function(e){
                $('#customForm').remove();
                $("body").css("overflow", "auto");
                $('#overlay').fadeOut(200,function(e){
                    $('#overlay').remove();
                    options.callBack();
                });
            });
        },
        forceClose: function(option){
            var defaults = {
                callBack: function(e){

                }
            };
            // merge
            var options = $.extend({}, defaults, option);

            $('#customForm').remove();
            $('#overlay').fadeOut(200,function(e){
                $('#overlay').remove();
                $("body").css("overflow", "auto");
                options.callBack();
            });
        },
        addLoadingForm: function(option){
            var defaults = {
                ele: "",
                msg: "Still processing something!",
                toFind: "",
                callBack: function(e){

                }
            };
            // merge
            var options = $.extend({}, defaults, option);
            var defLoader = '';
            defLoader += '<img src="' + bu + 'images/loading.gif" style="width: 418px, height: 70px;" />';
            defLoader += '<br />';
            defLoader += '<span style="color: #f82249;font-size: 25px;">' + options.msg + '</span>';

            var lfEle = '';
            lfEle += '<div id="loadingForm" style="opacity: 0;">';
            if(options.ele){
                lfEle += options.ele;
            }else{
                lfEle += defLoader;
            }
            lfEle += '</div>';
            if($('')){
                this.addOverlay();
            }

            $('body').append(lfEle);
            var lf = $('#loadingForm');
            if(options.toFind !== ""){
                var wid = lf.find(options.toFind).innerWidth();
                lf.animate({
                    width :wid + 25,
                    height:lf.height() + 50
                },600);
            }

            lf.centerForm({
                callBack: function(e){
                    $(this).animate({
                        opacity: 1
                    }, 500);
                }
            });

            options.callBack();
        },
        removeLoadingForm: function(option){
            var defaults = {
                delay: 200,
                hideLoading: true,
                callBack: function(e){

                }
            };
            // merge
            var options = $.extend({}, defaults, option);

            $('#loadingForm').remove();
            if(options.hideLoading){
                $('#overlay').fadeOut(options.delay,function(e){
                    $(this).remove();
                    options.callBack();
                });
            }
        },
        //show the form
        addNewForm: function(option){
            var defaults = {
                "appendToElement": '',
                "isDefault": true,
                "title": "QUERY",
                "url": "",
                "elemento": "",
                "data": {},
                "disableClose": false,
                "setSize": false,
                "autoHide": false,
                "autoDelay": 800,
                "toFind": "",
                "customForm":{
                    "width":"400px",
                    "height":"200px",
                    "border": "#000 1px solid;"
                },
                "customFormContent": "text-align: center;",
                "customFormHeader": "background: rgb(0, 0, 0);",
                callBack: function(e){

                },
                closeEvent: function(e){

                }
            };
            // merge
            var options = $.extend({}, defaults, option);
            this.addOverlay();

            var body = options.appendToElement ? options.appendToElement : $('body');

            var ele = '';
            if(options.isDefault){
                ele = '<div id="customForm">';
                ele += '<div id="customFormHeader" style="'+options.customFormHeader+'">';
                ele += '<span id="headerTitleValue" style="white-space: nowrap;">' + options.title + '</span>';
                if(options.autoHide == false && options.disableClose == false){
                    ele += '<span id="closedCustomForm" title="close">x</span>';
                }
                ele += '</div>';
                ele += '<div id="customFormContent" style="'+options.customFormContent+'">';
                if(options.elemento !== ""){
                    ele += options.elemento;
                }
                ele += '</div>';
                ele += '</div>';
                body.append(ele);

                var cf = $('#customForm');
                var cfc = $('#customFormContent');
                var t = (options.customForm.height/2);
                var l = (options.customForm.width/2);
                t *= -1;
                l *= -1;
                cf
                    .css({
                        "width":options.customForm.width,
                        "height":options.customForm.height,
                        "border":options.customForm.border,
                        "top": "50%",
                        "left": "50%",
                        "marginLeft": l,
                        "marginTop": t
                    })
                    .show("scale", 300, function(e){
                        if(options.url !== ""){
                            cfc
                                .load(options.url,options.data,function(e){
                                    if(options.setSize == false){
                                        var wid = cfc.width();
                                        if(options.toFind !== ""){
                                            wid = cfc.find(options.toFind).innerWidth();
                                        }

                                        cf.animate({
                                            width :wid + 25,
                                            height:cfc.height() + 50
                                        },600,function(e){
                                            cf.centerForm();
                                        });
                                    }
                                });
                        }

                        if(options.autoHide == true){
                            cf
                                .delay(options.autoDelay)
                                .fadeOut(500,function(e){
                                    $('#overlay').fadeOut(500,function(e){
                                        $('#overlay').remove();
                                        cf.remove();
                                    });
                                });
                        }
                    });

                this.removeForm({
                    callBack: options.closeEvent
                });
            }else{
                ele = '<div class="myNewForm">';
                ele += '</div>';
                body.append(ele);

                var parentEl = $('.myNewForm');

                parentEl.loadingForm();
                parentEl
                    .css({
                        "z-index":'999',
                        "position":"absolute"
                    })
                    .load(options.url,function(e){
                        parentEl
                            .prepend("<div id='closeDynaAddedButton' title='close'>x</div>")
                            .centerForm();
                        var cdb = $('#closeDynaAddedButton');
                        cdb.topLeftie(parentEl);
                        cdb.live('click',function(e){
                            cdb.remove();
                            parentEl.fadeOut(300,function(e){
                                parentEl.remove();
                                var ob = $('#overlay');
                                ob.fadeOut(500,function(e){
                                    ob.remove();
                                });
                            });
                        });
                    });
            }

            options.callBack();
        },
        //replace the form content
        replaceContent: function(option){
            var defaults = {
                "title": "",
                "url": "",
                "elemento": "",
                "disableClose": false,
                "data": {},
                "toFind": "",
                "customForm":{
                    "width":"400px",
                    "height":"200px",
                    "border": "#000 1px solid;"
                }
            };
            // merge
            var options = $.extend({}, defaults, option);

            var cf = $('#customForm');
            var cfc = $('#customFormContent');
            var h = $('#headerTitleValue');

            var isOkResize = false;
            if(options.title !== ""){
                h.html(options.title);
            }

            if(options.disableClose == true){
                $('#closedCustomForm').remove();
            }

            var t = (options.customForm.height/2);
            var l = (options.customForm.width/2);
            t *= -1;
            l *= -1;
            cf
                .css({
                    "width":options.customForm.width,
                    "height":options.customForm.height,
                    "border":options.customForm.border,
                    "top": "50%",
                    "left": "50%",
                    "marginLeft": l,
                    "marginTop": t
                });

            cfc.html(options.elemento); //temp content

            if(options.url !== ""){
                cfc.load(options.url,options.data,function(e){
                    var wid = cfc.width();
                    var hei = cfc.width();
                    if(options.toFind !== ""){
                        wid = cfc.find(options.toFind).innerWidth();
                        hei = cfc.find(options.toFind).innerHeight();
                    }

                    cf.animate({
                        width :wid + 25,
                        height:hei + 50
                    },600,function(e){
                        cf.centerForm();
                    });
                });
            }else{
                if(options.elemento !== ""){
                    cfc.html(options.elemento);
                    var wid = cfc.width();
                    var hei = cfc.width();
                    if(options.toFind !== ""){
                        wid = cfc.find(options.toFind).innerWidth();
                        hei = cfc.find(options.toFind).innerHeight();
                    }

                    cf.animate({
                        width :wid + 25,
                        height:hei + 50
                    },600,function(e){
                        cf.centerForm();
                    });
                }
            }
        },
        //resize the form
        formSizeChange: function(option){
            var defaults = {
                "toFind": ""
            };
            // merge
            var options = $.extend({}, defaults, option);

            var cf = $('#customForm');
            var cfc = $('#customFormContent');
            var wid = cfc.width();
            var hei = cfc.height();
            if(options.toFind !== ""){
                wid = cfc.find(options.toFind).width();
                hei = cfc.find(options.toFind).height();
            }

            cf.animate({
                width :wid + 25,
                height:hei + 50
            },600,function(e){
                cf.centerForm();
            });
        },
        //add a query form (Y or N)
        formDeleteQuery: function(option){
            var defaults = {
                title: 'Delete Entry',
                msg: 'Are you sure you want to delete this?',
                elemento: '',
                customFormHeader: 'background: #ff0d0d',
                appendToElement: '',
                "customForm":{
                    "width":"225px",
                    "height":"120px"
                },
                superClass: 'deleteQueryBtn',
                buttonType: 'button',
                buttonName: 'query',
                buttonAlign: 'center',
                disableClose: false,
                buttonStyle: 'padding: 5px;min-width: 20px;white-space: nowrap;margin: 0 3px;',
                button: {
                    yesBtn: 'Yes',
                    noBtn: 'No'
                },
                toFind: '.queryTable',
                callBack: function(e){

                }
            };
            // merge
            var options = jQuery.extend({}, defaults, option);

            var elemento = '<table class="queryTable" style="text-align: center;font-size: 12px;border-collapse: collapse;">' +
                '<tr>' +
                    '<td style="white-space: nowrap;">' + options.msg + '</td>' +
                '</tr>' +
                '<tr>' +
                    '<td style="white-space: nowrap;text-align: ' + options.buttonAlign + ';">';

                    $.each(options.button, function(key, args){
                        elemento += '<input type="' + options.buttonType + '" name="' + options.buttonName + '" value="' + args + '" ' +
                            'class="' + options.superClass + ' ' + key + ' pure_black" ' +
                            'id="' + key + '"' +
                            ' style="' + options.buttonStyle + '" />';
                    });

                elemento += '</td>' +
                '</tr>' +
            '</table>';

            this.addNewForm({
                title: options.title,
                elemento: elemento,
                customFormHeader: options.customFormHeader,
                customForm: options.customForm,
                appendToElement: options.appendToElement,
                disableClose: options.disableClose,
                callBack: function(e){
                    $(this).newForm.formSizeChange({
                        toFind: '.queryTable'
                    });
                    options.callBack();
                }
            });
        }
    }
});

jQuery.fn.loadingForm = function () {
    var parentEl = $('.myNewForm');

    var el = "";
    el += '<div id="preLoadingForm">';
    el += 'Please wait!<br />';
    el += 'Page is still Loading...<br />';
    el += '<img id="loading" src="'+baseUrl+'img/loading.gif" width="80" />';
    el += '</div>';

    parentEl.html(el);
    var thisId = $('#preLoadingForm');

    $('#loading').load(function() {
        thisId.centerForm();
    });
};

jQuery.fn.centerForm = function (option) {
    var defaults = {
        callBack: function(e){

        }
    };
    // merge
    var options = $.extend({}, defaults, option);

    var top = (jQuery(this).outerHeight(false)/2);
    var left = (jQuery(this).outerWidth(false)/2);
    top *= -1;
    left *= -1;

    this
        .css({
            "position":"fixed",
            "z-index": 999,
            "top": "50%",
            "left": "50%"
        })
        .animate({
            "marginLeft": left,
            "marginTop": top
        },300, options.callBack);
};

jQuery.fn.topLeftie = function (el) {
    var top = (el.outerHeight()/2) + (jQuery(this).outerHeight(false)/2);
    var left = (el.outerWidth()/2) + (jQuery(this).outerWidth(false)/2);
    top *= -1;
    left *= -1;

    this.css({
        "position":"fixed",
        "margin": top + "px " + left + "px",
        "top": "50%",
        "left": "50%"
    });
};

jQuery.strPad = function(i, l, s) {
    var o = i.toString();
    if (!s) {
        s = '0';
    }

    while (o.length < l) {
        o = s + o;
    }

    return o;
};