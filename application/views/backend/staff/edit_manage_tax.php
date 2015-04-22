<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($tax)>0):
        foreach($tax as $v):
            ?>
            <div class="form-group">
                <label class="col-sm-4 control-label">Amount:</label>
                <div class="col-sm-8">
                    <input type="text" name="earnings" class="form-control input-sm number" value="<?php echo $v->earnings;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">M Paye:</label>
                <div class="col-sm-8">
                    <input type="text" name="m_paye" class="form-control input-sm number" value="<?php echo $v->m_paye;?>">
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10" style="text-align: right">
                    <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
                    <button type="button" class="btn btn-default cancel-btn">Cancel</button>
                </div>
            </div>
        <?php
        endforeach;
    endif;
echo form_close();
?>