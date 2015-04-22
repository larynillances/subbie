<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="form-group">
        <label class="col-sm-4 control-label">Amount:</label>
        <div class="col-sm-8">
            <input type="text" name="amount" class="form-control input-sm required number">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">M Tax:</label>
        <div class="col-sm-8">
            <input type="text" name="m_paye" class="form-control input-sm required number">
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10" style="text-align: right">
            <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
            <button type="button" class="btn btn-default cancel-btn">Cancel</button>
        </div>
    </div>
<?php
echo form_close();
?>