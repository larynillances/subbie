$(function(){
    $('.cancel-btn').live('click',function(e){
        $(this).newForm.forceClose();
    });

    $('.submit-btn').live('click',function(e){
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
    $('.number').live('focusin',function(e){
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

    $('.msg-link').live('click',function(e){
        var id = this.id;
        $.post(bu + 'updateNotification/' + id,function(e){
            /*location.reload();*/
        });
    });
    $('.msg-btn').click(function(e){
        var notification = $('.notification-class');
        var ele = '<img src="'+ bu + 'images/loading_(2).gif" class="loading-img" style="height: 30px;margin:0 155px;">';
        var loading = $('.loading-img');
        notification.html(ele);
        notification.load(bu + 'updateNotification?is_view=true',
            function(){
                loading.css({
                    'display' : 'none'
                })
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
