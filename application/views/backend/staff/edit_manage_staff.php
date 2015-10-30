<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($staff)):
        foreach($staff as $v):
            $v->date_employed = $v->date_employed != "0000-00-00" ? date('d-m-Y',strtotime($v->date_employed)) : '';
            $bank_account = $v->bank_account ? json_decode($v->bank_account) : array();
        ?>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="fname">First Name:</label>
                    <div class="col-sm-3">
                        <input type="text" name="fname" id="fname" class="form-control input-sm required" value="<?php echo $v->fname;?>">
                    </div>
                    <label class="col-sm-2 control-label" for="mname">Middle:</label>
                    <div class="col-sm-3">
                        <input type="text" name="mname" id="mname" class="form-control input-sm" value="<?php echo $v->mname;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="lname">Last Name:</label>
                    <div class="col-sm-8">
                        <input type="text" name="lname" id="lname" class="form-control input-sm required" value="<?php echo $v->lname;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="company">Project:</label>
                    <div class="col-sm-8">
                        <?php echo form_dropdown('project_id',$project,$v->project_id,'class="form-control input-sm"')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="company">Company:</label>
                    <div class="col-sm-8">
                        <input type="text" name="company" id="company" value="<?php echo $v->company;?>" class="form-control input-sm required">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="position">Position:</label>
                    <div class="col-sm-8">
                        <input type="text" name="position" id="position" class="form-control input-sm required" value="<?php echo $v->position;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="email">Email:</label>
                    <div class="col-sm-5">
                        <input type="text" name="email" id="email" class="form-control input-sm email-details" value="<?php echo $v->email;?>">
                    </div>
                    <div class="col-sm-3">
                        <label>
                            Email Payslip?
                            <input type="checkbox" name="is_email_payslip" value="1" class="email-checked-details" disabled style="margin-top: 10px;" <?php echo $v->is_email_payslip ? 'checked' : '';?>>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="mobile">Mobile No.:</label>
                    <div class="col-sm-5">
                        <input type="text" name="mobile_no" id="mobile" class="form-control input-sm" value="<?php echo $v->mobile_no;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="bank_account">Bank Account:</label>
                    <div class="div-class">
                        <div class="col-sm-4" style="padding-right: 3px!important;text-align: right!important;">
                            <?php echo form_dropdown('bank_account[]',$bank_number,@$bank_account[0],'id="bank_account" class="form-control input-sm" style="width: 90%!important;"')?>
                        </div>
                        <div class="col-sm-2 div" style="margin-left: -13px">
                            <input type="text" name="bank_account[]" id="bank_account" class="form-control input-sm bank_account" value="<?php echo @$bank_account[1];?>" placeholder="0000" style="width: 55%!important;" maxlength="4">
                        </div>
                        <div class="col-sm-3 div" style="margin-left: -40px">
                            <input type="text" name="bank_account[]" id="bank_account" class="form-control input-sm bank_account" value="<?php echo @$bank_account[2];?>" placeholder="0000000" style="width: 50%!important;" maxlength="7">
                        </div>
                        <div class="col-sm-2 div" style="margin-left: -70px">
                            <input type="text" name="bank_account[]" id="bank_account" class="form-control input-sm bank_account" value="<?php echo @$bank_account[3];?>" placeholder="000" maxlength="3" style="width: 50%!important;">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="ird_num">IRD Number:</label>
                    <div class="col-sm-8">
                        <input type="text" name="ird_num" id="ird_num" class="form-control input-sm required" value="<?php echo $v->ird_num;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="tax_code_id">PAYE Code:</label>
                    <div class="col-sm-4">
                        <?php echo form_dropdown('tax_code_id',$tax_code,$v->tax_code_id,'class="form-control input-sm required" id="tax_code_id"');?>
                    </div>
                    <label class="col-sm-2 control-label" for="has_st_loan">
                        ST Loan? <input type="checkbox" name="has_st_loan" id="has_st_loan" value="1" <?php echo $v->has_st_loan ? 'checked' : ''?>>
                    </label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="currency">Currency:</label>
                    <div class="col-sm-8">
                        <?php echo form_dropdown('currency',$currency,$v->currency,'class="form-control input-sm required" id="currency"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="rate">Rate:</label>
                    <div class="col-sm-8">
                        <?php echo form_dropdown('rate',$rate,$v->rate,'class="form-control input-sm required" id="rate"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label tooltip-class" data-placement="top" title="Current Pay Rate Start Date" for="start_use">CPR Start Date:</label>
                    <div class="col-sm-8">
                        <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                            <input type='text' class="input-sm form-control date-class tooltip-class required" data-placement="top" title="Current Pay Rate Start Date" id="start_use" name="start_use" placeholder="dd-mm-yyyy" value="<?php echo $v->start_use;?>">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="date_employed">Date Employed</label>
                    <div class="col-sm-8">
                        <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                            <input type='text' class="input-sm form-control date-class required" name="date_employed" id="date_employed" placeholder="dd-mm-yyyy" value="<?php echo $v->date_employed;?>">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="wage_type">Wage Type:</label>
                    <div class="col-sm-8">
                        <?php echo form_dropdown('wage_type',$wage_type,$v->wage_type,'class="form-control input-sm required" id="wage_type"');?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kiwi_id">Kiwi Saver:</label>
                        <label class="col-sm-1 control-label" for="kiwi_id">Employee:</label>
                        <div class="col-sm-2" style="margin-left: 15px;">
                            <?php echo form_dropdown('kiwi_id',$kiwi,$v->kiwi_id,'class="form-control input-sm kiwi-class" id="kiwi_id" style="width:120%"');?>
                        </div>
                        <label class="col-sm-1 control-label" for="kiwi_id">Employer:</label>
                        <div class="col-sm-2" style="margin-left: 20px;">
                            <?php echo form_dropdown('employeer_kiwi',$kiwi,$v->employeer_kiwi,'class="form-control input-sm kiwi-class" id="kiwi_id" style="width:134%"');?>
                        </div>
                    </div>
                </div>
                <div class="has-kiwi-save" style="display: none">
                    <div class="form-group">
                        <label class="col-sm-3 control-label tooltip-class" data-placement="top" title="KiwiSaver Date Applied" for="kiwi_id">KS Date Applied:</label>
                        <div class="col-sm-6">
                            <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                                <input type='text' class="input-sm form-control date-class required" id="date_employed" name="kiwi_date_start" placeholder="dd-mm-yyyy" value="<?php echo date('d-m-Y',strtotime($v->kiwi_date_start))?>">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="esct_rate_id">ESCT Rate:</label>
                    <div class="col-sm-3">
                        <?php echo form_dropdown('esct_rate_id',$esct_rate,$v->esct_rate_id,'class="form-control input-sm esct_rate_id" id="esct_rate_id"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="balance">Balance Loans:</label>
                    <div class="col-sm-8">
                        <input type="text" name="balance" id="balance" class="form-control balance input-sm" value="<?php echo $v->balance;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="installment">Installment:</label>
                    <div class="col-sm-8">
                        <input type="text" name="installment" id="installment" class="form-control installment input-sm" disabled value="<?php echo $v->installment;?>">
                    </div>
                </div>
            </div>
        </div>
        <fieldset>
            <legend>&nbsp;</legend>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="next_of_kin">Next of Kin:</label>
                        <div class="col-sm-8">
                            <input type="text" name="next_of_kin" id="next_of_kin" class="form-control input-sm" value="<?php echo $v->next_of_kin;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="relationship">Relationship:</label>
                        <div class="col-sm-8">
                            <input type="text" name="relationship" id="relationship" class="form-control input-sm" value="<?php echo $v->relationship;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kin_mobile">Mobile No.:</label>
                        <div class="col-sm-6">
                            <input type="text" name="kin_mobile" id="kin_mobile" class="form-control input-sm" value="<?php echo $v->kin_mobile;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kin_email">Email:</label>
                        <div class="col-sm-6">
                            <input type="text" name="kin_email" id="kin_email" class="form-control input-sm" value="<?php echo $v->kin_email;?>">
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
        <div class="row">
            <div class="col-sm-12">
                <div class="pull-right" style="margin-right: 55px;">
                    <button type="submit" class="btn btn-primary btn-sm submit-btn" name="submit">Submit</button>
                    <button type="button" class="btn btn-default btn-sm cancel-btn" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
        <?php
        endforeach;
    endif;
echo form_close();
?>
<style>
    .div-class .div{
        padding-left: 2px;
        padding-right: 2px;
    }
    .date-class{
        pointer-events: none;
        background: #e7e7e7;
    }
</style>
<script>
    $(function(e){
        var installment = $('.installment'),
            balance = $('.balance'),
            kiwi_class = $('.kiwi-class'),
            esct_rate_id = $('.esct_rate_id'),
            tax_code = $('#tax_code_id'),
            has_st_loan = [];

        has_st_loan = <?php echo $has_st_loan ? $has_st_loan : '[]'?>;

        var st_loan_value = function(data){
            var st_loan = $('#has_st_loan');
            st_loan
                 .attr('disabled','disabled')
                 .removeAttr('checked');
            if(has_st_loan[data] != undefined){
                 st_loan
                 .attr('checked','checked')
                 .css({'pointer-events':'none!important'});
            }
        };

        st_loan_value(tax_code.val());

        tax_code.change(function(e){
            st_loan_value($(this).val());
        });

        var has_kiwi_value = function(data,$_class){

            if(data){
                $('.has-kiwi-save')
                    .css({'display':'inline'});
                $_class.addClass('required');
            }else{
                $_class
                    .removeClass('required')
                    .removeAttr('style')
                    .val('');
                $('.has-kiwi-save')
                    .css({'display':'none'});
            }
        };

        has_kiwi_value(kiwi_class.val(),esct_rate_id);

        kiwi_class.change(function () {
            has_kiwi_value($(this).val(),esct_rate_id);
        });

        esct_rate_id.change(function () {
            has_kiwi_value($(this).val(),kiwi_class);
        });

        $('.tooltip-class').tooltip();
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
        $('.datetimepicker')
            .datetimepicker({
                pickTime: false
            });

        $('.bank_account').on('keyup', function() {
            limitText(this, 10)
        });

        function limitText(field, maxChar){
            var ref = $(field),
                val = ref.val();
            if ( val.length >= maxChar ){
                ref.val(function() {
                    return val.substr(0, maxChar);
                });
            }
        }

        $('.cancel-btn').click(function () {
            location.replace(bu + 'wageManage');
        });
    })
</script>