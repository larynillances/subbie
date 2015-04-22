<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="form-group">
        <label class="col-sm-4 control-label">Date:</label>
        <div class="col-sm-8 text-left">
            <span class="date"><?php echo $date;?></span>
            <input type="hidden" name="date" class="date-picker form-control input-sm required" value="<?php echo $date;?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Name:</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('staff_id',$employee,'','class="form-control input-sm required"');?>
        </div>
    </div>
    <!--<div class="form-group">
        <label class="col-sm-4 control-label">Visa:</label>
        <div class="col-sm-8">
            <input type="text" name="visa" class="form-control input-sm number">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Flight:</label>
        <div class="col-sm-8">
            <input type="text" name="flight" class="form-control input-sm number">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Accommo:</label>
        <div class="col-sm-8">
            <input type="text" name="accommodation" class="form-control input-sm number">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Transport:</label>
        <div class="col-sm-8">
            <input type="text" name="transport" class="form-control input-sm number">
        </div>
    </div>-->
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10" style="text-align: right">
            <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
            <button type="button" class="btn btn-default cancel-btn">Cancel</button>
        </div>
    </div>
<?php
echo form_close();
?>
<script>
    $(function(e){
        $('.number').numberOnly();
        $('.submit-btn').click(function(e){
            var hasEmpty = false;
            $('.required').each(function(e){
                if(!$(this).val()){
                    hasEmpty = true;
                    $(this).css({
                        border:'1px solid #ff0000'
                    });
                }
            });
            if(hasEmpty){
                e.preventDefault();
            }
        });
        /*$('.date-picker').datepicker({
            dateFormat: "dd MM yy",
            showOn: "button",
            buttonImage: bu + "images/calendar-add.png",
            buttonImageOnly: true,
            onSelect: function(){
                var date = $(this).val();
                 $(this).parent().find('.date').html(String(date));
            }
        });*/
        $('.cancel-btn').click(function(e){
            $(this).newForm.forceClose();
        });

        var wp = $('.date-picker');
        if(wp.length != 0){
            var startDate;
            var endDate;

            var selectCurrentWeek = function() {
                window.setTimeout(function () {
                    wp.find('.ui-datepicker-current-day a').addClass('ui-state-active')
                }, 1);
            };

            function nextSession(date) {
                //if(date.getDate())
                var is_sun = new Date(date).getDay();
                var selectedDate = new Date(date);
                var thisDate;
                if(is_sun == 0){
                    thisDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate() - 1);
                }else{
                    thisDate = new Date(date);
                }
                var ret = new Date(thisDate||new Date());
                ret.setDate(ret.getDate() + (0 - 1 - ret.getDay() + 7) % 7 + 1);
                return ret;
            }

            wp.datepicker( {
                dateFormat: "dd MM yy",
                showOn: "button",
                buttonImage: bu + "images/calendar-add.png",
                buttonImageOnly: true,
                firstDay: 1,
                onSelect: function(dateText, inst) {
                    var date = $(this).datepicker('getDate');
                    startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay());
                    endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + 7);
                    var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
                    $('#endDatePartTwo').text($.datepicker.formatDate( 'dd-mm-yy', endDate, inst.settings ));

                    var st = $.datepicker.formatDate( dateFormat, startDate, inst.settings);
                    var en = $.datepicker.formatDate( dateFormat, endDate, inst.settings);

                    var thisDate = $.datepicker.formatDate('dd MM yy', nextSession($(this).val()) , inst.settings);
                    $(this).val(thisDate);
                    $(this).parent().find('.date').html(String(thisDate));
                    selectCurrentWeek();
                },
                beforeShowDay: function(date) {
                    var cssClass = '';
                    if(date >= startDate && date <= endDate)
                        cssClass = 'ui-datepicker-current-day';
                    return [true, cssClass];
                },
                onChangeMonthYear: function(year, month, inst) {
                    selectCurrentWeek();
                }
            });

            $('.ui-datepicker-calendar tr')
                .live('mousemove', function() { $(this).find('td a').addClass('ui-state-hover'); })
                .live('mouseleave', function() { $(this).find('td a').removeClass('ui-state-hover'); });
        }
    });
</script>