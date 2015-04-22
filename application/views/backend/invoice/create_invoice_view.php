<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="modal-body">
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
                cityName: 'client',
                city: job,
                style: 'width: 150px;',
                appendWhere: job_class
            });
            var job_name = $('.job_name');
            var checkJob = function(){
                if(job_class.val() == 0){
                    job_name.removeAttr('disabled');
                }
            };
            checkJob();
            /*job_class.change(function(e){
                job_name.attr('disabled','disabled');
                if($(this).val() == 0){
                    job_name.removeAttr('disabled');
                }
            });*/
            var textarea = $('.this-textarea');
            $('.template').change(function(e){
                var textVal = job_name.val();
                var thisVal = textVal + "\n" + $(this).val();

                job_name.val(thisVal);

                job_name.scrollTop(
                    job_name[0].scrollHeight - job_name.height()
                );
            });
            textarea.focusin(function(e){
                var total_line = job_name.val();
                var count_line = total_line.split("\n");
                var arr = $(this).val().split("\n");
                var newArr = [];
                var i;

                for(i = 0; i <= count_line.length; i++){
                    var thisVal = "";
                    if(arr[i]){
                        thisVal = arr[i];
                    }
                    newArr.push(thisVal);
                }

                $(this).val(newArr.join("\n"));
            });
        });
    </script>
    <div class="form-group">
        <label class="col-sm-2 control-label">Client Name</label>
        <div class="col-sm-4">
            <?php echo form_dropdown('client_id',$client,'','class="form-control input-sm required client"')?>
        </div>
        <label class="col-sm-2 control-label">Job Name</label>
        <div class="col-sm-4">
            <?php echo form_dropdown('job_id',$job,'','class="form-control input-sm job"')?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Template List</label>
        <div class="col-sm-6">
            <?php echo form_dropdown('template_text',$template_text,'','class="form-control input-sm template"')?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <table class="table table-invoice">
                <thead>
                <tr style="border-top: 2px solid #000000">
                    <th style="width: 15%">Your Ref</th>
                    <th>Job Name</th>
                    <th style="width: 15%">m&sup2;/hrs</th>
                    <th style="width: 15%">Unit Price</th>
                    <!--<th style="width: 10%">Total</th>-->
                </tr>
                </thead>
                <tbody>
                <tr style="border-bottom: 2px solid #000000">
                    <td><input type="text" name="your_ref" class="form-control input-sm required"></td>
                    <td style="height: 300px;">
                        <textarea name="job_name" class="form-control input-sm job_name" disabled style="height: 100%;width: 529px;"></textarea>
                    </td>
                    <td>
                        <textarea name="meter" class="form-control input-sm number this-textarea" style="height: 100%;"></textarea>
                    </td>
                    <td>
                        <textarea name="unit_price" class="form-control input-sm number this-textarea" style="height: 100%;"></textarea>
                    </td>
                    <!--<td>&nbsp;</td>-->
                </tr>
                </tbody>
            </table>
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