<?php
echo form_open('','class="form-horizontal" role="form"');
    ?>
<div class="modal-body">
    <div class="form-group">
        <label class="col-sm-4 control-label">First Name:</label>
        <div class="col-sm-8">
            <input type="text" name="fname" class="form-control input-sm required">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Last Name:</label>
        <div class="col-sm-8">
            <input type="text" name="lname" class="form-control input-sm required">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Company:</label>
        <div class="col-sm-8">
            <input type="text" name="company" class="form-control input-sm required">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">IRD Number:</label>
        <div class="col-sm-8">
            <input type="text" name="ird_num" class="form-control input-sm required">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Tax Code:</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('tax_code_id',$tax_code,'','class="form-control input-sm required"');?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Currency:</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('currency',$currency,109,'class="form-control input-sm required"');?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Rate:</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('rate',$rate,'','class="form-control input-sm required"');?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Wage Type:</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('wage_type',$wage_type,'','class="form-control input-sm required"');?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Tax Number:</label>
        <div class="col-sm-8">
            <input type="text" name="tax_number" class="form-control input-sm required">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Date Employed</label>
        <div class="col-sm-8">
            <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                <input type='text' class="form-control" name="date_employed" value="<?php echo date('d-m-Y')?>">
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
            </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Balance Loans:</label>
        <div class="col-sm-8">
            <input type="text" name="balance" class="form-control balance input-sm number">
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Installment:</label>
        <div class="col-sm-8">
            <input type="text" name="installment" class="form-control installment input-sm number" disabled>
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
    <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
    <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
</div>
<?php
echo form_close();
?>
<script>
    $(function(e){
        var installment = $('.installment');
        $('.balance').keyup(function(e){
            installment.attr('disabled','disabled');
            installment.removeClass('required');
            if($(this).val()){
                installment.removeAttr('disabled');
                installment.addClass('required');
            }
        });
        $('#datetimepicker1').datetimepicker({
            pickTime: false
        });
    })
</script>