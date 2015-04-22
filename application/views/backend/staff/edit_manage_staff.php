<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($staff)):
        foreach($staff as $v):
            $v->date_employed = $v->date_employed != "0000-00-00" ? date('d-m-Y',strtotime($v->date_employed)) : '';
        ?>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-4 control-label">First Name:</label>
                <div class="col-sm-8">
                    <input type="text" name="fname" class="form-control input-sm required" value="<?php echo $v->fname;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Last Name:</label>
                <div class="col-sm-8">
                    <input type="text" name="lname" class="form-control input-sm required" value="<?php echo $v->lname;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Company:</label>
                <div class="col-sm-8">
                    <input type="text" name="company" class="form-control input-sm required" value="<?php echo $v->company;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">IRD Number:</label>
                <div class="col-sm-8">
                    <input type="text" name="ird_num" class="form-control input-sm required" value="<?php echo $v->ird_num;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Tax Code:</label>
                <div class="col-sm-8">
                    <?php echo form_dropdown('tax_code_id',$tax_code,$v->tax_code_id,'class="form-control input-sm required"');?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Currency:</label>
                <div class="col-sm-8">
                    <?php echo form_dropdown('currency',$currency,$v->currency,'class="form-control input-sm required"');?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Rate:</label>
                <div class="col-sm-8">
                    <?php echo form_dropdown('rate',$rate,$v->rate,'class="form-control input-sm required"');?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Use Date:</label>
                <div class="col-sm-8">
                    <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                        <input type='text' class="form-control" name="start_use" value="<?php echo $v->start_use;?>">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Wage Type:</label>
                <div class="col-sm-8">
                    <?php echo form_dropdown('wage_type',$wage_type,$v->wage_type,'class="form-control input-sm required"');?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Tax Number:</label>
                <div class="col-sm-8">
                    <input type="text" name="tax_number" class="form-control input-sm required" value="<?php echo $v->tax_number;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Date Employed</label>
                <div class="col-sm-8">
                    <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                        <input type='text' class="form-control" name="date_employed" value="<?php echo $v->date_employed;?>">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Balance Loans:</label>
                <div class="col-sm-8">
                    <input type="text" name="balance" class="form-control balance input-sm" value="<?php echo $v->balance;?>">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Installment:</label>
                <div class="col-sm-8">
                    <input type="text" name="installment" class="form-control installment input-sm" disabled value="<?php echo $v->installment;?>">
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
<script>
    $(function(e){
        var installment = $('.installment');
        var balance = $('.balance');
        var checkInput = function(){
            if(balance.val()){
                installment.removeAttr('disabled');
            }
            //console.log(balance.val());
        };
        checkInput();
        balance.keyup(function(e){
            installment.removeAttr('disabled');
            installment.addClass('required');
            if(!$(this).val()){
                installment.attr('disabled','disabled');
                installment.removeClass('required');
            }
            if(installment.val() && !$(this).val()){
                $(this).addClass('required');
            }
        });
        $('.datetimepicker').datetimepicker({
            pickTime: false
        });
    })
</script>