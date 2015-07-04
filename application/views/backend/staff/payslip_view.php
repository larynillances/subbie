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
    <div class="row">
        <div class="col-sm-2">
            <a href="<?php echo base_url('printPaySlip/'.$this->uri->segment(2).'/'.$this->uri->segment(3));?>" target="_blank" class="btn btn-sm btn-primary"><i class="glyphicon glyphicon-print"></i> Print</a>
            <button type="button" class="btn btn-sm btn-success send-payslip" <?php echo !$has_email ? 'disabled' : '';?>><i class="glyphicon glyphicon-send"></i> Send</button>
        </div>
        <div class="col-sm-9">
            <?php
            $warning = '<div class="alert alert-danger warning-msg" role="alert"><strong>Warning!</strong>This person has no email address.</div>';
            echo !$has_email ? $warning : '';
            ?>
        </div>
    </div>
    <div class="content">
        <?php
        $date = $this->uri->segment(3);
        $name = '';
        $start_wage = date('d/m/Y',strtotime('April '.date('Y',strtotime($date)).' Tuesday '));

        if(count($staff)>0):
            foreach($staff as $v):
                $name = $v->name;
                $total = @$total_paid[$v->id][$date];
                $total_account_one = @$total['account_one'] ? $v->converted_amount * $total['account_one'] : 0;
                $total_account_two = @$total['account_two'] ? $v->converted_amount * $total['account_two'] : 0;
                ?>
                <table class="print-table">
                    <thead>
                    <tr>
                        <th colspan="4" style="text-transform: uppercase;">
                            SUBBIE SOLUTIONS LTD.
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" style="text-transform: uppercase;">
                            <?php echo 'PROJECT: '.$v->company?>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="4" style="padding: 10px;border: 1px solid #000000">Pay Advice Slip for the Pay Period Ended: <?php echo date('j F Y',strtotime('+5 days '.$date));?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="border: 1px solid #000000">
                        <td class="bold-text">
                            Name: <span><?php echo $v->name;?></span>
                        </td>
                        <td>
                            Tax Number: <span><?php echo $v->tax_number;?></span>
                        </td>
                        <td>
                            Working: <span><?php echo $v->wage_type != 1 ? $v->working_hours : 40;?></span><br/>
                            Non-Working: <span><?php echo @$v->non_working_hour ? @$v->non_working_hour : '00.0'?></span>
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
                        }else{
                            ?>
                            <td class="bold-text" colspan="2">Position: <?php echo $v->position;?></td>
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
                    }
                    else{
                        ?>
                        <tr>
                            <td class="bold-text" style="text-align: center">This Pay</td>
                            <td class="bold-text" style="text-align: center">Year to date<br/> (<?php echo $start_wage;?>)</td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr style="vertical-align: top">
                        <td >Wage Gross: <span><?php echo $v->gross ? '$'.number_format($v->gross,2) : '';?></span></td>
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
                                        <td>Tax</td>
                                        <td><?php echo $v->tax;?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo @$total_bal[$date][$v->id]['flight_debt'] ? '$'.@$total_bal[$date][$v->id]['flight_debt'] : '&nbsp;';?></td>
                                        <td>Flight</td>
                                        <td><?php echo $v->total_flight;?></td>
                                    </tr>
                                    <tr>
                                        <td><?php echo @$total_bal[$date][$v->id]['visa_debt'] ? '$'.@$total_bal[$date][$v->id]['visa_debt'] : '&nbsp;';?></td>
                                        <td>Visa</td>
                                        <td><?php echo $v->total_visa;?></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Accom</td>
                                        <td><?php echo $v->total_accom != '' ? number_format($v->total_accom,2,'.',',') : '&nbsp;';?></td>
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
                                        <td>Tax</td>
                                        <td><?php echo $v->tax;?></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>Student Loan</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                        <td>Kiwi Saver</td>
                                        <td><?php echo $v->kiwi_ ? $v->kiwi_ : '&nbsp;';?></td>
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
                                    <td style="border-top: 1px solid #000000"><strong><?php echo $v->total;?></strong></td>
                                </tr>
                            </table>
                        </td>
                        <td style="padding-left: 20px">
                            <table class="deduction-table">
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><?php echo !$v->nz_account ? ($v->distribution ? '$ '.number_format($v->distribution,2) : '') : '';?></td>
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
                                        <td style="text-align: center"><?php echo $v->star_balance;?></td>
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
                                    <td><?php echo !$v->nz_account ? (@$total['distribution'] ? '$ '.number_format($total['distribution'],2) : '') : '';?></td>
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
                                            echo @$total_bal[$date][$v->id]['balance'] != 0 ? number_format(@$total_bal[$date][$v->id]['balance'],2,'.',',') : '&nbsp;';?>
                                        </td>
                                    </tr>
                                <?php
                                }else{
                                    ?>
                                    <tr>
                                        <td style="border-top: 1px solid #000000;"><strong><?php echo @$total['distribution'] ? '$ '.number_format($total['distribution'],2) : '';?></strong></td>
                                    </tr>
                                <?php
                                }
                                ?>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: left;">Subtotal/NETT Pay: <span><?php echo $v->distribution ? '$ '.number_format($v->distribution,2) : '';?></span></td>
                    </tr>
                    <tr>
                        <td style="text-align: center;">
                            <strong>Distribution</strong>
                        </td>
                        <td style="text-align: center;">
                            <?php echo $v->nz_account ? 'PHP One(self)' : '&nbsp;'?>
                        </td>
                        <td style="text-align: center;">
                            <?php echo $v->nz_account ? 'PHP Two(wife)' : '&nbsp;'?>
                        </td>
                        <td style="text-align: center;">
                            <?php echo $v->nz_account ? 'NZ ACC' : '&nbsp;'?>
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
                                            <strong><?php echo $v->distribution ? '$'.number_format($v->distribution,2) : '';?></strong>
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
                                            <span><?php echo $v->nz_account ? $v->account_one : '&nbsp;';?></span><br/>
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
                                            <span><?php echo $v->total_account_two ? '$'.number_format($v->total_account_two,2,'.',',') : '&nbsp;';?></span><br/>
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
<script>
    $(function(){
        $('.send-payslip').click(function(){
            $(this).modifiedModal({
                url: bu + 'sendStaffPaySlip<?php echo '/'.$this->uri->segment(2).'/'.$this->uri->segment(3)?>',
                title: 'Send Pay Slip',
                type: 'small'
            });
        });
        jQuery(window).load(function () {
            $(this).newForm.removeLoadingForm();
        });
    });
</script>