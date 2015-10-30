<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-3 control-label">Supplier Name</label>
            <div class="col-sm-8">
                <input type="text" name="supplier_name" class="form-control input-sm required">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Phone No.</label>
            <div class="col-sm-8">
                <input type="text" name="phone" class="form-control input-sm">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Mobile No.</label>
            <div class="col-sm-8">
                <input type="text" name="mobile" class="form-control input-sm">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Fax</label>
            <div class="col-sm-8">
                <input type="text" name="fax" class="form-control input-sm">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Address</label>
            <div class="col-sm-8">
                <textarea name="address" class="form-control input-sm required"></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Email</label>
            <div class="col-sm-8">
                <input type="text" name="email" class="form-control input-sm required">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10" style="text-align: right">
                <button type="submit" class="btn btn-primary btn-sm submit-btn" name="submit">Submit</button>
                <button type="button" class="btn btn-default btn-sm cancel-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
<?php
echo form_close();
?>