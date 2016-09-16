<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
    <?php
    if(count($quotation) > 0):
        foreach($quotation as $v):
        ?>
        <div class="form-group">
            <label class="col-sm-4 control-label">Client Name</label>
            <div class="col-sm-8">
                <?php echo form_dropdown('client_id',$client,$v->client_id,'class="form-control required input-sm client"')?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Job Name</label>
            <div class="col-sm-8">
                <?php /*echo form_dropdown('job_id',$job,$v->job_id,'class="form-control required input-sm job"')*/?>
                <div class="job">
                    <span class="defaultCity"><?php echo $v->job_id?></span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Price excl. GST</label>
            <div class="col-sm-4">
                <input type="text" name="price" class="form-control input-sm required number price" value="<?php echo $v->price;?>">
            </div>
            <label class="col-sm-1 control-label">GST</label>
            <div class="col-sm-3" style="margin-top: 5px;">
                <input type="text" name="gst" class="form-control input-sm required gst" disabled>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Tags<br/>(if more than 1 put in 1.,2.3.)</label>
            <div class="col-sm-8">
                <textarea name="tags" class="form-control input-sm" placeholder="Tags" style="height: 100px;"><?php echo $v->tags;?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Closure Date</label>
            <div class="col-sm-8">
                <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY hh:mm A">
                    <input type='text' class="form-control" name="closure_date" value="<?php echo $v->closure_date;?>"/>
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Announcement Date</label>
            <div class="col-sm-8">
                <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY hh:mm A">
                    <input type='text' class="form-control" name="announce_date" value="<?php echo $v->announce_date;?>"/>
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
            </span>
                </div>
            </div>
        </div>
        <?php
        endforeach;
    endif;
    ?>
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
        var job = [];
        var job_class = $('.job');
        <?php
        if(count($job)>0){
            $ref = 1;
            foreach($job as $client_id=>$job_val){
                echo 'job[' . $client_id . '] = [];' . "\r\n";
                if(count($job_val)>0){
                    foreach($job_val as $job_id=>$name){
                        echo 'job[' . $client_id . '][' . $ref . '] = [];' . "\r\n";
                        echo 'job[' . $client_id . '][' . $ref . '][' . $job_id . '] = "' . $name . '";' . "\r\n";
                        $ref++;
                    }
                }
            }
        }
        ?>
        $('.client').selectCountry({
            cityName: 'job_id',
            city: job,
            style: 'width: 250px;',
            appendWhere: job_class
        });
        $('.price').focusout(function(e){
            var gst = $('.gst');
            var total_gst = $(this).val() * 0.15;
            gst.val(total_gst.toFixed(2));
        });
        var checkPrice = function(){
            var price = $('.price').val();
            $('.gst').val(price * 0.15);
        };
        checkPrice();
        $('.datetimepicker').datetimepicker();
    });
</script>