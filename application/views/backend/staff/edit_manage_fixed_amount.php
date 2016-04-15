<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($fixed_amount)>0):
        foreach($fixed_amount as $v):
        ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-5 control-label">Account Two:</label>
                    <div class="col-sm-7">
                        <input type="text" name="account_two" class="form-control input-sm number" value="<?php echo $v->account_two;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 control-label">NZ ACC:</label>
                    <div class="col-sm-7">
                        <input type="text" name="nz_account" class="form-control input-sm number" value="<?php echo $v->nz_account;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 control-label">Hourly Rate:</label>
                    <div class="col-sm-7">
                        <?php echo form_dropdown('hourly_nz_rate_id',$hourly_rate,$v->hourly_nz_rate_id,'class="form-control input-sm"');?>
                    </div>
                </div>
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