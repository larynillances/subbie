<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="modal-body">
    <script>
        $(function(e){
            var job = [];
            var job_class = $('.job');
            var client = $('.client');
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
            client
                .selectCountry({
                    cityName: 'client',
                    city: job,
                    style: 'width: 150px;',
                    appendWhere: job_class
                });

            client.change(function(e){
                var new_format = $('.new-format');
                var df_format = $('.df-format');
                var description = $('.description');
                var text_area = $('.text_area_job_name');
                new_format.css({
                    display: 'none'
                });
                df_format.css({
                   display: 'inline'
                });
                description.html('Job Name');
                text_area.attr('name','job_name');
                if($(this).val() == 19){
                    df_format.css({
                        display: 'none'
                    });
                    new_format.css({
                        display: 'inline'
                    });
                    description.html('Description of Work');
                    text_area.attr('name','work_description');
                }
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
        <div class="df-format">
            <div class="col-sm-4">
                <?php echo form_dropdown('job_id',$job,'','class="form-control input-sm job"')?>
            </div>
        </div>
        <div class="new-format" style="display: none;">
            <div class="col-sm-4">
                <input type="text" class="form-control input-sm" id="job_name_" name="job_name">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Template List</label>
        <div class="col-sm-4">
            <?php echo form_dropdown('template_text',$template_text,'','class="form-control input-sm template"')?>
        </div>
        <div class="new-format" style="display: none;">
            <label class="col-sm-2 control-label">Trade</label>
            <div class="col-sm-4">
                <?php echo form_dropdown('trade_id',$trade,'','class="form-control input-sm trade"')?>
            </div>
        </div>
    </div>
    <div class="new-format" style="display: none;">
        <div class="form-group">
            <label class="col-sm-2 control-label">&nbsp;</label>
            <?php
            $ref = 1;
            if(count($invoice_type) > 0){
                foreach($invoice_type as $key=>$val){
                    ?>
                    <label class="control-label checkbox-inline">
                        <input type="radio" name="invoice_type" value="<?php echo $key?>" <?php echo $ref == 1 ? 'checked' : '';?> > <?php echo $val;?>
                    </label>
                <?php
                    $ref++;
                }
            }
            ?>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="order_num">Variation Order #:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control input-sm" id="order_num" name="order_number">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="contract_amount">Contract Amount:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control input-sm number_only" id="contract_amount" name="contract_amount">
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <table class="table table-invoice">
                <thead>
                <tr style="border-top: 2px solid #000000">
                    <th style="width: 15%">Your Ref</th>
                    <th class="description">Job Name</th>
                    <th style="width: 15%">m&sup2;/hrs</th>
                    <th style="width: 15%">Unit Price</th>
                    <!--<th style="width: 10%">Total</th>-->
                </tr>
                </thead>
                <tbody>
                <tr style="border-bottom: 2px solid #000000">
                    <td><input type="text" name="your_ref" class="form-control input-sm required"></td>
                    <td style="height: 300px;">
                        <textarea name="job_name" class="form-control input-sm job_name text_area_job_name" disabled style="height: 100%;width: 529px;"></textarea>
                    </td>
                    <td style="height: 300px;">
                        <textarea name="meter" class="form-control input-sm number this-textarea" style="height: 100%;"></textarea>
                    </td>
                    <td style="height: 300px;">
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
<script>
    $(function(e){
        $('.number_only').numberOnly();
    })
</script>