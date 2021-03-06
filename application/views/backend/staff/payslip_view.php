<style>
    .print-table{
        margin: 0 auto;
        border-collapse: collapse;
        width: 100%;
        font-size: 13px;
    }
    .print-table > thead > tr > th{
        text-align: center;
    }
    .print-table > tbody > tr > td{
        padding: 5px 15px;
        border: 1px solid #000000;
    }
    .bold-text{
        font-weight: bold;
    }
    .deduction-table{
        border-collapse: collapse;
        width: 100%;
        font-size: 13px;
    }
    .deduction-table tr td:last-child{
        text-align: right;
    }
    .inner-table{
        border-collapse: collapse;
        width: 100%;
        height: 120px;
        font-size: 12px!important;
    }
    .inner-table > thead > tr > th{
        border-bottom: 1px solid #000000!important;
        text-align: center;
    }
    .inner-table > thead > tr > th:nth-child(1),
    .inner-table > tbody > tr > td:nth-child(1){
        padding: 3px;
        border-right: 1px solid #000000;
    }
    .inner-table > tbody > tr > td{
        padding: 3px;
        text-align: right!important;
        vertical-align: top;
        height: 50px;
    }
    .warning-msg{
        padding: 5px 15px!important;
    }
    .row div{
        padding: 2px;
    }
</style>
<div id="content" style="width: 850px;margin: 0 auto;">
    <?php
    if(!isset($_GET['dtr'])){
        ?>
        <div class="row">
            <div class="col-sm-3 pull-right">
                <button type="button" class="btn btn-sm btn-success send-payslip" <?php echo $has_email != 1 ? 'disabled' : '';?>><i class="glyphicon glyphicon-send"></i> Send</button>
                <a href="<?php echo base_url('printPaySlip/'.$this->uri->segment(2).'/'.$this->uri->segment(3));?>" target="_blank" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-print"></i> Print</a>
                <a href="<?php echo base_url('wageTable');?>" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-arrow-left"></i> Back</a>
            </div>
            <div class="col-sm-8">
                <?php
                $_warning_no_email = '<div class="alert alert-danger warning-msg" role="alert"><strong>Warning!</strong>This person has no email address.</div>';
                $_warning_manual_send = '<div class="alert alert-danger warning-msg" role="alert"><strong>Warning!</strong>This person has an email address but prefer to send manually.</div>';
                echo !$has_email ? $_warning_no_email : ($has_email == 2 ? $_warning_manual_send : '');
                ?>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="content">
        <?php
        $date = $this->uri->segment(3);
        $name = '';
        $_date = new DateTime($date);
        $week = $_date->format("W");
        $_year = $_date->format("Y");
        $start_wage = date('d/m/Y',strtotime('April '.date('Y',strtotime($date))));
        if(count($staff)>0):
            foreach($staff as $v):
                $name = $v->name;
                $total = @$total_paid[$v->id][$date];
                $balance_data = @$total_bal[$v->id][$_year][$week];
                $total_account_one = @$total['account_one'] ? $v->converted_amount * $total['account_one'] : 0;
                $total_account_two = @$total['account_two'] ? $v->converted_amount * $total['account_two'] : 0;
                ?>
                <table class="print-table">
                    <thead>
                    <tr>
                        <th colspan="4" style="text-transform: uppercase;">
                            <?php echo 'SUBBIE SOLUTIONS LTD.';?>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" style="text-transform: uppercase;">
                            <?php echo 'PROJECT: '.$v->project_name?>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" style="padding: 10px;border: 1px solid #000000">Pay Advice Slip for the Pay Period Ended:
                            <?php
                            echo $week != 30 ? date('j F Y',strtotime('+6 days '.$date)) : date('j F Y',strtotime('+5 days '.$date));
                            echo '&nbsp;[Week '.$week.']';
                            echo $v->is_on_acc_leave ? ($v->acc_pay ? '' : '<br/><strong style="color: red;">On ACC - Nil Pay</strong>') : '';
                            ?>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="border: 1px solid #000000">
                        <td class="bold-text">
                            Name: <span><?php echo $v->name;?></span>
                        </td>
                        <td>
                            IRD No.: <span><?php echo $v->ird_num;?></span>
                        </td>
                        <td>
                            <table style="font-size: 13px;border-collapse: collapse;">
                                <tr>
                                    <td style="text-align: right">Working:</td>
                                    <td style="padding-left: 5px;"><span><?php echo $v->wage_type != 1 ? $v->working_hours : 40;?></span></td>
                                </tr>
                                <tr>
                                    <td style="text-align: right">Non-Working:</td>
                                    <td style="padding-left: 5px;"><span><?php echo @$v->non_working_hours ? @$v->non_working_hours : '00.0'?></span></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <?php echo $v->wage_type != 1 ? 'Hourly Rate:' : 'Fixed Rate:'?> <span><?php echo $v->rate_cost;?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="bold-text" style="text-align: center" rowspan="2">Income <span></span></td>
                        <td class="bold-text" style="text-align: center" rowspan="2">Deductions</td>
                        <?php
                        if($v->nz_account != ''){
                            ?>
                            <td class="bold-text" style="text-align: center" colspan="2">Loans</td>
                        <?php
                        }
                        else{
                            ?>
                            <td class="bold-text">Position: <?php echo $v->position;?></td>
                            <td class="bold-text">Start Date: <?php echo $start_date;?></td>
                        <?php
                        }
                        ?>
                    </tr>
                    <?php
                    if($v->nz_account != ''){
                        ?>
                        <tr>
                            <td class="bold-text" style="text-align: center">Start Bal</td>
                            <td class="bold-text" style="text-align: center">Bal Outs</td>
                        </tr>
                    <?php
                    }else{
                        ?>
                        <tr>
                            <td class="bold-text" style="text-align: center">This Pay</td>
                            <td class="bold-text" style="text-align: center">Year to date<br/> (<?php echo $start_wage;?>)</td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr style="vertical-align: top">
                        <td>
                            <table style="font-size: 13px;border-collapse: collapse;width: 100%;">
                                <tr>
                                    <td style="text-align: right">
                                        <?php echo $v->stat_holiday_pay && $v->rate_cost && !$v->is_on_acc_leave ? 'Worked Hours:' : 'Wage Gross'?>
                                    </td>
                                    <td style="padding-left: 5px;text-align: right;">
                                        <span><?php echo $v->gross_ ? '$'.number_format(($v->gross_ - $v->stat_holiday_pay),2) : '';?></span>
                                    </td>
                                </tr>
                                <?php
                                if($v->is_on_acc_leave && $v->acc_pay){
                                  ?>
                                    <tr>
                                        <td style="text-align: right">ACC Pay:</td>
                                        <td style="padding-left: 5px;"><span><?php echo $v->acc_pay ? '$'.number_format($v->acc_pay,2) : 'Nil';?></span></td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <?php
                                if($v->stat_holiday_pay && $v->rate_cost && !$v->is_on_acc_leave){
                                    ?>
                                    <tr>
                                        <td style="text-align: right">Holiday Pay:</td>
                                        <td style="padding-left: 5px;text-align: right;"><span><?php echo $v->stat_holiday_pay ? '$'.number_format($v->stat_holiday_pay,2) : '';?></span></td>
                                    </tr>
                                    <tr>
                                        <td style="text-align: right"><strong>Wage Gross:</strong></td>
                                        <td colspan="2" style="padding-left: 5px;border-top: 1px solid #000000;text-align: right;"><strong><span><?php echo $v->gross_ ? '$'.number_format($v->gross_,2) : '';?></span></strong></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </td>
                        <td class="deduction-column">
                            <table class="deduction-table">
                                <tr>
                                    <th style="text-align: left">Debt</th>
                                    <th style="text-align: left">Deduction</th>
                                    <th>Amount</th>
                                </tr>
                                <?php
                                if($v->nz_account != ''){
                                    ?>
                                    <tr>
                                        <td></td>
                                        <td>PAYE</td>
                                        <td><?php echo @$v->tax ? @number_format($v->tax,2) : '$ 0.00';?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo @$balance_data['flight_debt'] ? '$'.@$balance_data['flight_debt'] : '&nbsp;';?></td>
                                        <td>Flight</td>
                                        <td><?php echo $v->total_flight;?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo @$balance_data['visa_debt'] ? '$'.@$balance_data['visa_debt'] : '&nbsp;';?></td>
                                        <td>Visa</td>
                                        <td><?php echo $v->total_visa;?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Accom</td>
                                        <td><?php echo $v->total_accom != '' ? number_format($v->total_accom,2) : '&nbsp;';?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Transport</td>
                                        <td><?php echo $v->total_trans;?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Recruit</td>
                                        <td><?php echo $v->recruit;?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Admin</td>
                                        <td><?php echo $v->admin;?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Loans</td>
                                        <td><?php echo $v->total_install;?></td>
                                    </tr>
                                <?php
                                }
                                else{
                                    ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>PAYE</td>
                                        <td><?php echo @$v->tax ? @number_format($v->tax,2,'.','') : '0.00';?></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>Student Loan</td>
                                        <td><?php echo $v->st_loan ? number_format($v->st_loan,2,'.','') : '0.00';?></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>Kiwi Saver</td>
                                        <td><?php echo $v->kiwi_ ? number_format($v->kiwi_,2,'.','') : '0.00';?></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <td></td>
                                    <td><strong>Total</strong></td>
                                    <td style="border-top: 1px solid #000000"><strong><?php echo '$ '.number_format($v->total,2);?></strong></td>
                                </tr>
                            </table>
                        </td>
                        <td style="padding-left: 20px">
                            <table class="deduction-table">
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php
                                        if(!$v->nz_account){
                                            echo $v->adjustment_ ? '$ '.number_format($v->orig_net,2) : ($v->distribution ? '$ '.number_format($v->distribution,2) : '');
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <?php
                                    echo !$v->nz_account && $v->adjustment_ ? '<td>Adjustment: ' . $v->adjustment_ . '</td>' : '<td>&nbsp;</td>';
                                    ?>
                                </tr>
                                <?php
                                if($v->nz_account != ''){
                                    ?>
                                    <tr>
                                        <td style="text-align: center"><?php echo @$balance_data['balance'] != 0 ? $v->star_balance : '&nbsp;';?></td>
                                    </tr>
                                <?php
                                }else{
                                    ?>
                                    <tr>
                                        <td style="border-top: 1px solid #000000;"><strong><?php echo $v->distribution ? '$ '.number_format($v->distribution,2) : '';?></strong></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </td>
                        <td style="text-align: center">
                            <table class="deduction-table">
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><?php echo !$v->nz_account ? (@$total['distribution'] ? '$ '.number_format($total['distribution'],2) : '&nbsp;') : '';?></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <?php
                                if($v->nz_account != ''){
                                    ?>
                                    <tr>
                                        <td style="text-align: center">
                                            <?php
                                            echo @$balance_data['balance'] != 0 ? number_format(@$balance_data['balance'],2) : '&nbsp;';
                                            ?>
                                        </td>
                                    </tr>
                                <?php
                                }else{
                                    ?>
                                    <tr>
                                        <td style="border-top: 1px solid #000000;"><strong><?php echo @$total['distribution'] ? '$ '.number_format($total['distribution'],2) : '&nbsp;';?></strong></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: left;">Subtotal/NETT Pay: <span><?php echo $v->orig_dis ? '$ '.number_format($v->orig_dis,2) : '';?></span></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <strong>Distribution</strong>
                        </td>
                        <td style="text-align: center;">
                            <?php echo $v->nz_account ? 'PHP One(self)' : '&nbsp;'?>
                        </td>
                        <td style="text-align: center;">
                            <strong><?php echo $v->nz_account ? 'PHP Two(wife)' : '&nbsp;' /*'Holiday <span style="color: #ff0000">(' .$total_holiday_leave.')</span>'*/?></strong>
                        </td>
                        <td style="text-align: center;">
                            <strong><?php echo $v->nz_account ? 'NZ ACC' : '&nbsp;'/*'Sick Leave <span style="color: #ff0000">(' .$total_sick_leave.')</span>'*/?></strong>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center;padding: 0!important;<?php echo !$v->nz_account ? 'height:60px!important;' : ''?>">
                            <?php if($v->nz_account){
                                ?>
                                <table class="inner-table">
                                    <thead>
                                    <tr>
                                        <th>This Pay</th>
                                        <th>Year to date<br/> (<?php echo $start_wage;?>)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <strong>
                                                <?php
                                                echo $v->adjustment_ ? '$ '.number_format($v->orig_dis,2) : ($v->distribution ? '$ '.number_format($v->distribution,2) : '');
                                                ?>
                                            </strong>
                                            <?php
                                            echo $v->adjustment_ ? '<br/><br/><span style="border-bottom: 1px solid #000000;">Adjust: ' . $v->adjustment_.'</span><br/>'.($v->distribution ? '$ '.number_format($v->distribution,2) : '') : '&nbsp;';
                                            ?>
                                        </td>
                                        <td>
                                            <strong><?php echo @$total['distribution'] ? '$'.number_format(@$total['distribution'],2) : '';?></strong>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            <?php
                            }
                            else{
                                ?>
                                <strong><?php echo $v->distribution ? '$'.number_format($v->distribution,2) : '';?></strong>
                            <?php
                            }?>
                        </td>
                        <td style="text-align: center;padding: 0!important;">
                            <?php if($v->nz_account){?>
                                <table class="inner-table">
                                    <thead>
                                    <tr>
                                        <th>This Pay</th>
                                        <th>Year to date<br/> (<?php echo $start_wage;?>)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <span><?php echo $v->nz_account ? ($v->account_one ? $v->account_one : '$ 0.00') : '&nbsp;';?></span><br/>
                                            <span style="color: #ff0000;font-weight: bold"><?php echo $v->nz_account ? $v->account_one_ : '&nbsp;';?></span>
                                        </td>
                                        <td>
                                            <span><?php echo $v->nz_account ? '$'.number_format(@$total['account_one'],2) : '&nbsp;';?></span><br/>
                                            <span style="color: #ff0000;font-weight: bold"><?php echo $total_account_one ? $v->symbols.number_format($total_account_one,2) : '&nbsp;';?></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            <?php
                            }?>
                        </td>
                        <td style="text-align: center;padding: 0!important;">
                            <?php if($v->nz_account){
                                ?>
                                <table class="inner-table">
                                    <thead>
                                    <tr>
                                        <th>This Pay</th>
                                        <th>Year to date<br/> (<?php echo $start_wage;?>)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <span><?php echo $v->total_account_two ? '$'.number_format($v->total_account_two,2,'.',',') : '$ 0.00';?></span><br/>
                                            <span style="color: #ff0000;font-weight: bold"><?php echo $v->visa ? $v->account_two_ : '&nbsp;';?></span>
                                        </td>
                                        <td>
                                            <span><?php echo @$total['account_two'] ? '$'.number_format(@$total['account_two'],2) : '&nbsp;';?></span><br/>
                                            <span style="color: #ff0000;font-weight: bold"><?php echo $total_account_two ? $v->symbols.number_format($total_account_two,2) : '&nbsp;';?></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            <?php
                            }else{
                                ?>
                                <!--<table class="inner-table">
                                    <thead>
                                    <tr>
                                        <th style="width: 50%;">Remaining</th>
                                        <th>Taken</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="padding-right: 10px;"><?php /*echo $total_holiday_leave*/?></td>
                                        <td style="padding-right: 10px;">0</td>
                                    </tr>
                                    </tbody>
                                </table>-->
                            <?php
                            }?>
                        </td>
                        <td style="text-align: center;padding: 0!important;">
                            <?php if($v->nz_account){
                                ?>
                                <table class="inner-table">
                                    <thead>
                                    <tr>
                                        <th>This Pay</th>
                                        <th>Year to date<br/> (<?php echo $start_wage;?>)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <span><?php echo $v->total_nz_account ? '$'.number_format($v->total_nz_account,2,'.',',') : '';?></span>
                                        </td>
                                        <td>
                                            <span><?php echo @$total['nz_account'] ? '$'.number_format(@$total['nz_account'],2,'.',',') : '';?></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            <?php
                            }else{
                                ?>
                                <!--<table class="inner-table">
                                    <thead>
                                    <tr>
                                        <th style="width: 50%;">Remaining</th>
                                        <th>Taken</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td style="padding-right: 10px;"><?php /*echo $total_sick_leave*/?></td>
                                        <td style="padding-right: 10px;">0</td>
                                    </tr>
                                    </tbody>
                                </table>-->
                            <?php
                            }?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            <?php
            endforeach;
        endif;
        ?>
    </div>
</div>
<?php
if(!isset($_GET['dtr'])){
    ?>
    <script>
        $(function(){
            $('.send-payslip').click(function(){
                $(this).modifiedModal({
                    url: bu + 'sendStaffPaySlip<?php echo '/'.$this->uri->segment(2).'/'.$this->uri->segment(3).'/'.$this->uri->segment(4).'?type='.$_GET['type']?>',
                    title: 'Send Pay Slip',
                    type: 'small'
                });
            });
            jQuery(window).load(function () {
                $(this).newForm.removeLoadingForm();
            });
        });
    </script>
<?php
}
?>