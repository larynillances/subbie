<div class="container-fluid">
    <?php
    echo form_open('','class="form-horizontal" role="form"');
    ?>
    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="fname">First Name:</label>
                <div class="col-sm-3">
                    <input type="text" name="fname" id="fname" class="form-control input-sm required">
                </div>
                <label class="col-sm-2 control-label" for="mname">Middle:</label>
                <div class="col-sm-3">
                    <input type="text" name="mname" id="mname" class="form-control input-sm">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="lname">Last Name:</label>
                <div class="col-sm-8">
                    <input type="text" name="lname" id="lname" class="form-control input-sm required">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="company">Project:</label>
                <div class="col-sm-8">
                    <?php echo form_dropdown('project_id',$project,'','class="form-control input-sm"')?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="company">Company:</label>
                <div class="col-sm-8">
                    <input type="text" name="company" id="company" class="form-control input-sm required">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="position">Position:</label>
                <div class="col-sm-8">
                    <input type="text" name="position" id="position" class="form-control input-sm required">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="email">Email:</label>
                <div class="col-sm-5">
                    <input type="text" name="email" id="email" class="form-control input-sm email-details">
                </div>
                <div class="col-sm-3">
                    <label>
                        Email Payslip?
                        <input type="checkbox" name="is_email_payslip" value="1" class="email-checked-details" disabled style="margin-top: 10px;">
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="mobile">Mobile No.:</label>
                <div class="col-sm-5">
                    <input type="text" name="mobile_no" id="mobile" class="form-control input-sm">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="bank_account">Bank Account:</label>
                <div class="div-class">
                    <div class="col-sm-3" style="padding-right: 3px!important;text-align: right!important;">
                        <?php echo form_dropdown('bank_account[]',$bank_number,'','id="bank_account" class="form-control input-sm" style="width: 90%!important;"')?>
                    </div>
                    <div class="col-sm-3 div" style="margin-left: -13px">
                        <input type="text" name="bank_account[]" id="bank_account" class="form-control input-sm bank_account" placeholder="0000" style="width: 55%!important;" maxlength="4">
                    </div>
                    <div class="col-sm-3 div" style="margin-left: -70px">
                        <input type="text" name="bank_account[]" id="bank_account" class="form-control input-sm bank_account" placeholder="0000000" style="width: 50%!important;" maxlength="7">
                    </div>
                    <div class="col-sm-3 div" style="margin-left: -80px">
                        <input type="text" name="bank_account[]" id="bank_account" class="form-control input-sm bank_account" placeholder="000" maxlength="3" style="width: 50%!important;">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="ird_num">IRD Number:</label>
                <div class="col-sm-8">
                    <input type="text" name="ird_num" id="ird_num" class="form-control input-sm required">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="tax_code_id">PAYE Code:</label>
                <div class="col-sm-4">
                    <?php echo form_dropdown('tax_code_id',$tax_code,'','class="form-control input-sm required" id="tax_code_id"');?>
                </div>
                <label class="col-sm-3 control-label" for="has_st_loan">
                    ST Loan? <input type="checkbox" name="has_st_loan" id="has_st_loan" value="1" disabled>
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
                        <input type='text' class="input-sm form-control date-class tooltip-class required" data-toggle="tooltip" data-placement="top" title="Current Pay Rate Start Date" id="start_use" name="start_use" placeholder="dd-mm-yyyy" value="<?php echo date('d-m-Y')?>">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="date_employed">Date Employed:</label>
                <div class="col-sm-6">
                    <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                        <input type='text' class="input-sm form-control date-class required" id="date_employed" name="date_employed" placeholder="dd-mm-yyyy" value="<?php echo date('d-m-Y')?>">
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
                <label class="col-sm-3 control-label" for="kiwi_id">Employee Kiwi Saver:</label>
                <div class="col-sm-3" >
                    <?php echo form_dropdown('kiwi_id',$kiwi,'','class="form-control input-sm kiwi-class" id="kiwi_id" style="width:134%"');?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="kiwi_id">Employer Kiwi Saver:</label>
                <div class="col-sm-3">
                    <?php echo form_dropdown('employeer_kiwi',$kiwi,'','class="form-control input-sm kiwi-class" id="kiwi_id" style="width:134%"');?>
                </div>
            </div>
            <div class="has-kiwi-save" style="display: none">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="kiwi_id">Kiwi Saver Date Applied:</label>
                    <div class="col-sm-6">
                        <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                            <input type='text' class="input-sm form-control date-class required" id="date_employed" name="kiwi_date_start" placeholder="dd-mm-yyyy" value="<?php echo date('d-m-Y')?>">
                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="esct_rate_id">ESCT Rate:</label>
                <div class="col-sm-3">
                    <?php echo form_dropdown('esct_rate_id',$esct_rate,'','class="form-control input-sm" id="esct_rate_id"');?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="balance">Balance Loans:</label>
                <div class="col-sm-6">
                    <input type="text" name="balance" id="balance" class="form-control balance input-sm number">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label" for="installment">Installment:</label>
                <div class="col-sm-6">
                    <input type="text" name="installment" id="installment" class="form-control installment input-sm number" disabled>
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
                        <input type="text" name="next_of_kin" id="next_of_kin" class="form-control input-sm">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="relationship">Relationship:</label>
                    <div class="col-sm-8">
                        <input type="text" name="relationship" id="relationship" class="form-control input-sm">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="kin_mobile">Mobile No.:</label>
                    <div class="col-sm-6">
                        <input type="text" name="kin_mobile" id="kin_mobile" class="form-control input-sm">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="kin_email">Email:</label>
                    <div class="col-sm-6">
                        <input type="text" name="kin_email" id="kin_email" class="form-control input-sm">
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
    <div class="row">
        <div class="col-sm-11">
            <div class="pull-right" style="margin-right: 55px;">
                <button type="submit" class="btn btn-primary btn-sm submit-btn" name="submit">Submit</button>
                <button type="button" class="btn btn-default btn-sm cancel-btn" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
<?php
echo form_close();
?>
<style>
    .form-horizontal .control-label{
        padding: 5px 0;
    }
    .date-class{
        pointer-events: none;
        background: #e7e7e7;
    }
</style>
<script>
    $(function(e){
        var installment = $('.installment'),
            kiwi_class = $('.kiwi-class'),
            esct_rate_id = $('#esct_rate_id'),
            has_st_loan = [];

        has_st_loan = <?php echo $has_st_loan ? $has_st_loan : '[]'?>;
        $('.tooltip-class').tooltip();
        $('.balance').keyup(function(){
            installment.attr('disabled','disabled');
            installment.removeClass('required');
            if($(this).val()){
                installment.removeAttr('disabled');
                installment.addClass('required');
            }
        });
        $('.datetimepicker').datetimepicker({
            pickTime: false
        });

        $('.bank_account').on('keyup', function() {
            limitText(this, 10)
        });
        var tax_code = $('#tax_code_id');
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

        kiwi_class.change(function () {
            has_kiwi_value($(this).val(),esct_rate_id);
        });

        esct_rate_id.change(function () {
            has_kiwi_value($(this).val(),kiwi_class);
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