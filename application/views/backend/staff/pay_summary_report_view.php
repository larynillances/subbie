<style>
    .inner-table-class{
        font-size: 13px;
        width: 100%;
    }
    .inner-table-class tr td{
        padding: 5px;
    }
    .inner-table-class tr td:nth-child(odd){
        width: 12%;
        font-weight: bold;
        text-align: right;
        white-space: nowrap;
    }
    .inner-table-class tr td:nth-child(even){
        color: #0000ff;
    }
    .inner-table-class tr td:nth-child(2){
        width: 15%;
    }
    .inner-table-class tr td:nth-child(4){
        width: 15%;
    }
    .inner-table-class tr td:last-child{
        width: 12%;
    }
    .content-div{
        padding: 5px;
        border-bottom: 1px dotted #000000;
    }
    .content-div:first-child{
        border-top: 1px dotted #000000;
    }
</style>
<?php
echo form_open('','class="form-horizontal"');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group form-class">
            <label class="col-sm-1 control-label" >Date:</label>
            <div class="col-sm-2">
                <?php echo form_dropdown('month',$month,$thisMonth,'class="form-control input-sm select month-dp"')?>
            </div>
            <div class="col-sm-1">
                <?php echo form_dropdown('year',$year,$thisYear,'class="form-control input-sm select year-dp" style="width:120%;"')?>
            </div>
            <label class="col-sm-1 control-label" >Week:</label>
            <div class="col-sm-1 week-display">
                <?php echo form_dropdown('week',$week,$thisWeek,'class="form-control input-sm"')?>
            </div>
            <!--<div class="col-sm-2">
                <?php /*echo form_dropdown('project_type',$project_type,$thisProject,'class="form-control input-sm"')*/?>
            </div>-->
            <div class="col-sm-3">
                <input type="submit" name="search" class="btn btn-success btn-sm" value="Go">
                <a href="<?php echo base_url().'payPeriodSummaryReport?print=1'?>" class="btn btn-sm btn-success" target="_blank">Print</a>
            </div>
        </div>
    </div>
</div><br/>
<?php
echo form_close();
?>
<div class="row">
    <div class="col-sm-12">
        <?php
        $this_date = $date[$thisWeek];
        $this_data = @$wage_data[$this_date];
        $total_nett = 0;
        $total_dist = 0;
        $total_gross = 0;
        $total_paye = 0;
        $total_st_loan = 0;
        $total_kiwi = 0;
        $total_emp_kiwi = 0;
        $total_esct = 0;
        $ref = 0;
        $total_data = array();
        if(count($this_data) >0) {
            foreach ($this_data as $val) {
                $last_pay = @$last_pay_data[$val['id']];
                if($val['hours'] > 0){
                    $total_nett += $val['nett'];
                    $total_dist += $val['distribution'];
                    $total_gross += $val['gross'];
                    $total_paye += $val['tax'];
                    $total_st_loan += $val['st_loan'];
                    $total_kiwi += $val['kiwi'];
                    $total_emp_kiwi += $val['cec'];
                    $total_esct += $val['esct'];

                    $total_data[$project_type[$val['project_id']]][] = array(
                        'total_nett' => $val['nett'],
                        'total_dist' => $val['distribution'],
                        'total_gross' => $val['gross'],
                        'total_paye' => $val['tax'],
                        'total_st_loan' => $val['st_loan'],
                        'total_kiwi' => $val['kiwi'],
                        'total_emp_kiwi' => $val['cec'],
                        'total_esct' => $val['esct']
                    );

                }
                ?>
                <div class="content-div">
                    <table class="inner-table-class">
                        <tbody>
                        <tr>
                            <td>Franchise Name:</td>
                            <td><?php echo 'Subbie Solutions'?></td>
                            <td>Employee:</td>
                            <td><?php echo $val['name']?></td>
                            <td>IRD Number:</td>
                            <td><?php echo $val['ird_num']?></td>
                            <td>Tax Code:</td>
                            <td><?php echo $val['tax_code']?></td>
                        </tr>
                        <?php
                        if(count(@$last_pay) > 0){
                            if($last_pay['last_week'] == $thisWeek){
                                $_annual_nett = ($last_pay['annual_leave_pay'] - $last_pay['annual_tax']);
                                $final_pay = $last_pay['distribution'] + $_annual_nett;
                                $total_dist += $final_pay;
                                ?>
                                <tr>
                                    <td>Hours worked:</td>
                                    <td><?php echo number_format($last_pay['hours'],2)?></td>
                                    <td>Ordinary Gross:</td>
                                    <td><?php echo $last_pay['gross'] ? '$ '.number_format($last_pay['gross'],2) : '$ 0.00'?></td>
                                    <td>Ordinary PAYE:</td>
                                    <td><?php echo $last_pay['tax'] ? '$ '.number_format($last_pay['tax'],2) : '$ 0.00'?></td>
                                    <td class="text-right">Ordinary Nett Pay:</td>
                                    <td><strong><?php echo $last_pay['distribution'] > 0 ? '$ '.number_format($last_pay['distribution'],2) : '$ 0.00'?></strong></td>
                                </tr>
                                <tr>
                                    <td>Student Loan:</td>
                                    <td><?php echo $val['st_loan'] ? '$ '.number_format($val['st_loan'],2) : '$ 0.00'?></td>
                                    <td>Kiwisaver:</td>
                                    <td><?php echo $val['kiwi'] ? '$ '.number_format($val['kiwi'],2) : '$ 0.00'?></td>
                                    <td>ESCT:</td>
                                    <td><?php echo $val['has_kiwi'] ? ($val['esct'] ? '$ '.number_format($val['esct'],2) : '$ 0.00') : 'N/A';?></td>
                                    <td>CEC:</td>
                                    <td><?php echo $val['has_kiwi'] ? ($val['cec'] ? '$ '.number_format($val['cec'],2) : '$ 0.00') : 'N/A';?></td>
                                </tr>
                                <tr>
                                    <td>Accom.:</td>
                                    <td><?php echo $val['hours'] > 0 ? '$ '.number_format($val['accommodation'],2) : '$ 0.00';?></td>
                                    <td>Transport:</td>
                                    <td><?php echo $val['hours'] > 0 ? '$ '.number_format($val['transport'],2) : '$ 0.00';?></td>
                                    <td>ACC Levy:</td>
                                    <td><?php echo $val['acc_pay'] && $val['is_on_acc_leave'] ? '$ '.number_format($val['acc_pay'],2) : 'Nil';?></td>
                                    <td>Loan Repay:</td>
                                    <td>
                                        <?php
                                        $thisBalance =  @$total_bal[$v][$val['id']]['balance'];
                                        echo $thisBalance;
                                        echo $thisBalance > 0 ? ($val['installment'] ? '$ '.number_format($val['installment'],2) : '$ 0.00') : '$ 0.00';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Holiday Taken:</td>
                                    <td><?php echo $val['holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <td>Holiday Remaining:</td>
                                    <td><?php echo $val['total_holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <?php
                                    if($val['adjustment']){
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td><?php echo '$ '.number_format($val['stat_holiday_pay'],2)?></td>
                                        <td>Adjustments:</td>
                                        <td><?php echo $val['adjustment']?></td>
                                        <?php
                                    }else{
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td colspan="3"><?php echo '$ '.number_format($val['stat_holiday_pay'],2)?></td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                <?php
                                if($val['has_nz_account']){
                                    ?>
                                    <tr>
                                        <td>Sick Leave Taken:</td>
                                        <td><?php echo $last_pay['sick_leave_taken'].' ('.$last_pay['overall_sick_leave'].')'?></td>
                                        <td>Sick Leave Remaining:</td>
                                        <td colspan="5"><?php echo $last_pay['total_sick_leave'].' ('.$last_pay['overall_sick_leave'].')'?></td>
                                    </tr>
                                    <tr>
                                        <td>PHP One:</td>
                                        <td><?php echo $last_pay['nz_account'] ? '$ '.number_format($last_pay['account_one'],2) : 'N/A'?></td>
                                        <td>PHP Two:</td>
                                        <td colspan="5"><?php echo $last_pay['nz_account'] ? '$ '.number_format($last_pay['account_two'],2) : 'N/A'?></td>
                                    </tr>
                                <?php
                                }else{
                                    ?>
                                    <tr>
                                        <td>Sick Leave Taken:</td>
                                        <td><?php echo $last_pay['sick_leave_taken'].' ('.$last_pay['overall_sick_leave'].')'?></td>
                                        <td>Sick Leave Remaining:</td>
                                        <td colspan="5"><?php echo $last_pay['total_sick_leave'].' ('.$last_pay['overall_sick_leave'].')'?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td>Annual Leave Days:</td>
                                    <td><?php echo $last_pay['total_holiday_leave']?></td>
                                    <td>Public Holidays:</td>
                                    <td><?php echo '0'?></td>
                                    <td>Gross Income:</td>
                                    <td><?php echo '$ '.number_format($last_pay['total_gross'],2)?></td>
                                    <td>Method:</td>
                                    <td><?php echo $last_pay['calculation_type'];?></td>
                                </tr>
                                <tr>
                                    <td>Annual Leave Pay:</td>
                                    <td><?php echo '$ '.number_format($last_pay['annual_leave_pay'],2)?></td>
                                    <td>Annual Leave PAYE:</td>
                                    <td><?php echo '$ '.number_format($last_pay['annual_tax'],2)?></td>
                                    <td>Annual Leave<br/>NETT:</td>
                                    <td><?php echo '$ '.number_format($_annual_nett,2)?></td>
                                    <td>Final Pay:</td>
                                    <td style="background: #b2b2b2;color: #000000"><strong><?php echo '$'.number_format($final_pay,2);?></strong></td>
                                </tr>
                                <?php
                            }
                            else{
                                ?><tr>
                                <td>Hours worked:</td>
                                <td><?php echo number_format($val['hours'],2)?></td>
                                <td>Gross Pay:</td>
                                <td><?php echo $val['gross'] ? '$ '.number_format($val['gross'],2) : '$ 0.00'?></td>
                                <td>PAYE:</td>
                                <td><?php echo $val['tax'] ? '$ '.number_format($val['tax'],2) : '$ 0.00'?></td>
                                <td class="text-right">Nett Pay:</td>
                                <td><strong><?php echo $val['adjustment'] ? '$ '.number_format($val['orig_nett'],2) : ($val['nett'] > 0 ? '$ '.number_format($val['nett'],2) : '$ 0.00')?></strong></td>
                                </tr>
                                <tr>
                                    <td>Student Loan:</td>
                                    <td><?php echo $val['st_loan'] ? '$ '.number_format($val['st_loan'],2) : '$ 0.00'?></td>
                                    <td>Kiwisaver:</td>
                                    <td><?php echo $val['kiwi'] ? '$ '.number_format($val['kiwi'],2) : '$ 0.00'?></td>
                                    <td>ESCT:</td>
                                    <td><?php echo $val['has_kiwi'] ? ($val['esct'] ? '$ '.number_format($val['esct'],2) : '$ 0.00') : 'N/A';?></td>
                                    <td>CEC:</td>
                                    <td><?php echo $val['has_kiwi'] ? ($val['cec'] ? '$ '.number_format($val['cec'],2) : '$ 0.00') : 'N/A';?></td>
                                </tr>
                                <tr>
                                    <td>Accom.:</td>
                                    <td><?php echo $val['hours'] > 0 ? '$ '.number_format($val['accommodation'],2) : '$ 0.00';?></td>
                                    <td>Transport:</td>
                                    <td><?php echo $val['hours'] > 0 ? '$ '.number_format($val['transport'],2) : '$ 0.00';?></td>
                                    <td>ACC Levy:</td>
                                    <td><?php echo $val['acc_pay'] && $val['is_on_acc_leave'] ? '$ '.number_format($val['acc_pay'],2) : 'Nil';?></td>
                                    <td>Loan Repay:</td>
                                    <td>
                                        <?php
                                        $thisBalance =  @$total_bal[$v][$val['id']]['balance'];
                                        echo $thisBalance;
                                        echo $thisBalance > 0 ? ($val['installment'] ? '$ '.number_format($val['installment'],2) : '$ 0.00') : '$ 0.00';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Holiday Taken:</td>
                                    <td><?php echo $val['holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <td>Holiday Remaining:</td>
                                    <td><?php echo $val['total_holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <?php
                                    if($val['adjustment']){
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td><?php echo '$ '.number_format($val['stat_holiday_pay'],2)?></td>
                                        <td>Adjustments:</td>
                                        <td><?php echo $val['adjustment']?></td>
                                    <?php
                                    }else{
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td colspan="3"><?php echo '$ '.number_format($val['stat_holiday_pay'],2)?></td>
                                    <?php
                                    }
                                    ?>
                                </tr>
                                <?php
                                if($val['has_nz_account']){
                                    ?>
                                    <tr>
                                        <td>Sick Leave Taken:</td>
                                        <td><?php echo $val['sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <td>Sick Leave Remaining:</td>
                                        <td colspan="5"><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                    </tr>
                                    <tr>
                                        <td>PHP One:</td>
                                        <td><?php echo $val['nz_account'] ? '$ '.number_format($val['account_one'],2) : 'N/A'?></td>
                                        <td>PHP Two:</td>
                                        <td colspan="3"><?php echo $val['nz_account'] ? '$ '.number_format($val['account_two'],2) : 'N/A'?></td>
                                        <td>To Bank:</td>
                                        <td style="background: #b2b2b2;color: #000000"><strong><?php echo $val['hours'] ? '$ '.number_format($val['nz_account'],2) : '$ '.number_format($val['distribution'],2);?></strong></td>
                                    </tr>
                                <?php
                                }
                                else{
                                    ?>
                                    <tr>
                                        <td>Sick Leave Taken:</td>
                                        <td><?php echo $val['sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <td>Sick Leave Remaining:</td>
                                        <td colspan="3"><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <td>To Bank:</td>
                                        <td style="background: #b2b2b2;color: #000000"><strong><?php echo '$ '.number_format($val['distribution'],2);?></strong></td>
                                    </tr>
                                <?php
                                }
                            }
                        }
                        else{
                            if($val['is_on_acc_leave'] && !$val['acc_pay']){
                                ?>
                                <tr>
                                    <td>Hours worked:</td>
                                    <td>Nil</td>
                                    <td>Gross Pay:</td>
                                    <td>Nil</td>
                                    <td>PAYE:</td>
                                    <td>Nil</td>
                                    <td class="text-right">Nett Pay:</td>
                                    <td><strong>Nil</strong></td>
                                </tr>
                                <tr>
                                    <td>Student Loan:</td>
                                    <td>Nil</td>
                                    <td>Kiwisaver:</td>
                                    <td>Nil</td>
                                    <td>ESCT:</td>
                                    <td>Nil</td>
                                    <td>CEC:</td>
                                    <td>Nil</td>
                                </tr>
                                <tr>
                                    <td>Accom.:</td>
                                    <td>Nil</td>
                                    <td>Transport:</td>
                                    <td>Nil</td>
                                    <td>ACC Levy:</td>
                                    <td>Nil</td>
                                    <td>Loan Repay:</td>
                                    <td>Nil</td>
                                </tr>
                                <tr>
                                    <td>Holiday Taken:</td>
                                    <td>Nil</td>
                                    <td>Holiday Remaining:</td>
                                    <td>Nil</td>
                                    <td>Holiday Pay:</td>
                                    <td colspan="3">Nil</td>
                                </tr>
                                <tr>
                                    <td>Sick Leave Taken:</td>
                                    <td>Nil</td>
                                    <td>Sick Leave Remaining:</td>
                                    <td colspan="3">Nil</td>
                                    <td>To Bank:</td>
                                    <td style="background: #b2b2b2;color: #000000"><strong>Nil</strong></td>
                                </tr>
                                <?php
                            }
                            else{
                                ?>
                                <tr>
                                    <td>Hours worked:</td>
                                    <td><?php echo number_format($val['hours'],2)?></td>
                                    <td>Gross Pay:</td>
                                    <td><?php echo $val['gross'] ? '$ '.number_format($val['gross'],2) : '$ 0.00'?></td>
                                    <td>PAYE:</td>
                                    <td><?php echo $val['tax'] ? '$ '.number_format($val['tax'],2) : '$ 0.00'?></td>
                                    <td class="text-right">Nett Pay:</td>
                                    <td><strong><?php echo $val['adjustment'] ? '$ '.number_format($val['orig_nett'],2) : ($val['nett'] > 0 ? '$ '.number_format($val['nett'],2) : '$ 0.00')?></strong></td>
                                </tr>
                                <tr>
                                    <td>Student Loan:</td>
                                    <td><?php echo $val['st_loan'] ? '$ '.number_format($val['st_loan'],2) : '$ 0.00'?></td>
                                    <td>Kiwisaver:</td>
                                    <td><?php echo $val['kiwi'] ? '$ '.number_format($val['kiwi'],2) : '$ 0.00'?></td>
                                    <td>ESCT:</td>
                                    <td><?php echo $val['has_kiwi'] ? ($val['esct'] ? '$ '.number_format($val['esct'],2) : '$ 0.00') : 'N/A';?></td>
                                    <td>CEC:</td>
                                    <td><?php echo $val['has_kiwi'] ? ($val['cec'] ? '$ '.number_format($val['cec'],2) : '$ 0.00') : 'N/A';?></td>
                                </tr>
                                <tr>
                                    <td>Accom.:</td>
                                    <td><?php echo $val['hours'] > 0 ? '$ '.number_format($val['accommodation'],2) : '$ 0.00';?></td>
                                    <td>Transport:</td>
                                    <td><?php echo $val['hours'] > 0 ? '$ '.number_format($val['transport'],2) : '$ 0.00';?></td>
                                    <td>ACC Levy:</td>
                                    <td><?php echo $val['acc_pay'] && $val['is_on_acc_leave'] ? '$ '.number_format($val['acc_pay'],2) : 'Nil';?></td>
                                    <td>Loan Repay:</td>
                                    <td>
                                        <?php
                                        $thisBalance =  @$total_bal[$v][$val['id']]['balance'];
                                        echo $thisBalance;
                                        echo $thisBalance > 0 ? ($val['installment'] ? '$ '.number_format($val['installment'],2) : '$ 0.00') : '$ 0.00';
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Holiday Taken:</td>
                                    <td><?php echo $val['holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <td>Holiday Remaining:</td>
                                    <td><?php echo $val['total_holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <?php
                                    if($val['adjustment']){
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td><?php echo '$ '.number_format($val['stat_holiday_pay'],2)?></td>
                                        <td>Adjustments:</td>
                                        <td><?php echo $val['adjustment']?></td>
                                    <?php
                                    }else{
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td colspan="3"><?php echo '$ '.number_format($val['stat_holiday_pay'],2)?></td>
                                    <?php
                                    }
                                    ?>
                                </tr>
                                <?php
                                if($val['has_nz_account']){
                                    ?>
                                    <tr>
                                        <td>Sick Leave Taken:</td>
                                        <td><?php echo $val['sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <td>Sick Leave Remaining:</td>
                                        <td colspan="5"><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                    </tr>
                                    <tr>
                                        <td>PHP One:</td>
                                        <td><?php echo $val['nz_account'] ? '$ '.number_format($val['account_one'],2) : 'N/A'?></td>
                                        <td>PHP Two:</td>
                                        <td colspan="3"><?php echo $val['nz_account'] ? '$ '.number_format($val['account_two'],2) : 'N/A'?></td>
                                        <td>To Bank:</td>
                                        <td style="background: #b2b2b2;color: #000000"><strong><?php echo $val['hours'] ? '$ '.number_format($val['nz_account'],2) : '$ '.number_format($val['distribution'],2);?></strong></td>
                                    </tr>
                                <?php
                                }
                                else{
                                    ?>
                                    <tr>
                                        <td>Sick Leave Taken:</td>
                                        <td><?php echo $val['sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <td>Sick Leave Remaining:</td>
                                        <td colspan="3"><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <td>To Bank:</td>
                                        <td style="background: #b2b2b2;color: #000000"><strong><?php echo '$ '.number_format($val['distribution'],2);?></strong></td>
                                    </tr>
                                <?php
                                }
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            <?php
            }
        }
        ksort($total_data);
        if(count($total_data) > 0){
            foreach($total_data as $key=>$data){
                $_gross = 0;
                $_paye = 0;
                $_st_loan = 0;
                $_nett = 0;
                $_kiwi = 0;
                $_emp_kiwi = 0;
                $_esct = 0;
                $_dist = 0;
                if(count($data) > 0){
                    foreach($data as $val){
                        $_gross += $val['total_gross'];
                        $_paye += $val['total_paye'];
                        $_st_loan += $val['total_st_loan'];
                        $_nett += $val['total_nett'];
                        $_kiwi += $val['total_kiwi'];
                        $_emp_kiwi += $val['total_emp_kiwi'];
                        $_esct += $val['total_esct'];
                        $_dist += $val['total_dist'];
                    }
                }
                ?>
                <div class="content-div">
                    <table class="inner-table-class">
                        <tr>
                            <td colspan="8" style="text-align: left;text-transform: uppercase;background: #dadada"><strong><?php echo $key.' Project Sub-Total:';?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-right"><strong>GROSS:</strong></td>
                            <td><strong><?php echo '$ '.number_format($_gross,2)?></strong></td>
                            <td class="text-right"><strong>PAYE:</strong></td>
                            <td><strong><?php echo '$ '.number_format($_paye,2)?></strong></td>
                            <td class="text-right"><strong>Student Loan:</strong></td>
                            <td><strong><?php echo '$ '.number_format($_st_loan,2)?></strong></td>
                            <td class="text-right"><strong>Nett Pay:</strong></td>
                            <td><strong><?php echo '$ '.number_format($_nett,2)?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-right"><strong>Kiwisaver Employee:</strong></td>
                            <td><strong><?php echo '$ '.number_format($_kiwi,2)?></strong></td>
                            <td class="text-right"><strong>Kiwisaver Employer:</strong></td>
                            <td><strong><?php echo '$ '.number_format($_emp_kiwi,2)?></strong></td>
                            <td class="text-right"><strong>ESCT:</strong></td>
                            <td><strong><?php echo '$ '.number_format($_esct,2)?></strong></td>
                            <td class="text-right"><strong>To Bank:</strong></td>
                            <td style="background: #b2b2b2;color: #000000"><strong><?php echo '$ '.number_format($_dist,2)?></strong></td>
                        </tr>
                    </table>
                </div>
            <?php
            }
        }
        ?>
        <div class="content-div">
            <table class="inner-table-class">
                <tr>
                    <td colspan="8" style="text-align: left;background: #dab7b6"><strong>GRAND TOTAL:</strong></td>
                </tr>
                <tr>
                    <td class="text-right"><strong>GROSS:</strong></td>
                    <td><strong><?php echo '$ '.number_format($total_gross,2)?></strong></td>
                    <td class="text-right"><strong>PAYE:</strong></td>
                    <td><strong><?php echo '$ '.number_format($total_paye,2)?></strong></td>
                    <td class="text-right"><strong>Student Loan:</strong></td>
                    <td><strong><?php echo '$ '.number_format($total_st_loan,2)?></strong></td>
                    <td class="text-right"><strong>Nett Pay:</strong></td>
                    <td><strong><?php echo '$ '.number_format($total_nett,2)?></strong></td>
                </tr>
                <tr>
                    <td class="text-right"><strong>Kiwisaver Employee:</strong></td>
                    <td><strong><?php echo '$ '.number_format($total_kiwi,2)?></strong></td>
                    <td class="text-right"><strong>Kiwisaver Employer:</strong></td>
                    <td><strong><?php echo '$ '.number_format($total_emp_kiwi,2)?></strong></td>
                    <td class="text-right"><strong>ESCT:</strong></td>
                    <td><strong><?php echo '$ '.number_format($total_esct,2)?></strong></td>
                    <td class="text-right"><strong>To Bank:</strong></td>
                    <td style="background: #b2b2b2;color: #000000"><strong><?php echo '$ '.number_format($total_dist,2)?></strong></td>
                </tr>
            </table>
        </div>
    </div>
</div>