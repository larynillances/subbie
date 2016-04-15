<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($deductions)>0):
        foreach($deductions as $v):
        ?>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-4 control-label">Flight Debt:</label>
                <div class="col-sm-8">
                    <input type="text" name="flight_debt" class="form-control input-sm number" value="<?php echo $v->flight_debt;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Flight Deduct:</label>
                <div class="col-sm-8">
                    <input type="text" name="flight_deduct" class="form-control input-sm number" value="<?php echo $v->flight_deduct;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Visa Debt:</label>
                <div class="col-sm-8">
                    <input type="text" name="visa_debt" class="form-control input-sm number" value="<?php echo $v->visa_debt;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Visa Deduct:</label>
                <div class="col-sm-8">
                    <input type="text" name="visa_deduct" class="form-control input-sm number" value="<?php echo $v->visa_deduct;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Accommodation:</label>
                <div class="col-sm-8">
                    <input type="text" name="accommodation" class="form-control input-sm number" value="<?php echo $v->accommodation;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Transport:</label>
                <div class="col-sm-8">
                    <input type="text" name="transport" class="form-control input-sm number" value="<?php echo $v->transport;?>">
                </div>
            </div>
            <!--<div class="form-group">
                <div class="col-sm-offset-2 col-sm-10" style="text-align: right">
                    <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
                    <button type="button" class="btn btn-default cancel-btn">Cancel</button>
                </div>
            </div>-->
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-primary submit-btn" name="submit">Submit</button>
            <button type="button" class="btn btn-sm btn-default cancel-btn" data-dismiss="modal">Cancel</button>
        </div>
        <?php
        endforeach;
    endif;
echo form_close();
?>