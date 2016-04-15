
$(function(){
    /*$(window).on('beforeunload', function() {
        $.post(bu + 'logout',{submit:1},function(data){});
    });*/
    var email_details = $('.email-details');
    email_details.focusout(function(){
        var email_address = $(this).val();
        var check_box = $('.email-checked-details');
        email_details.removeAttr('style');
        check_box.removeAttr('disabled');
        if(!isValidEmailAddress( email_address ) ) {
            $(this).css({
                'border':'1px solid rgb(169, 68, 66)'
            });
            check_box
                .attr('disabled','disabled')
                .prop('checked',false);
        }
        if(!email_address){
            email_details.removeAttr('style');
            check_box
                .removeAttr('disabled')
                .attr('disabled','disabled')
                .prop('checked',false);

        }
    });

    var check_email_details = function(){
        var email_address = email_details.val();
        var check_box = $('.email-checked-details');
        email_details.removeAttr('style');
        check_box.removeAttr('disabled');
        if( email_address && !isValidEmailAddress( email_address ) ) {
            email_details.css({
                'border':'1px solid rgb(169, 68, 66)'
            });
            check_box
                .attr('disabled','disabled')
                .prop('checked',false);
        }

        if(!email_address){
            email_details.removeAttr('style');
            check_box
                .removeAttr('disabled')
                .attr('disabled','disabled')
                .prop('checked',false);

        }
    };

    check_email_details();

    $('.select').change(function(){
        var year = $('.year-dp'),
            month = parseInt($('.month-dp').val()),
            year_val = parseInt(year.val());

        $.post(bu + 'monthWeeks',
            {
                month:month,
                year:year_val
            },
            function(data){
                var week_display = $('.week-display');
                var ele = '<select name="week" class="form-control input-sm">';
                $.each(jQuery.parseJSON(data),function(key,val){
                    ele += '<option value="' + key + '">' + val + '</option>';

                });
                ele += '</select>';
                week_display.html(ele);
            }
        );
    });

    $('.cancel-btn').on('click',function(e){
        $(this).newForm.forceClose();
    });

    $('.submit-btn').on('click',function(e){
        var hasEmpty = false;
        $('.required').each(function(e){
            if(!$(this).val()){
                hasEmpty = true;
                $(this).css({
                    border:'1px solid #a94442'
                });
            }
        });
        if(hasEmpty){
            e.preventDefault();
        }
    });
    $('.number').on('focusin',function(e){
        $(this).numberOnly();
    });
    $('.datepicker').datetimepicker({
        pickTime: false
    });

    var typeValue = $('.action_type');
    var month = $('.month-class');

    var displayDropdown = function(){
        var whatVal = typeValue.val();
        if(whatVal != 0){
            month.css({
                display:'none'
            });
        }else{
            month.css({
                display:'inline'
            });
        }
    };

    displayDropdown();

    typeValue.change(function(e){
        displayDropdown();
    });
    var archive = $('.archive-btn');
    archive.on('click',function(e){
        e.preventDefault();
        var myWindow = window.open(
            $(this).attr('href'),
            'Invoice PDF'
            /*'width=842,height=595;toolbar=no,menubar=no,location=no,titlebar=no'*/
        );

        $(myWindow).load(function(){
            window.location.reload();
        });
    });
    $('.archive-quote').on('click',function(e){
        var myWindow = window.open(
            $('.print-btn').attr('href'),
            'Quote PDF'
            /*'width=842,height=595;toolbar=no,menubar=no,location=no,titlebar=no'*/
        );

        $(myWindow).load(function(){
            window.location.reload();
        });
    });

    $('.back-btn-class').click(function(e){
        e.preventDefault();
        window.history.back();
    });

    $('.dropdown .dropdown-menu').click(function(e) {
        e.stopPropagation();
    });

    $('.msg-btn').click(function(e){
        var notification = $('.notification-class');
        var ele = '<img src="'+ bu + 'images/loading_(2).gif" class="loading-img" style="height: 30px;margin:0 155px;">';
        var loading = $('.loading-img');
        var read_all_msg = $('.read-all-message');
        var filter_val = $('.filter_msg').val();
        notification.html(ele);
        notification.load(bu + 'updateNotification/'+ filter_val +'?is_view=true',
            function(){
                loading.css({
                    'display' : 'none'
                });
                read_all_msg.css({
                    'display' : 'none'
                });
                if(filter_val == 3){
                    read_all_msg.css({
                        'display' : 'inline'
                    });
                }
            }
        );
    });
    setInterval(function(e){
        $.getJSON( bu + "updateNotification?is_json=true", function( data ) {
            var msg_btn = $('.msg-btn');
            msg_btn.html('<i class="fa fa-envelope fa-fw"></i> <i class="fa fa-caret-down"></i>');
            if(data > 0){
                msg_btn.prepend($('<span class="badge">'+ data +'</span>'));
            }
            $('.count-msg').html(data);
        });
    },6000);
});

function isValidEmailAddress(emailAddress) {
    var pattern = /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i;
    return pattern.test(emailAddress);
}
