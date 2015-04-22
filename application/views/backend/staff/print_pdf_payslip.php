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
            }
            .deduction-table tr td:last-child{
                text-align: right;
            }
        </style>
    </head>

    <body>
    <div id="wrap">
        <div id="content">
            <div class="content">
                <?php
                if(count($staff)>0):
                    foreach($staff as $v):
                        $date = $this->uri->segment(3);
                        ?>
                        <table class="print-table">
                            <thead>
                            <tr>
                                <th colspan="4" style="text-transform: uppercase;">
                                    <?php echo $v->company?>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="4" style="padding: 10px;border: 1px solid #000000">Weekly Pay Slip Period End (<?php echo date('j F Y',strtotime($date));?>)</th>
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
                                    Working: <span><?php echo $v->working_hours;?></span><br/>
                                    Non-Working: <span><?php echo $v->non_working_hours?></span>
                                </td>
                                <td>
                                    Hourly Rate: <span><?php echo $v->rate_cost;?></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="bold-text" style="text-align: center" rowspan="2">Income <span></span></td>
                                <td class="bold-text" style="text-align: center" rowspan="2">Deductions</td>
                                <td class="bold-text" style="text-align: center" colspan="2">Loans</td>
                            </tr>
                            <tr>
                                <td class="bold-text" style="text-align: center">Start Bal</td>
                                <td class="bold-text" style="text-align: center">Bal Outs</td>
                            </tr>
                            <tr style="vertical-align: top">
                                <td >Wage Gross: <span><?php echo $v->gross ? '$'.$v->gross : '';?></span></td>
                                <td class="deduction-column">
                                    <table class="deduction-table">
                                        <tr>
                                            <th style="text-align: left">Debt</th>
                                            <th style="text-align: left">Deduction</th>
                                            <th>Amount</th>
                                        </tr>
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
                                        <tr>
                                            <td style="text-align: center"><?php echo $v->star_balance;?></td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="text-align: center">
                                    <table class="deduction-table">
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
                                        <tr>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: center">
                                                <?php
                                                echo @$total_bal[$date][$v->id]['balance'] != 0 ? number_format(@$total_bal[$date][$v->id]['balance'],2,'.',',') : '&nbsp;';?>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align: left;">Subtotal/NETT Pay: <span><?php echo $v->distribution ? '$ '.$v->distribution : '';?></span></td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <strong>Distribution</strong>
                                </td>
                                <td style="text-align: center;">
                                   <?php echo $v->flight ? 'PHP One(self)' : '&nbsp;'?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $v->flight ? 'PHP Two(wife)' : '&nbsp;'?>
                                </td>
                                <td style="text-align: center;">
                                    <?php echo $v->flight ? 'NZ ACC' : '&nbsp;'?>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <strong><?php echo $v->distribution ? '$ '.$v->distribution : '';?></strong>
                                </td>
                                <td style="text-align: center">
                                    <span><?php echo $v->flight ? $v->account_one : '&nbsp;';?></span><br/>
                                    <span style="color: #ff0000;font-weight: bold"><?php echo $v->flight ? $v->account_one_ : '&nbsp;';?></span>
                                </td>
                                <td style="text-align: center">
                                    <span><?php echo $v->total_account_two && $v->flight ? '$ '.number_format($v->total_account_two,2,'.',',') : '&nbsp;';?></span><br/>
                                    <span style="color: #ff0000;font-weight: bold"><?php echo $v->flight ? $v->account_two_ : '&nbsp;';?></span>
                                </td>
                                <td style="text-align: center">
                                    <span><?php echo $v->total_nz_account ? '$ '.number_format($v->total_nz_account,2,'.',',') : '';?></span>
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
$pdfName = $this->uri->segment(2).'_'.date('d-F-y',strtotime($date));
@ $domPdf->stream($pdfName.".pdf", array("Attachment" => 0));

$file_to_save = $dir.'/'.$pdfName.'.pdf';
//save the pdf file on the server
//file_put_contents($file_to_save, $domPdf->output());
//print the pdf file to the screen for saving
/*header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="'.$pdfName.'.pdf"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file_to_save));
header('Accept-Ranges: bytes');
readfile($file_to_save);*/
?>