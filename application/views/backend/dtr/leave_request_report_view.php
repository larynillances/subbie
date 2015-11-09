<fieldset>
    <legend style="font-size: 16px;;">Annual Pay Details</legend>
    <div class="form-group">
        <label class="control-label col-sm-3">Ordinary Gross:</label>
        <div class="col-sm-6">
            <input type="text" class="form-control input-sm" value="<?php echo @$pay_data['gross'] ? '$ '.number_format(@$pay_data['gross'],2) : '$ 0.00'?>" readonly>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3" for="last-pay-gross">Ordinary Nett Pay:</label>
        <div class="col-sm-6">
            <input type="text" class="input-sm form-control" id="last-pay-gross" value="<?php echo '$ '.number_format($pay_data['distribution'],2)?>" readonly>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3" for="last-pay-gross">Ordinary PAYE:</label>
        <div class="col-sm-6">
            <input type="text" class="input-sm form-control" id="last-pay-gross" value="<?php echo '$ '.number_format($pay_data['tax'],2)?>" readonly>
        </div>
    </div><br/>
    <div class="form-group">
        <label class="control-label col-sm-3" for="annual-pay">Annual Leave Days owing:</label>
        <div class="col-sm-6">
            <input type="text" class="input-sm form-control" id="annual-pay" value="<?php echo $pay_data['total_holiday_leave']?>" readonly>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3" for="public-holidays">Public Holidays owing:</label>
        <div class="col-sm-6">
            <input type="text" class="input-sm form-control" id="public-holidays" value="<?php echo '0'?>" readonly>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3" for="gross-income">Gross Income (This Year):</label>
        <div class="col-sm-6">
            <input type="text" class="input-sm form-control" id="gross-income" value="<?php echo '$ '.number_format($pay_data['total_gross'],2)?>" readonly>
        </div>
    </div>
    <div class="form-group" style="white-space: nowrap!important;">
        <label class="control-label col-sm-3" for="annual-pay">Annual Leave Pay:</label>
        <div class="col-sm-6">
            <input type="text" class="input-sm form-control" id="annual-pay" value="<?php echo '$ '.number_format($pay_data['annual_leave_pay'],2)?>" readonly>
        </div>
        <label class="control-label">Method: <?php echo '('.$pay_data['calculation_type'].')';?></label>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3" for="annual-pay">Annual Leave PAYE:</label>
        <div class="col-sm-6">
            <input type="text" class="input-sm form-control" id="annual-pay" value="<?php echo '$ '.number_format($pay_data['annual_tax'],2)?>" readonly>
        </div>
    </div><br/>
</fieldset>