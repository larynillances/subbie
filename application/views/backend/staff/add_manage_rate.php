<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-4 control-label">Description:</label>
            <div class="col-sm-8">
                <input type="text" name="rate_name" class="form-control input-sm required">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Cost:</label>
            <div class="col-sm-8">
                <input type="text" name="rate_cost" class="form-control input-sm number required">
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