<?php
echo form_open('','class="form-horizontal" role="form"');
$termination = $this->session->userdata('termination_type');
$disable = $staff_id ? '' : 'disabled';
$display = $staff_id && (@$termination[$staff_id] || @$staff_has_final_pay[$staff_id]) ? 'style="display:inline"' : 'style="display:none"';
$disable_submit = $staff_id && @$termination[$staff_id] ? '' : 'disabled';
$display_notification = count(@$staff_has_final_pay[$staff_id]) > 0 ? 'style="display:inline;"' : 'style="display:none;"'

?>
<div class="row" style="border-bottom: 1px solid #808080;">
    <div class="col-sm-6">
        <div class="form-group">
            <label class="control-label col-sm-3" for="staff_name">Select Staff:</label>
            <div class="col-sm-4">
                <?php echo form_dropdown('staff_id',$staff_list,$staff_id,'id="staff_name" class="form-control input-sm"')?>
            </div>
            <div class="col-sm-5">
                <input type="submit" class="btn btn-sm btn-success" name="search" value="Go">
                <input type="button" class="btn btn-sm btn-info select-termination" name="select" value="Termination Type" <?php echo $disable;?> >
                <a href="<?php echo base_url('printFinalPaySlip/' . $staff_id . '/' . $last_period .'?v=1')?>" <?php echo $display;?> class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-file "></i>Payslip</a>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div <?php echo $display_notification;?> >
            <div style="padding: 5px;" class="alert alert-success" role="alert">This Employee record will be changed from Current to Active, when the Pay Period is next Committed.</div>
        </div>
    </div>
</div><br/>
<?php
    $pay_data = @$last_pay_data[$staff_id];
    if(count($staff)):
        foreach($staff as $v):
            $v->date_employed = $v->date_employed != "0000-00-00" ? date('d-m-Y',strtotime($v->date_employed)) : '';
            $bank_account = $v->bank_account ? json_decode($v->bank_account) : array();
        ?>
        <div class="row details">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="fname">First Name:</label>
                    <div class="col-sm-6">
                        <input type="text" name="fname" id="fname" class="form-control input-sm required" value="<?php echo $v->fname;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="lname">Last Name:</label>
                    <div class="col-sm-6">
                        <input type="text" name="lname" id="lname" class="form-control input-sm required" value="<?php echo $v->lname;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="company">Project:</label>
                    <div class="col-sm-6">
                        <input type="text" name="company" id="company" class="form-control input-sm" value="<?php echo $v->company;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="position">Position:</label>
                    <div class="col-sm-6">
                        <input type="text" name="position" id="position" class="form-control input-sm required" value="<?php echo $v->position;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="email">Email:</label>
                    <div class="col-sm-5">
                        <input type="text" name="email" id="email" class="form-control input-sm" value="<?php echo $v->email;?>">
                    </div>
                    <div class="col-sm-3">
                        <label>
                            Email Payslip?
                            <input type="checkbox" name="is_email_payslip" value="1" style="margin-top: 10px;" <?php echo $v->is_email_payslip ? 'checked' : '';?>>
                        </label>
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
                    <div class="col-sm-6">
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
                    <div class="col-sm-6">
                        <?php echo form_dropdown('currency',$currency,$v->currency,'class="form-control input-sm required" id="currency"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="rate">Rate:</label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('rate',$rate,$v->rate,'class="form-control input-sm required" id="rate"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label tooltip-class" data-placement="top" title="Current Pay Rate Start Date" for="start_use">CPR Start Date:</label>
                    <div class="col-sm-6">
                        <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                            <input type='text' class="input-sm form-control date-class tooltip-class" data-placement="top" title="Current Pay Rate Start Date" id="start_use" name="start_use" placeholder="dd-mm-yyyy" value="<?php echo date('d-m-Y',strtotime($v->start_use));?>">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="date_employed">Date Employed</label>
                    <div class="col-sm-6">
                        <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                            <input type='text' class="input-sm form-control date-class" name="date_employed" id="date_employed" placeholder="dd-mm-yyyy" value="<?php echo $v->date_employed;?>">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="wage_type">Wage Type:</label>
                    <div class="col-sm-6">
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
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="esct_rate_id">ESCT Rate:</label>
                    <div class="col-sm-3">
                        <?php echo form_dropdown('esct_rate_id',$esct_rate,$v->esct_rate_id,'class="form-control input-sm esct_rate_id" id="esct_rate_id"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="balance">Balance Loans:</label>
                    <div class="col-sm-6">
                        <input type="text" name="balance" id="balance" class="form-control balance input-sm" value="<?php echo $v->balance;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="installment">Installment:</label>
                    <div class="col-sm-6">
                        <input type="text" name="installment" id="installment" class="form-control installment input-sm" disabled value="<?php echo $v->installment;?>">
                    </div>
                </div>
            </div>
        </div>
        <?php
        endforeach;
    else:
        ?>
        <div class="row details">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="fname">First Name:</label>
                    <div class="col-sm-6">
                        <input type="text" name="fname" id="fname" class="form-control input-sm required">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="lname">Last Name:</label>
                    <div class="col-sm-6">
                        <input type="text" name="lname" id="lname" class="form-control input-sm required">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="company">Project:</label>
                    <div class="col-sm-6">
                        <input type="text" name="company" id="company" class="form-control input-sm">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="position">Position:</label>
                    <div class="col-sm-6">
                        <input type="text" name="position" id="position" class="form-control input-sm required">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="email">Email:</label>
                    <div class="col-sm-5">
                        <input type="text" name="email" id="email" class="form-control input-sm">
                    </div>
                    <div class="col-sm-3">
                        <label>
                            Email Payslip?
                            <input type="checkbox" name="is_email_payslip" value="1" style="margin-top: 10px;">
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="bank_account">Bank Account:</label>
                    <div class="div-class">
                        <div class="col-sm-4" style="padding-right: 3px!important;text-align: right!important;">
                            <?php echo form_dropdown('bank_account[]',$bank_number,'','id="bank_account" class="form-control input-sm" style="width: 90%!important;"')?>
                        </div>
                        <div class="col-sm-2 div" style="margin-left: -13px">
                            <input type="text" name="bank_account[]" id="bank_account" class="form-control input-sm bank_account" placeholder="0000" style="width: 55%!important;" maxlength="4">
                        </div>
                        <div class="col-sm-3 div" style="margin-left: -40px">
                            <input type="text" name="bank_account[]" id="bank_account" class="form-control input-sm bank_account" placeholder="0000000" style="width: 50%!important;" maxlength="7">
                        </div>
                        <div class="col-sm-2 div" style="margin-left: -70px">
                            <input type="text" name="bank_account[]" id="bank_account" class="form-control input-sm bank_account" placeholder="000" maxlength="3" style="width: 50%!important;">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="ird_num">IRD Number:</label>
                    <div class="col-sm-6">
                        <input type="text" name="ird_num" id="ird_num" class="form-control input-sm required">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="tax_code_id">PAYE Code:</label>
                    <div class="col-sm-4">
                        <?php echo form_dropdown('tax_code_id',$tax_code,'','class="form-control input-sm required" id="tax_code_id"');?>
                    </div>
                    <label class="col-sm-2 control-label" for="has_st_loan">
                        ST Loan? <input type="checkbox" name="has_st_loan" id="has_st_loan" value="1">
                    </label>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="currency">Currency:</label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('currency',$currency,72,'class="form-control input-sm required" id="currency"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="rate">Rate:</label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('rate',$rate,'','class="form-control input-sm required" id="rate"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label tooltip-class" data-placement="top" title="Current Pay Rate Start Date" for="start_use">CPR Start Date:</label>
                    <div class="col-sm-6">
                        <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                            <input type='text' class="input-sm form-control date-class tooltip-class" data-placement="top" title="Current Pay Rate Start Date" id="start_use" name="start_use" placeholder="dd-mm-yyyy">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="date_employed">Date Employed</label>
                    <div class="col-sm-6">
                        <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                            <input type='text' class="input-sm form-control date-class" name="date_employed" id="date_employed" placeholder="dd-mm-yyyy">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="wage_type">Wage Type:</label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('wage_type',$wage_type,'','class="form-control input-sm required" id="wage_type"');?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="kiwi_id">Kiwi Saver:</label>
                        <label class="col-sm-1 control-label" for="kiwi_id">Employee:</label>
                        <div class="col-sm-2" style="margin-left: 15px;">
                            <?php echo form_dropdown('kiwi_id',$kiwi,'','class="form-control input-sm kiwi-class" id="kiwi_id" style="width:120%"');?>
                        </div>
                        <label class="col-sm-1 control-label" for="kiwi_id">Employer:</label>
                        <div class="col-sm-2" style="margin-left: 20px;">
                            <?php echo form_dropdown('employeer_kiwi',$kiwi,'','class="form-control input-sm kiwi-class" id="kiwi_id" style="width:134%"');?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="esct_rate_id">ESCT Rate:</label>
                    <div class="col-sm-3">
                        <?php echo form_dropdown('esct_rate_id',$esct_rate,'','class="form-control input-sm esct_rate_id" id="esct_rate_id"');?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="balance">Balance Loans:</label>
                    <div class="col-sm-6">
                        <input type="text" name="balance" id="balance" class="form-control balance input-sm">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="installment">Installment:</label>
                    <div class="col-sm-6">
                        <input type="text" name="installment" id="installment" class="form-control installment input-sm" disabled>
                    </div>
                </div>
            </div>
        </div>
    <?php
    endif;
    if(count(@$termination[$staff_id]) > 0 || count(@$staff_has_final_pay[$staff_id])){
        $final_pay = $pay_data['distribution'] + ($pay_data['annual_leave_pay'] - $pay_data['annual_tax']);
        ?>
        <fieldset>
            <legend>Last Pay Details</legend>
            <div class="row details">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="termination-type">Termination Type:</label>
                        <div class="col-sm-6">
                            <?php
                            $str = '';
                            $ref = count(@$termination[$staff_id]) > 0 ? count(@$termination[$staff_id]) : count($staff_has_final_pay[$staff_id]);
                            $_termination_type = count(@$termination[$staff_id]) > 0 ? @$termination[$staff_id] : $staff_has_final_pay[$staff_id];
                            foreach($_termination_type as $val){
                                $str .= $termination_pay[$val];
                                $str .= $ref != 1 ? ', ' : '';
                                $ref--;
                            }
                            ?>
                            <input type="text" class="input-sm form-control" id="termination-type" value="<?php echo $str;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="last-pay">Last Day Worked:</label>
                        <div class="col-sm-6">
                            <input type="hidden" name="last_week_pay" id="last-week" value="<?php echo $pay_data['last_week']?>">
                            <input type="hidden" name="last_date_pay" id="last-pay" value="<?php echo $pay_data['last_date_pay']?>">
                            <input type="text" class="input-sm form-control" value="<?php echo date('d/m/Y',strtotime('+6 days '.$pay_data['last_date_pay']))?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="last-pay-gross">Ordinary Gross:</label>
                        <div class="col-sm-6">
                            <input type="text" class="input-sm form-control" id="last-pay-gross" value="<?php echo number_format($pay_data['gross'],2)?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="last-pay-gross">Ordinary Nett Pay:</label>
                        <div class="col-sm-6">
                            <input type="text" class="input-sm form-control" id="last-pay-gross" value="<?php echo number_format($pay_data['distribution'],2)?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="last-pay-gross">Ordinary PAYE:</label>
                        <div class="col-sm-6">
                            <input type="text" class="input-sm form-control" id="last-pay-gross" value="<?php echo number_format($pay_data['tax'],2)?>">
                        </div>
                    </div><br/>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="annual-pay">Annual Leave Days owing:</label>
                        <div class="col-sm-6">
                            <input type="text" class="input-sm form-control" id="annual-pay" value="<?php echo $pay_data['total_holiday_leave']?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="public-holidays">Public Holidays owing:</label>
                        <div class="col-sm-6">
                            <input type="text" class="input-sm form-control" id="public-holidays" value="<?php echo '0'?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="gross-income">Gross Income (This Year):</label>
                        <div class="col-sm-6">
                            <input type="text" class="input-sm form-control" id="gross-income" value="<?php echo number_format($pay_data['total_gross'],2)?>">
                        </div>
                    </div>
                    <div class="form-group" style="white-space: nowrap!important;">
                        <label class="control-label col-sm-4" for="annual-pay">Annual Leave Pay:</label>
                        <div class="col-sm-6">
                            <input type="text" class="input-sm form-control" id="annual-pay" value="<?php echo number_format($pay_data['annual_leave_pay'],2)?>">
                        </div>
                        <label class="control-label">Method: <?php echo '('.$pay_data['calculation_type'].')';?></label>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="annual-pay">Annual Leave PAYE:</label>
                        <div class="col-sm-6">
                            <input type="text" class="input-sm form-control" id="annual-pay" value="<?php echo number_format($pay_data['annual_tax'],2)?>">
                        </div>
                    </div><br/>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="final-pay">Final Pay:</label>
                        <div class="col-sm-6">
                            <input type="text" class="input-sm form-control" id="final-pay" value="<?php echo number_format($final_pay,2)?>">
                        </div>
                    </div>
                </div>
            </div>
        </fieldset>
    <?php
    }
?>
<div class="row">
    <div class="col-sm-12">
        <div class="pull-right" style="margin-right: 60px;">
            <button type="submit" class="btn btn-primary btn-sm final-pay-submit-btn" name="submit" id="<?php echo $staff_id;?>" <?php echo $disable_submit?>>Submit</button>
        </div>
    </div>
</div><br/>
<?php
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
            staff_id = <?php echo $staff_id ? $staff_id : 0;?>,
            has_termination = <?php echo count(@$termination[$staff_id]) > 0 || count(@$staff_has_final_pay[$staff_id]) > 0 ? 1 : 0;?>,
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
                $_class.addClass('required');
            }else{
                $_class
                    .removeClass('required')
                    .removeAttr('style')
                    .val('');
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
        var disableInput = function(){
            var details = $('.details');
            details.find('input,select').attr('disabled','disabled');
        };
        disableInput();
        $('.select-termination').click(function(e){
            e.preventDefault();
            $(this).modifiedModal({
                url: bu + 'selectTerminationPay/' + staff_id,
                title: 'Select Termination Type',
                type: 'small'
            });
        });
        var popOutView = function(){
            if(staff_id && !has_termination){
                $('.select-termination').trigger('click');

            }
        };
        popOutView();
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

        $('.final-pay-submit-btn').click(function(e){
            e.preventDefault();
            $.post(bu + 'employeeFinalPay',
                {
                    submit: 1,
                    date_last_pay: $('#last-pay').val(),
                    last_week_pay: $('#last-week').val()
                }, function(data){
                    location.reload();
                }
            );
        });

    })
</script>