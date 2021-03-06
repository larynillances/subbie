<?php
require_once(realpath(APPPATH ."../plugins/dompdf/dompdf_config.inc.php"));
ini_set("upload_max_filesize","1024M");
ini_set("memory_limit","1024M");
ini_set('post_max_size', '1024M');
ini_set('max_input_time', 900000000);
ini_set('max_execution_time', 900000000);
set_time_limit(900000000);
ob_start();
?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style>
            body{
                font-family: helvetica, sans-serif;
                font-size: 13px;
            }
            .print-table{
                margin: 0 auto;
                border-collapse: collapse;
                width: 100%;
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
            }.inner-table{
                 border-collapse: collapse;
                 width: 100%;
                 font-size: 11px!important;
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
            .deduction-table tr td:last-child{
                text-align: right;
            }
        </style>
    </head>

    <body>
    <script type='text/php'>
          if ( isset($pdf) ) {
            $font = Font_Metrics::get_font('helvetica', 'normal');
            $size = 9;
            $y = $pdf->get_height() - 24;
            $x = $pdf->get_width() - 60 - Font_Metrics::get_text_width('1/1', $font, $size);
            $pdf->page_text($x, $y, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, $size);

            $date = date("d/m/Y");
            $y = $pdf->get_height() - 24;
            $x = $pdf->get_width() - 550 - Font_Metrics::get_text_width('1/1', $font, $size);
            $pdf->page_text($x, $y, 'Printed : '. $date, $font, 8, array(0, 0, 0));
          }
        </script>
    <div id="wrap">
        <div id="content">
            <div class="content">
                <?php
                $date = @$pay_period_date ? @$pay_period_date : $this->uri->segment(3);
                $name = '';
                $_date = new DateTime($date);
                $week = $_date->format("W");
                $_year = $_date->format("Y");
                $start_wage = date('d/m/Y',strtotime('April '.date('Y',strtotime($date)).' Tuesday '));
                if(count($staff)>0):
                    foreach($staff as $v):
                        $pay_data = @$last_pay[$v->id];
                        $name = $v->name;
                        $total = @$total_paid[$v->id][$date];
                        $balance_data = @$total_bal[$v->id][$_year][$week];
                        $total_account_one = @$total['account_one'] ? $v->converted_amount * $total['account_one'] : 0;
                        $total_account_two = @$total['account_two'] ? $v->converted_amount * $total['account_two'] : 0;
                        $v->distribution = ($pay_data['annual_leave_pay'] - $pay_data['annual_tax']) + $v->distribution;
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
                                <th colspan="4" style="padding: 10px;border: 1px solid #000000">Final Pay Advice Slip for the Pay Period Ended:
                                    <?php
                                    echo date('j F Y',strtotime('+6 days '.$pay_data['last_date_pay']));
                                    echo '&nbsp;[Week '.$week.']';
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
                                            <td style="padding-left: 5px;"><span><?php echo @$v->non_working_hour ? @$v->non_working_hour : '00.0'?></span></td>
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
                                }else{
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
                                    <table style="font-size: 12px;border-collapse: collapse;">
                                        <tr>
                                            <td style="text-align: right;white-space: nowrap">Wage Gross:</td>
                                            <td style="padding-left: 5px;"><span><?php echo $v->gross ? '$'.number_format($v->gross,2) : '';?></span></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;white-space: nowrap">Annual Leave Pay:</td>
                                            <td style="padding-left: 5px;"><span><?php echo $pay_data['annual_leave_pay'] ? '$'.number_format($pay_data['annual_leave_pay'],2) : '$0.00';?></span></td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right">Annual Leave PAYE:</td>
                                            <td style="padding-left: 5px;"><span><?php echo $pay_data['annual_tax'] ? '$'.number_format($pay_data['annual_tax'],2) : '$0.00';?></span></td>
                                        </tr>
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
                                                <td><?php echo number_format($v->tax,2);?></td>
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
                                        }else{
                                            ?>
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>PAYE</td>
                                                <td><?php echo $v->tax ? number_format($v->tax,2,'.','') : '0.00';?></td>
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
                                    <strong><?php echo $v->nz_account ? 'PHP Two(wife)' : 'Holiday <span style="color: #ff0000">(' .$total_holiday_leave.')</span>'?></strong>
                                </td>
                                <td style="text-align: center;">
                                    <strong><?php echo $v->nz_account ? 'NZ ACC' : 'Sick Leave <span style="color: #ff0000">(' .$total_sick_leave.')</span>'?></strong>
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
                                    }else{
                                        ?>
                                        <table class="inner-table">
                                            <thead>
                                            <tr>
                                                <th style="width: 50%;">Remaining</th>
                                                <th>Taken</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td style="padding-right: 10px;"><?php echo $pay_data['total_holiday_leave']?></td>
                                                <td style="padding-right: 10px;"><?php echo $pay_data['holiday_leave_taken']?></td>
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
                                    }else{
                                        ?>
                                        <table class="inner-table">
                                            <thead>
                                            <tr>
                                                <th style="width: 50%;">Remaining</th>
                                                <th>Taken</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td style="padding-right: 10px;"><?php echo $pay_data['total_sick_leave']?></td>
                                                <td style="padding-right: 10px;"><?php echo $pay_data['sick_leave_taken']?></td>
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
    </div>
    </body>
    </html>
<?php
//$size = array(0,0,500,500);
$html = ob_get_clean();

$domPdf = new DOMPDF();
$domPdf->load_html($html,'UTF-8');
$domPdf->set_paper('A4', "portrait");

$domPdf->render();

// The next call will store the entire PDF as a string in $pdf
$pdf = $domPdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.
$pdfName = $file_name;
if(!$is_download){
    @$domPdf->stream($pdfName.".pdf", array("Attachment" => 0));
    $file_to_save = $dir.'/'.$pdfName.'.pdf';
    //save the pdf file on the server
    file_put_contents($file_to_save, $pdf);
}else{
    $file_to_save = $dir.'/'.$pdfName.'.pdf';
    //save the pdf file on the server
    file_put_contents($file_to_save, $pdf);
}

?>