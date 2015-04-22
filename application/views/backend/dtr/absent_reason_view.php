<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-4 control-label">Date</label>
            <div class="col-sm-8" style="text-align: left;margin-top:5px;">
                <strong><?php echo date('j F Y',strtotime($this->uri->segment(3)));?></strong>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Type</label>
            <div class="col-sm-8">
                <?php echo form_dropdown('absent_type_id',$absent_type,'','class="form-control required input-sm"')?>
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