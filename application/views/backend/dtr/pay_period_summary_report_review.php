<div class="row">
    <div class="col-sm-12">
        <?php
        $this_date = $date[$_GET['week']];
        $this_data = @$wage_data[$this_date];
        $total_nett = 0;
        $total_dist = 0;
        $total_gross = 0;
        $total_paye = 0;
        $total_st_loan = 0;
        $total_kiwi = 0;
        $total_emp_kiwi = 0;
        $total_esct = 0;
        $total_adjustment = 0;
        $ref = 0;
        $total_data = array();
        if(count($this_data) >0) {
            foreach ($this_data as $val) {
                $last_pay = @$last_pay_data[$val['id']];
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
                                <?php
                                if($val['top_hours']){
                                    ?>
                                    <tr>
                                        <td>Topup Hours:</td>
                                        <td colspan="7"><?php echo $val['top_hours'];?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
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
                                <?php
                                if($val['visa']){
                                    ?>
                                    <tr>
                                        <td>Flight:</td>
                                        <td><?php echo $val['flight'] ? '$ '.number_format($val['flight'],2) : '$ 0.00'?></td>
                                        <td>Visa:</td>
                                        <td><?php echo $val['visa'] ? '$ '.number_format($val['visa'],2) : '$ 0.00'?></td>
                                        <td>Recruit:</td>
                                        <td><?php echo $val['recruit'] ? ($val['recruit'] ? '$ '.number_format($val['recruit'],2) : '$ 0.00') : 'N/A';?></td>
                                        <td>Admin:</td>
                                        <td><?php echo $val['admin'] ? ($val['admin'] ? '$ '.number_format($val['admin'],2) : '$ 0.00') : 'N/A';?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td>Holiday Taken:</td>
                                    <td><?php echo $val['holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <td>Holiday Remaining:</td>
                                    <td><?php echo $val['total_holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <?php
                                    if($val['adjustment']){
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td style="white-space: nowrap">
                                            <?php
                                            echo '$ '.number_format($val['stat_holiday_pay'],2) . ($val['stat_holiday_pay'] ? ' ['.'$ '.$val['stat_holiday_paye'].']' : '');
                                            ?>
                                        </td>
                                        <td>Adjustments:</td>
                                        <td><?php echo $val['adjustment']?></td>
                                    <?php
                                    }else{
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td colspan="3" style="white-space: nowrap">
                                            <?php
                                            echo '$ '.number_format($val['stat_holiday_pay'],2) . ($val['stat_holiday_pay'] ? ' ['.'$ '.$val['stat_holiday_paye'].']' : '');
                                            ?>
                                        </td>
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
                                        <?php
                                        if($val['leave_type'] && $val['leave_type_id'] != 7){
                                            ?>
                                            <td><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                            <td><?php echo $val['leave_type'].' Pay:'?></td>
                                            <td><?php echo $val['leave_pay'] ? '$ '.number_format($val['leave_pay'],2) : '$ 0.00'?></td>
                                        <?php
                                        }
                                        else{
                                            ?>
                                            <td colspan="5"><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <?php
                                        }
                                        ?>
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
                                        <?php
                                        if($val['leave_type'] && $val['leave_type_id'] != 7){
                                            ?>
                                            <td><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                            <td><?php echo $val['leave_type'].' Pay:'?></td>
                                            <td><?php echo $val['leave_pay'] ? '$ '.number_format($val['leave_pay'],2) : '$ 0.00'?></td>
                                        <?php
                                        }
                                        else{
                                            ?>
                                            <td colspan="3"><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <?php
                                        }
                                        ?>
                                        <td>To Bank:</td>
                                        <td style="background: #b2b2b2;color: #000000"><strong><?php echo '$ '.number_format($val['distribution'],2);?></strong></td>
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
                                ?>
                                <tr>
                                    <td>Hours worked:</td>
                                    <td><?php echo number_format($val['hours'],2)?></td>
                                    <td>Gross Pay:</td>
                                    <td><?php echo $val['gross'] ? '$ '.number_format($val['gross'],2) : '$ 0.00'?></td>
                                    <td>PAYE:</td>
                                    <td><?php echo $val['tax'] ? '$ '.number_format($val['tax'],2) : '$ 0.00'?></td>
                                    <td class="text-right">Nett Pay:</td>
                                    <td><strong><?php echo $val['adjustment'] ? '$ '.number_format($val['orig_dis'],2) : ($val['nett'] > 0 ? '$ '.number_format($val['nett'],2) : '$ 0.00')?></strong></td>
                                </tr>
                                <?php
                                if($val['top_hours']){
                                    ?>
                                    <tr>
                                        <td>Topup Hours:</td>
                                        <td colspan="7"><?php echo $val['top_hours'];?></td>
                                    </tr>
                                <?php
                                }
                                ?>
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
                                <?php
                                if($val['visa']){
                                    ?>
                                    <tr>
                                        <td>Flight:</td>
                                        <td><?php echo $val['flight'] ? '$ '.number_format($val['flight'],2) : '$ 0.00'?></td>
                                        <td>Visa:</td>
                                        <td><?php echo $val['visa'] ? '$ '.number_format($val['visa'],2) : '$ 0.00'?></td>
                                        <td>Recruit:</td>
                                        <td><?php echo $val['recruit'] ? ($val['recruit'] ? '$ '.number_format($val['recruit'],2) : '$ 0.00') : 'N/A';?></td>
                                        <td>Admin:</td>
                                        <td><?php echo $val['admin'] ? ($val['admin'] ? '$ '.number_format($val['admin'],2) : '$ 0.00') : 'N/A';?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td>Holiday Taken:</td>
                                    <td><?php echo $val['holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <td>Holiday Remaining:</td>
                                    <td><?php echo $val['total_holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <?php
                                    if($val['adjustment']){
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td style="white-space: nowrap">
                                            <?php
                                            echo '$ '.number_format($val['stat_holiday_pay'],2) . ($val['stat_holiday_pay'] ? ' ['.'$ '.$val['stat_holiday_paye'].']' : '');
                                            ?>
                                        </td>
                                        <td>Adjustments:</td>
                                        <td><?php echo $val['adjustment']?></td>
                                    <?php
                                    }else{
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td colspan="3" style="white-space: nowrap">
                                            <?php
                                            echo '$ '.number_format($val['stat_holiday_pay'],2) . ($val['stat_holiday_pay'] ? ' ['.'$ '.$val['stat_holiday_paye'].']' : '');
                                            ?>
                                        </td>
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
                                        <?php
                                        if($val['leave_type'] && $val['leave_type_id'] != 7){
                                            ?>
                                            <td><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                            <td><?php echo $val['leave_type'].' Pay:'?></td>
                                            <td><?php echo $val['leave_pay'] ? '$ '.number_format($val['leave_pay'],2) : '$ 0.00'?></td>
                                        <?php
                                        }
                                        else{
                                            ?>
                                            <td colspan="5"><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <?php
                                        }
                                        ?>
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
                                        <?php
                                        if($val['leave_type'] && $val['leave_type_id'] != 7){
                                            ?>
                                            <td><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                            <td><?php echo $val['leave_type'].' Pay:'?></td>
                                            <td><?php echo $val['leave_pay'] ? '$ '.number_format($val['leave_pay'],2) : '$ 0.00'?></td>
                                        <?php
                                        }
                                        else{
                                            ?>
                                            <td colspan="3"><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <?php
                                        }
                                        ?>
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
                                    <td><strong><?php echo $val['adjustment'] ? '$ '.number_format($val['orig_dis'],2) : ($val['nett'] > 0 ? '$ '.number_format($val['nett'],2) : '$ 0.00')?></strong></td>
                                </tr>
                                <?php
                                if($val['top_hours']){
                                    ?>
                                    <tr>
                                        <td>Topup Hours:</td>
                                        <td colspan="7"><?php echo $val['top_hours'];?></td>
                                    </tr>
                                <?php
                                }
                                ?>
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
                                <?php
                                if($val['visa']){
                                    ?>
                                    <tr>
                                        <td>Flight:</td>
                                        <td><?php echo $val['flight'] ? '$ '.number_format($val['flight'],2) : '$ 0.00'?></td>
                                        <td>Visa:</td>
                                        <td><?php echo $val['visa'] ? '$ '.number_format($val['visa'],2) : '$ 0.00'?></td>
                                        <td>Recruit:</td>
                                        <td><?php echo $val['recruit'] ? ($val['recruit'] ? '$ '.number_format($val['recruit'],2) : '$ 0.00') : 'N/A';?></td>
                                        <td>Admin:</td>
                                        <td><?php echo $val['admin'] ? ($val['admin'] ? '$ '.number_format($val['admin'],2) : '$ 0.00') : 'N/A';?></td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td>Holiday Taken:</td>
                                    <td><?php echo $val['holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <td>Holiday Remaining:</td>
                                    <td><?php echo $val['total_holiday_leave'].' ('.$val['overall_holiday_leave'].')'?></td>
                                    <?php
                                    if($val['adjustment']){
                                        ?>
                                        <td><?php echo 'Holiday Pay'?>:</td>
                                        <td style="white-space: nowrap">
                                            <?php
                                            echo '$ '.number_format($val['stat_holiday_pay'],2) . ($val['stat_holiday_pay'] ? ' ['.'$ '.$val['stat_holiday_paye'].']' : '');
                                            ?>
                                        </td>
                                        <td>Adjustments:</td>
                                        <td><?php echo $val['adjustment']?></td>
                                    <?php
                                    }else{
                                        ?>
                                        <td>Holiday Pay:</td>
                                        <td colspan="3" style="white-space: nowrap">
                                            <?php
                                            echo '$ '.number_format($val['stat_holiday_pay'],2) . ($val['stat_holiday_pay'] ? ' ['.'$ '.$val['stat_holiday_paye'].']' : '');
                                            ?>
                                        </td>
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
                                        <?php
                                        if($val['leave_type'] && $val['leave_type_id'] != 7){
                                            ?>
                                            <td><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                            <td><?php echo $val['leave_type'].' Pay:'?></td>
                                            <td><?php echo $val['leave_pay'] ? '$ '.number_format($val['leave_pay'],2) : '$ 0.00'?></td>
                                        <?php
                                        }
                                        else{
                                            ?>
                                            <td colspan="5"><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <?php
                                        }
                                        ?>
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
                                        <?php
                                        if($val['leave_type'] && $val['leave_type_id'] != 7){
                                            ?>
                                            <td><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                            <td><?php echo $val['leave_type'].' Pay:'?></td>
                                            <td><?php echo $val['leave_pay'] ? '$ '.number_format($val['leave_pay'],2) : '$ 0.00'?></td>
                                        <?php
                                        }
                                        else{
                                            ?>
                                            <td colspan="3"><?php echo $val['total_sick_leave'].' ('.$val['overall_sick_leave'].')'?></td>
                                        <?php
                                        }
                                        ?>
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
        ?>
    </div>
</div>

<style>
    .inner-table-class{
        font-size: 13px;
        width: 100%;
    }
    .inner-table-class tr td{
        padding: 5px;
        white-space: nowrap!important;
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