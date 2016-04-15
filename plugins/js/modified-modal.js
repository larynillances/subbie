(function( $ ){
    $.fn.modifiedModal = function(option) {
        var defaults = {
            "type": 'default',
            "url": '',
            "html": '',
            "data": {},
            "title": 'My Modal'
        };
        // merge
        var options = $.extend({}, defaults, option);
        var modal_title = $('.modal-title');

        switch (options.type){
            case 'large':
                if(options.url != ''){
                    $('.lg-page-load').load(options.url,option.data);
                }else{
                    $('.lg-page-load').html(options.html);
                }
                modal_title.html(options.title);
                $('.largeModal').modal();
                break;
            case 'small':
                if(options.url != ''){
                    $('.sm-page-load').load(options.url,option.data);
                }else{
                    $('.sm-page-load').html(options.html);
                }
                modal_title.html(options.title);
                $('.smallModal').modal();
                break;
            default:
                if(options.url != ''){
                 $('.df-page-load').load(options.url,option.data);
                 }else{
                 $('.df-page-load').html(options.html);
                 }
                 modal_title.html(options.title);
                 $('.defaultModal').modal();
                break;
        }

    };
})( jQuery );