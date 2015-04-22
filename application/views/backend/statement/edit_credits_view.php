<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($credits) >0):
        foreach($credits as $v):
            ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label">Amount</label>
                    <div class="col-sm-9">
                        <input type='text' class="form-control input-sm number" name="credits" value="<?php echo $v->credits;?>"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
                <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
            </div>
            <?php
        endforeach;
    endif;
echo form_close();
?>