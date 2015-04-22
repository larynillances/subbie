<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
        <div class="form-group">
            <label class="control-label col-sm-4">Client Ref:</label>
            <div class="col-sm-8">
                <input type="text" name="client_ref" class="form-control input-sm required">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Job Ref:</label>
            <div class="col-sm-8">
                <?php echo form_dropdown('invoice_id',$job_list,'','class="form-control input-sm"');?>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Meter/Hrs:</label>
            <div class="col-sm-8">
                <input type="text" name="area" class="form-control input-sm number">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Unit Price:</label>
            <div class="col-sm-8">
                <input type="text" name="price" class="form-control input-sm number required">
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