<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
        <div class="form-group">
            <label class="control-label col-sm-4">Client Ref:</label>
            <div class="col-sm-8">
                <input type="text" name="client_ref" class="form-control input-sm required" value="<?php echo @$credit_note->client_ref?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Inv Ref:</label>
            <div class="col-sm-8">
                <?php echo form_dropdown('inv_ref',$inv_ref,@$credit_note->inv_ref,'class="form-control input-sm inv-ref"');?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Job Ref:</label>
            <div class="col-sm-8">
                <div class="job_option_area">
                    <div class="default_job_id"><?php echo @$credit_note->invoice_id?></div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Meter/Hrs:</label>
            <div class="col-sm-8">
                <input type="text" name="area" class="form-control input-sm number" value="<?php echo @$credit_note->area;?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Unit Price:</label>
            <div class="col-sm-8">
                <input type="text" name="price" class="form-control input-sm number required" value="<?php echo @$credit_note->price;?>">
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
    $(function(){
        var $merchantsOption = <?php echo $job_list_json;?>;
        $('.inv-ref').selectCountry({
            cityName: 'invoice_id',
            cityId: 'job_list',
            city: $merchantsOption,
            appendWhere: $('.job_option_area'),
            defaultCity: '.default_job_id'
        });
    })
</script>