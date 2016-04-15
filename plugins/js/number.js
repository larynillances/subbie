(function( $ ){
    $.fn.numberOnly = function(option) {
        var defaults = {
            "wholeNumber": false,
            "isForContact": false,
            "alertSomething": true,
            "isPercentage": false,
            "maxVal": 100,
            "hasMaxChar": false,
            "maxCharLen": 0
        };
        // merge
        var options = $.extend({}, defaults, option);

        if(options.hasMaxChar){
            $(this).attr('maxLength', options.maxCharLen);
        }

        $(this).keydown(function(event) {
            // Allow: backspace, delete, tab, escape, and enter
            if ( event.which == 46 || event.which == 8 || event.which == 9 || event.which == 27 || event.which == 13 ||
                    // Allow: Ctrl+A
                    (event.which == 65 && event.ctrlKey === true) ||
                    // Allow: home, end, left, right
                    (event.which >= 35 && event.which <= 39) ||
                    // Allow: decimal
                    (((event.which == 110) || (event.which == 190)) && options.wholeNumber == false) ||
                    //Allow: dash
                    (((event.which == 109) || (event.which == 189) )&& options.isForContact == true)
                ) {
                    //Allow: decimal to fire once only
                    if((event.which == 110 || event.which == 190) && $(this).val().indexOf('.') != -1){
                        event.preventDefault();
                    }
                    // let it happen, don't do anything

                    return;
            }
            else {
                // Ensure that it is a number and stop the keypress
                if (event.shiftKey || (event.which < 48 || event.which > 57) && (event.which < 96 || event.which > 105 )) {
                    event.preventDefault();
                }
            }
        })
        .keyup(function(e){
            if(options.isPercentage){
                if($(this).val() > options.maxVal){
                    $(this).val(options.maxVal);
                }
            }
        });
    };
})( jQuery );