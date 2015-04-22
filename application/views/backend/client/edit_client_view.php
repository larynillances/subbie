<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="modal-body">
    <?php
    if(count($client_data)>0):
        foreach($client_data as $v):
        ?>
        <div class="form-group">
            <label class="col-sm-4 control-label">Client Name</label>
            <div class="col-sm-8">
                <input type="text" name="client_name" class="form-control input-sm required" value="<?php echo $v->client_name?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Code</label>
            <div class="col-sm-8">
                <input type="text" name="client_code" class="form-control input-sm required" value="<?php echo $v->client_code?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Contact Name</label>
            <div class="col-sm-8">
                <input type="text" name="contact_name" class="form-control input-sm required" value="<?php echo $v->contact_name?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Phone No.</label>
            <div class="col-sm-8">
                <input type="text" name="phone" class="form-control input-sm" value="<?php echo $v->phone?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Mobile No.</label>
            <div class="col-sm-8">
                <input type="text" name="mobile" class="form-control input-sm" value="<?php echo $v->mobile?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Address</label>
            <div class="col-sm-8">
                <textarea name="address" class="form-control input-sm required"><?php echo $v->address?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Email</label>
            <div class="col-sm-8">
                <input type="text" name="email" class="form-control input-sm required" value="<?php echo $v->email?>">
            </div>
        </div>
        <?php
        endforeach;
    endif;
    ?>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-primary submit-btn" name="submit">Update</button>
    <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
</div>
    <?php
echo form_close();
?>