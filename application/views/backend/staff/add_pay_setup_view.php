<?php
echo form_open('','class="form-horizontal" role="form"');
    ?>
<div class="modal-body">
    <div class="form-group">
        <label class="col-sm-4 control-label" for="accountant_name">Accountant's Name:</label>
        <div class="col-sm-7">
            <input type="text" name="accountant_name" id="accountant_name" class="form-control input-sm" value="<?php echo @$pay_setup->accountant_name?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label" for="accountant_email">Accountant's Email:</label>
        <div class="col-sm-7">
            <input type="email" name="accountant_email" id="accountant_email" class="form-control input-sm" value="<?php echo @$pay_setup->accountant_email?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label" for="director_name">Director's Name:</label>
        <div class="col-sm-7">
            <input type="text" name="director_name" id="director_name" class="form-control input-sm" value="<?php echo @$pay_setup->director_name?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label" for="director_email">Director's Email:</label>
        <div class="col-sm-7">
            <input type="email" name="director_email" id="director_email" class="form-control input-sm" value="<?php echo @$pay_setup->director_email?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label" for="enderly_name">Enderly's Project Admin:</label>
        <div class="col-sm-7">
            <input type="text" name="enderly_name" id="enderly_name" class="form-control input-sm" value="<?php echo @$pay_setup->enderly_name?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label" for="enderly_email">Enderly's Project Email:</label>
        <div class="col-sm-7">
            <input type="email" name="enderly_email" id="enderly_email" class="form-control input-sm" value="<?php echo @$pay_setup->enderly_email?>">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label" for="ird_number">Employer's IRD Number:</label>
        <div class="col-sm-7">
            <input type="text" name="ird_number" id="ird_number" class="form-control input-sm" value="<?php echo @$pay_setup->ird_number?>">
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