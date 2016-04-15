jQuery.fn.checkingMail = function (option) {
    var el = $(this);
    el
        .unbind('focusout, change')
        .live('focusout, change', function(e) {
            e.stopPropagation();
            var options = $(this).getOptions(option);
            $(this).validateMail(options);
        });
};

jQuery.fn.getOptions = function (option) {
    var defaults = {
        submit: '',
        thisEl: $(this),
        fieldName: $(this).attr('name'),
        postName: $(this).attr('name'),
        includeElement: '',
        minimumLength: 1,
        isMail: true,
        checkOption:{},
        customError: '',
        callBack: function(e){

        }
    };
    // merge
    var options = $.extend({}, defaults, option);

    var postArray = [];
    postArray.push('"' + options.postName + '": "' + $(this).val() + '"');
    if(options.includeElement){
        var ie = options.includeElement;
        if($.isArray(ie)) {
            for (var k in ie) {
                var element = ie[k];
                postArray.push('"' + element.attr('name') + '": "' + element.val() + '"');
            }
        }
        else{
            postArray.push('"' + ie.attr('name') + '": "' + ie.val() + '"');
        }
    }
    var postString = '{' + postArray.join(',') + '}';
    var fn = $.parseJSON(postString);

    return $.extend(true, options, options, { checkOption: { field: fn }});
};

jQuery.fn.validateMail = function(options){
    var errType = '';
    var customError = options.customError;
    var emailReg = /^([\w\.]+@([\w-]+\.)+[\w-]{2,4})?$/;

    var emailAddressVal = this.val();

    //start the filter option if to execute this function
    var isProceed = true;
    if(!$(this).val()){
        isProceed = false;
    }

    if(isProceed && options.includeElement){
        var ie = options.includeElement;
        if($.isArray(ie)) {
            for (var k in ie) {
                var element = ie[k];

                if(!element.val()){
                    isProceed = false;
                }
            }
        }
        else{
            if(!ie.val()){
                isProceed = false;
            }
        }
    }

    if(isProceed){
        if(emailAddressVal == '') {

        }
        else if(!emailReg.test(emailAddressVal) && options.isMail) {
            errType = 'invalid';
        }
        else{
            if(Object.keys(options.checkOption).length != 0 && emailAddressVal.length >= options.minimumLength){
                var res = false;
                if($.isArray(options.checkOption.url)){
                    var ele = $(this);
                    $.each(options.checkOption.url, function(key, url){
                        var thisOption = options.checkOption;
                        thisOption.url = url;

                        res = ele.checkIfExist(thisOption);
                        if(res){
                            if($.isArray(options.customError)){
                                customError = options.customError[key];
                            }

                            return false;
                        }
                    });
                }
                else{
                    res = $(this).checkIfExist(options.checkOption);
                }

                errType = res ? 'exist' : '';
            }
        }
    }

    if(errType){
        options.submit.controlSubmit({isDisable: true});
        var msg = '';

        switch(errType){
            case 'invalid':
                msg = 'Invalid Email!';
                break;
            case 'exist':
                msg = customError ? customError : 'Already exist!';
                break;
        }

        this.displayError({
            fieldName: options.fieldName,
            msg: msg,
            thisEl: options.thisEl,
            callBack: options.callBack()
        });
    }
    else{
        this.hideError({
            thisEl: options.thisEl,
            fieldName: options.fieldName,
            callBack: options.callBack()
        });

        options.submit.controlSubmit();
    }

    return errType;
};

jQuery.fn.controlSubmit = function(opt){
    var defaults = {
        isDisable: false
    };
    var options = $.extend({}, defaults, opt);

    if(options.isDisable){
        $(this)
            .attr('disabled','disable')
            .addClass('disabled');
    }else{
        if($('.hasError').length == 0){
            $(this)
                .removeAttr('disabled')
                .removeClass('disabled');
        }
    }
};

jQuery.fn.checkIfExist = function(opt){
    var defaults = {
        dbname: '',
        url: '',
        field: {},
        except: {}
    };
    var options = $.extend({}, defaults, opt);

    var res = false;
    if(options.url && Object.keys(options.field).length != 0){
        $.ajax({
            url:  options.url,
            type: "POST",
            data: {
                dbname: options.dbname,
                field: options.field,
                except: options.except
            },
            async:false,
            success: function(re){
                res = $.inArray(re, ["true", "1", 1]) != -1;
            }
        });
    }

    return res;
};

jQuery.fn.displayError = function (opt){
    var defaults = {
        fieldName: $(this).attr('name'),
        "msg": "",
        "type": "error",
        thisEl: $(this),
        callBack: function(e){

        }
    };
    var options = $.extend({}, defaults, opt);

    var thisEl = options.thisEl;
    var thisParentEl = options.thisEl.parent();
    var epo = $(".error_" + options.fieldName);
    if(epo.length == 0){
        thisParentEl.append('<div class="error_pop_out error_' + options.fieldName + '" style="font-size: 12px;">' + options.msg + '</div>');
        //the Error Message Element
        var thisError = thisParentEl.find(".error_pop_out");

        var marginLeft = thisEl.innerWidth() + parseInt(thisEl.css('paddingLeft'));
        //Add Half of the Element Height and Padding Top
        var marginTop = (thisEl.innerHeight() + parseInt(thisEl.css('paddingTop')))/2;
        //Add the Half of the Error Message Height and Padding Top
        marginTop += (thisError.innerHeight() + parseInt(thisError.css('paddingTop')))/2;
        //Convert to Negative Value
        marginTop *= -1;

        $('.error_' + options.fieldName)
            .css({
                marginTop: marginTop + "px",
                marginLeft: marginLeft + "px"
            })
            .fadeIn(300);
    }
    else{
        epo.html(options.msg);
    }

    options.callBack();
    options.thisEl
        .addClass('hasError')
        .showErrorBorder();
};

jQuery.fn.hideError = function (opt){
    var defaults = {
        thisEl: $(this),
        fieldName: $(this).attr('name'),
        callBack: function(e){

        }
    };
    var options = $.extend({}, defaults, opt);

    var thisEl = options.thisEl.parent();
    var epo = $(".error_" + options.fieldName);

    epo.fadeOut(800,function(e){
        $(this).remove();
    });

    options.callBack();

    options.thisEl
        .removeClass('hasError')
        .showErrorBorder();
};

jQuery.fn.showErrorBorder = function (){
    var borderPadding = parseInt($(this).css('border-width')) - 1;
    var paddingTop = parseInt($(this).css('padding-top')) + borderPadding;
    var paddingLeft = parseInt($(this).css('padding-left')) + borderPadding;

    $(this).css({
        border: '1px solid #' + ($(this).hasClass('hasError') ? 'F00' : 'CCC'),
        padding: paddingTop + 'px ' + paddingLeft + 'px'
    });
};