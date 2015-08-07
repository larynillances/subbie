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
            font-size: 11px;
        }
        .row{
            margin: -5px;
        }
        .inner-table-class{
            border-collapse: collapse;
            font-size: 11px;
            width: 100%;
        }
        .inner-table-class tr td{
            padding: 5px;
        }
        .inner-table-class tr td:nth-child(odd){
            width: 9%;
            font-weight: bold;
            text-align: right;
        }
        .inner-table-class tr td:nth-child(even){
            color: #0000ff;
        }
        .inner-table-class tr td:nth-child(2),
        .inner-table-class tr td:nth-child(4),
        .inner-table-class tr td:nth-child(6){
            width: 12%;
            white-space: nowrap;
        }
        .inner-table-class tr td:nth-child(3),
        .inner-table-class tr td:nth-child(5){
            width: 5%;
            white-space: nowrap
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
        .inner-table-class tr td.text-right{
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

        $date = date("d/m/Y g:i:s a");
        $y = $pdf->get_height() - 24;
        $x = $pdf->get_width() - 800 - Font_Metrics::get_text_width('1/1', $font, $size);
        $pdf->page_text($x, $y, 'Printed : '. $date, $font, 8, array(0, 0, 0));
      }
    </script>
    <div class="row">
        <div style="color: red;position: absolute;">
            <?php
            if(!@$pay_period->is_locked){
                echo 'Provisional-not committed';
            }
            ?>
        </div>
        <div style="position: absolute;margin: -15px 940px 0;">
            <img src="<?php echo base_url('images/subbie-small-logo.png')?>" width="100">
        </div>
        <div class="col-sm-12">
            <table style="width: 100%">
                <thead>
                <tr>
                    <th style="text-align: center;">
                        <h3><?php echo $page_name;?></h3>
                    </th>
                </tr>
                </thead>
            </table>
        </div><br/>
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
            $total_accommodation = 0;
            $total_esct = 0;
            $ref = 0;
            if(count($this_data) >0) {
                foreach ($this_data as $val) {
                    if($val['hours'] > 0){
                        $total_nett += $val['nett'];
                        $total_dist += $val['distribution'];
                        $total_gross += $val['gross'];
                        $total_paye += $val['tax'];
                        $total_st_loan += $val['st_loan'];
                        $total_kiwi += $val['kiwi'];
                        $total_emp_kiwi += $val['cec'];
                        $total_esct += $val['esct'];
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
                            <tr>
                                <td>Hours Worked:</td>
                                <td><?php echo number_format($val['hours'],2)?></td>
                                <td>Gross Pay:</td>
                                <td><?php echo $val['gross'] ? '$ '.number_format($val['gross'],2) : '$ 0.00'?></td>
                                <td>PAYE:</td>
                                <td><?php echo $val['tax'] ? '$ '.number_format($val['tax'],2) : '$ 0.00'?></td>
                                <td>Nett Pay:</td>
                                <td><strong><?php echo $val['nett'] > 0 ? '$ '.number_format($val['nett'],2) : '$ 0.00'?></strong></td>
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
                                <td colspan="5"><?php echo $val['hours'] > 0 ? '$ '.number_format($val['transport'],2) : '$ 0.00';?></td>
                            </tr>
                            <tr>
                                <td>Holiday Taken:</td>
                                <td><?php echo $val['total_holiday_leave'].' ('.$val['total_holiday_leave'].')'?></td>
                                <td>Holiday Remaining:</td>
                                <td><?php echo $val['total_holiday_leave'].' ('.$val['total_holiday_leave'].')'?></td>
                                <td>ACC Levy:</td>
                                <td>&nbsp;</td>
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
                            if($val['has_nz_account']){
                                ?>
                                <tr>
                                    <td>Sick Leave Taken:</td>
                                    <td><?php echo $val['total_sick_leave'].' ('.$val['total_sick_leave'].')'?></td>
                                    <td>Sick Leave Remaining:</td>
                                    <td colspan="5"><?php echo $val['total_sick_leave'].' ('.$val['total_sick_leave'].')'?></td>
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
                            }else{
                                ?>
                                <tr>
                                    <td>Sick Leave Taken:</td>
                                    <td><?php echo $val['total_sick_leave'].' ('.$val['total_sick_leave'].')'?></td>
                                    <td>Sick Leave Remaining:</td>
                                    <td colspan="3"><?php echo $val['total_sick_leave'].' ('.$val['total_sick_leave'].')'?></td>
                                    <td>To Bank:</td>
                                    <td style="background: #b2b2b2;color: #000000"><strong><?php echo '$ '.number_format($val['distribution'],2);?></strong></td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                }
            }
            ?>
            <div class="content-div">
                <table class="inner-table-class">
                    <tr>
                        <td colspan="8" style="text-align: left;"><strong>TOTALS:</strong></td>
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
</body>
</html>
<?php
$html = ob_get_clean();

$domPdf = new DOMPDF();

$domPdf->load_html($html,'UTF-8');
$domPdf->set_paper('A4', "landscape");

$domPdf->render();

// The next call will store the entire PDF as a string in $pdf
$pdf = $domPdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.
$pdfName = $pdf_name;
@ $domPdf->stream($pdfName.".pdf", array("Attachment" => 0));

$file_to_save = $dir.'/'.$pdfName.'.pdf';
//save the pdf file on the server
file_put_contents($file_to_save, $pdf);
?>