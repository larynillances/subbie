<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="modal-body">
    <div class="form-group">
        <label class="col-sm-4 control-label">Events</label>
        <div class="col-sm-8">
            <textarea class="form-control input-sm required" name="event"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Num. of days</label>
        <div class="col-sm-8">
            <input type="text" name="duration" class="form-control input-sm required duration" value="1">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Calendar</label>
        <div class="col-lg-8 multi-datepicker"></div>
        <input type="hidden" name="days" class="form-control input-sm required date">
    </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
    <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
</div>
<?php
echo form_close();
?>
<script>
    $(function(e){
        var duration = $('.duration');
        var multiDatepicker = $('.multi-datepicker');
        var num = duration.val();
        var m = function(num){
            multiDatepicker.multiDatesPicker({
                minDate: new Date(),
                dateFormat: 'yy-mm-dd',
                maxPicks: num,
                altField:'.date'
            });
        };

        m(num);
        duration.keyup(function(e){
            multiDatepicker.multiDatesPicker('resetDates');
            num = $(this).val();
            m(num);
        });
        duration.numberOnly({
            wholeNumber: true
        });
    })
</script>