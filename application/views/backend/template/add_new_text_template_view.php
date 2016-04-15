<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
        <div class="form-group">
            <div class="col-lg-12">
                <input type="text" class="form-control input-sm" name="title">
            </div>
        </div>
        <div class="form-group">
            <div class="col-lg-12">
                <textarea class="form-control input-sm" style="height: 200px;" name="value"></textarea>
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