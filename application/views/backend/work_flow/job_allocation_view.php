<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-4 control-label">Job</label>
            <div class="col-sm-8">
                <?php echo form_dropdown('job_id',$job_list,'','class="form-control required"')?>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-4 control-label">Color</label>
            <?php
            $ref = 0;
            if(count($color) > 0):
                foreach($color as $v):
                    $selected = $ref == 0 ? 'checked' : '';
                    ?>
                    <div class="col-lg-1">
                        <label class="radio-inline">
                            <input type="radio" name="color_pick_id" value="<?php echo $v->id;?>" <?php echo $selected;?>>
                            <?php echo '<span style="padding:5px;color:white;background:'. $v->color.'">&nbsp;</span>';?>
                        </label>
                    </div>
                    <?php
                    $ref++;
                endforeach;
            endif;
            ?>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Duration</label>
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
        $('.cancel-btn').click(function(e){
            $(this).newForm.forceClose();
        });
        $('.submit-btn').click(function(e){
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
    })
</script>