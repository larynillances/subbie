<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-4 control-label">Client Name</label>
            <div class="col-sm-8">
                <?php echo form_dropdown('client_id',$client,'','class="form-control required input-sm client"')?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Job Name</label>
            <div class="col-sm-8">
                <?php /*echo form_dropdown('job_id',$job,'','class="form-control input-sm job"')*/?>
                <div class="job"></div>
                <input type="text" name="job_name" class="form-control input-sm" placeholder="Job Name" style="margin-top: 5px;">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Price excl. GST</label>
            <div class="col-sm-4">
                <input type="text" name="price" class="form-control input-sm required number price" style="width: 150px;">
            </div>
            <label class="col-sm-1 control-label">GST</label>
            <div class="col-sm-3" style="margin-top: 5px;">
                <input type="text" name="gst" class="form-control input-sm gst" disabled style="width: 100px;">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Tags<br/>(if more than 1 put in 1,2,3)</label>
            <div class="col-sm-8">
                <textarea name="tags" class="form-control input-sm" placeholder="Tags" style="height: 100px;"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Closure Date</label>
            <div class="col-sm-8">
                <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY hh:mm A">
                    <input type='text' class="form-control required" name="closure_date"/>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Announcement Date</label>
            <div class="col-sm-8">
                <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY hh:mm A">
                    <input type='text' class="form-control required" name="announce_date"/>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
            </div>
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
            style: 'width: 260px;',
            appendWhere: job_class
        });

        job_class.change(function(e){
            var job_name = $('input[name=job_name]');
            job_name.css({'display':'inline'});
            if($(this).val() != 0){
                job_name.css({'display':'none'});
            }
        });

        $('.price').focusout(function(e){
            var gst = $('.gst');
            var total_gst = $(this).val() * 0.15;
            gst.val(total_gst.toFixed(2));
        });
        $('.datetimepicker').datetimepicker();
    });
</script>