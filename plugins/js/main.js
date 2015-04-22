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
});
