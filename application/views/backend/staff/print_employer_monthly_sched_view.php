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
        }
        /*table > tbody > tr > td,
        table > thead > tr > th{
            border: 1px solid #000000;
        }*/
        table{
            border-collapse: collapse;
        }
        .table{
            font-size: 10px!important;
        }
        .table > tbody > tr > td{
            padding: 5px;
        }
        .table > tbody.table-deduction > tr > td{
            background: #e0e0e0;
            border: none;
            font-size: 10px;
        }
        .indicator-table{
            width: 100%;
            font-size: 10px;
            font-weight: normal;
            border:1px solid #ffffff;
        }
        .indicator-table > thead > tr > th,
        .indicator-table > tbody > tr > td
        {
            padding: 5px;
        }
        table.table > tbody.content-data > tr > td{
            border: 1px solid #000000;
            background: #e0e0e0;
            font-size: 10px;
        }
        table.table > tbody.content-data > tr > td.footer{
            background: none;
            border: none;
            font-size: 12px;
        }
        table.table > thead > tr > th{
            border-bottom: 1px solid #000000;
            text-align: center;
        }
        .staff-info > tbody > tr > td{
            font-weight: bold;
        }

        .date-table > thead > tr > th,
        .date-table > tbody > tr > td{
            text-align: center;
        }
        .indicator-table{
            width: 100%;
            font-size: 10px;
            font-weight: normal;
            border:1px solid #d2d2d2;
        }
        .indicator-table > thead > tr > th,
        .indicator-table > tbody > tr > td
        {
            padding: 5px;
        }
        .table-total > tbody > tr > td{
            font-size: 13px;
            font-weight: bold;
        }
        .input-text{
            color: #271dff;
            font-weight: bold;
        }
        .info-content{
            border: 1px solid #000000;
            text-align: left;
            padding: 2px;
            background: #FFFFFF;
            font-weight: bold;
        }
        .lg-info-content{
            border:1px solid #000000;
            padding: 5px;
            font-weight: bold;
            background: #FFFFFF;
        }
    </style>
</head>

<body>
    <div id="content">
        <?php
        $date = $thisYear.'-'.$thisMonth.'-01';
        $gst_num = '';
        $employer_name = '';
        $name_address = '';
        $contact_num = '';
        if(count($info_array) >0){
            foreach($info_array as $inv_data){
                $gst_num = $inv_data->gst_num;
                $employer_name = $inv_data->company_name;
                $name_address = $inv_data->info_text;
                $contact_num = $inv_data->contact_num;
            }
        }
        ?>
        <table class="table">
            <thead>
            <tr>
                <th style="text-align:left;border-bottom: 1px solid #000000;">
                    <img src="<?php echo base_url().'images/ird-logo.png'?>">
                </th>
                <th style="width:150px;padding-top: 0;border-bottom: 1px solid #000000;">

                    <p><span style="font-size: 14px!important;">Employer monthly schedule</span>
                    <span style="font-style: italic;font-size: 12px!important;">For help, refer to notes on employer deductions form.</span></p>
                </th>
                <th style="width: 20%;font-size: 12px;vertical-align: bottom;" rowspan="2">
                    <table style="width: 100%;">
                        <thead>
                        <tr>
                            <th>Employer’s<br/>IRD number</th>
                            <th>
                                <img src="<?php echo base_url().'images/arrow-1.png'?>">
                            </th>
                            <th style="width: 60%;text-align: left;"><?php echo $gst_num?></th>
                        </tr>
                        <tr>
                            <th>Period ended</th>
                            <th>
                                <img src="<?php echo base_url().'images/arrow-2.png'?>">
                            </th>
                            <th style="width: 60%;"><div class="info-content"><?php echo date('t/m/Y',strtotime($date));?></div></th>
                        </tr>
                        </thead>
                    </table>
                </th>
            </tr>
            <tr>
                <th colspan="2" style="font-size: 12px;">
                    <table style="width: 100%;">
                        <thead>
                        <tr>
                            <th style="width: 15%;">Employer Name</th>
                            <th style="width: 30%;"><div class="info-content"><?php echo $employer_name?></div></th>
                            <th style="width: 15%;">This schedule is due</th>
                            <th style="width: 20%;"><div class="info-content"><?php echo date('20/m/Y',strtotime('+1 month '.$date));?></div></th>
                        </tr>
                        </thead>
                    </table>
                </th>
            </tr>
            </thead>
            <tbody class="content-data">
            <tr>
                <td colspan="3">
                    <table style="width: 100%;" class="table-info">
                        <thead>
                        <tr>
                            <th width="80">Gross earnings and/or schedular payments</th>
                            <th width="80">Earnings and/or schedular payments not liable for ACC earners’ levy</th>
                            <th width="80">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/white-arrow-3.png'?>"></th>
                                        <th>PAYE and/or schedular tax deductions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th width="80">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/white-arrow-4.png'?>"></th>
                                        <th>Child support deductions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th style="width: 10%;">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/white-arrow-5.png'?>"></th>
                                        <th>Student loan deductions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th style="width: 7%;">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/white-arrow-6.png'?>"></th>
                                        <th>KiwiSaver<br/>deductions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th style="width: 10%;">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/white-arrow-7.png'?>"></th>
                                        <th>Net KiwiSaver<br/>employer<br/>contributions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </td>
            </tr>
            <?php
            $gross_total = 0;
            $tax_total = 0;
            $st_loan_total = 0;
            $kiwi_total = 0;
            $emp_kiwi_total = 0;
            $ref = 0;
            if(count($staff) > 0):
                foreach($staff as $v):
                ?>
                <tr>
                    <td colspan="3">
                        <table style="width: 100%;" class="staff-info">
                            <thead>
                            <?php
                            if($ref == 0):
                                ?>
                                <tr>
                                    <th colspan="6"><strong style="font-size: 12px;">Employee name and IRD number</strong></th>
                                    <th colspan="4">Employment start and/or finish date</th>
                                </tr>
                                <?php
                            endif;
                            ?>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Surname</td>
                                <td>First name(s)</td>
                                <td colspan="3">IRD number</td>
                                <td colspan="4"></td>
                            </tr>
                            <tr class="info">
                                <td style="width: 23%;"><div class="info-content"><?php echo $v->lname?></div></td>
                                <td style="width: 20%;"><div class="info-content"><?php echo $v->fname?></div></td>
                                <td style="width: 10%;"><div class="info-content"><?php echo $v->ird_num ? $v->ird_num :'&nbsp;';?></div></td>
                                <td style="width: 3%;color: #000000;">Tax Code:</td>
                                <td style="width: 7%;"><div class="info-content"><?php echo $v->tax_code ? $v->tax_code :'&nbsp;';?></div></td>
                                <td style="color: #000000;width: 3%;text-align: right;">Start</td>
                                <td style="width: 12%;">
                                    <table style="width: 100%" class="date-table">
                                        <thead>
                                        <tr>
                                            <th style="width: 20%;"><div class="info-content"><?php echo $v->date_employed != '' ? date('d',strtotime($v->date_employed)) : '&nbsp;';?></div></th>
                                            <th style="width: 20%;"><div class="info-content"><?php echo $v->date_employed != '' ? date('m',strtotime($v->date_employed)) : '&nbsp;';?></div></th>
                                            <th><div class="info-content"><?php echo $v->date_employed != '' ? date('Y',strtotime($v->date_employed)) : '&nbsp;';?></div></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 9px!important;">Day</td>
                                            <td style="font-size: 9px!important;">Month</td>
                                            <td style="font-size: 9px!important;">Year</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="width: 3%;color: #000000;text-align: right;">Finished</td>
                                <td style="width: 12%;">
                                    <table style="width: 100%" class="date-table">
                                        <thead>
                                        <tr>
                                            <th><div class="info-content">&nbsp;</div></th>
                                            <th><div class="info-content">&nbsp;</div></th>
                                            <th style="width: 50%;"><div class="info-content">&nbsp;</div></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td style="font-size: 9px!important;">Day</td>
                                            <td style="font-size: 9px!important;">Month</td>
                                            <td style="font-size: 9px!important;">Year</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" style="padding-left: 35%;">
                                    <table style="width: 100%;" class="table-bordered">
                                        <tbody>
                                        <tr>
                                            <td><input type="checkbox" name="checkbox"></td>
                                            <td>Tick if lump sum payment made<br/>and taxed at lowest rate</td>
                                            <td style="width: 10%;padding-right: 5px;"><div class="info-content">&nbsp;</div></td>
                                            <td>CS Code</td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </td>
                            </tr>
                            <tr>
                                <?php
                                $staff_wage = @$monthly_pay[$v->id];
                                if($staff_wage['hours'] != 0):
                                    $gross_total += $staff_wage['gross'];
                                    $tax_total += $staff_wage['tax'];
                                    $st_loan_total += $staff_wage['st_loan'];
                                    $kiwi_total += $staff_wage['kiwi'];
                                    $emp_kiwi_total += $staff_wage['emp_kiwi'];
                                    $gross_ = explode('.',number_format($staff_wage['gross'],2));
                                        ?>
                                        <td colspan="10">
                                            <table style="width: 100%;font-size: 13px;">
                                                <tbody>
                                                <tr>
                                                    <td style="width: 2%;">$</td>
                                                    <td>
                                                        <div class="info-content"><?php echo number_format($staff_wage['gross'])?></div>
                                                    </td>
                                                    <td style="width: 2%">.00</td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td>
                                                        <div class="info-content">nil</div>
                                                    </td>
                                                    <td style="width: 2%">.00</td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td>
                                                        <div class="info-content"><?php echo number_format($staff_wage['tax'],2)?></div>
                                                    </td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td><div class="info-content">nil</div></td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td><div class="info-content"><?php echo number_format($staff_wage['st_loan'],2)?></div></td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td><div class="info-content"><?php echo number_format($staff_wage['kiwi'],2)?></div></td>
                                                    <td style="width: 2%;padding-left: 5px;">$</td>
                                                    <td><div class="info-content"><?php echo number_format($staff_wage['emp_kiwi'],2)?></div></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                        <?php
                                else:
                                ?>
                                    <td colspan="10">
                                        <table style="width: 100%;font-size: 13px;">
                                            <tbody>
                                            <tr>
                                                <td style="width: 2%;">$</td>
                                                <td style="width: 7%;"><div class="info-content">nil</div></td>
                                                <td style="width: 2%;">.00</td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td style="width: 7%;"><div class="info-content">nil</div></td>
                                                <td style="width: 2%;">.00</td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td style="width: 7%;"><div class="info-content">nil</div></td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td style="width: 7%;"><div class="info-content">nil</div></td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td style="width: 7%;"><div class="info-content">nil</div></td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td style="width: 7%;"><div class="info-content">nil</div></td>
                                                <td style="width: 2%;padding-left: 5px;">$</td>
                                                <td style="width: 7%;"><div class="info-content">nil</div></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                <?php
                                endif;
                                ?>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <?php
                    $ref++;
                endforeach;
            endif;
            ?>
            <tr>
                <td colspan="3">
                    <table style="width: 100%;" class="table-total">
                        <thead>
                        <tr>
                            <th colspan="3">Total gross earnings and/ or schedular payments</th>
                            <th colspan="3">Total earnings not liable for ACC earners’ levy</th>
                            <th colspan="2">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-3.png'?>"></th>
                                        <th>Total PAYE and/or schedular tax</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th colspan="2">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-4.png'?>"></th>
                                        <th>Total child support</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th colspan="2">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-5.png'?>"></th>
                                        <th>Total student loan</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th colspan="2">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-6.png'?>"></th>
                                        <th>Total KiwiSaver deductions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                            <th style="width: 13%;" colspan="2">
                                <table style="width: 100%;">
                                    <thead>
                                    <tr>
                                        <th style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-7.png'?>"></th>
                                        <th>NetTotal Net KiwiSaver employer contributions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td style="width: 2%;">$</td>
                            <td><div class="info-content"><?php echo number_format($gross_total)?></div></td>
                            <td style="width: 2%">.00</td>
                            <td style="width: 2%;text-align: right;">$</td>
                            <td><div class="info-content">nil</div></td>
                            <td style="width: 2%">.00</td>
                            <td style="width: 2%;text-align: right;">$</td>
                            <td><div class="info-content"><?php echo number_format($tax_total,2)?></div></td>
                            <td style="width: 2%;text-align: right;">$</td>
                            <td><div class="info-content">nil</div></td>
                            <td style="width: 2%;text-align: right;">$</td>
                            <td><div class="info-content"><?php echo number_format($st_loan_total,2)?></div></td>
                            <td style="width: 2%;text-align: right;">$</td>
                            <td><div class="info-content"><?php echo number_format($kiwi_total,2);?></div></td>
                            <td style="width: 2%;text-align: right;">$</td>
                            <td><div class="info-content"><?php echo number_format($emp_kiwi_total,2);?></div></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="footer" style="vertical-align: top;">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td colspan="3">If your correct daytime phone number is not shown below, print in the box</td>
                        </tr>
                        <tr>
                            <td style="width: 20%;"></td>
                            <td style="width: 5%;"><img src="<?php echo base_url().'images/right-arrow-background.png'?>"></td>
                            <td><div class="info-content" style="width: 250px;"><?php echo $contact_num;?></div></td>
                        </tr>
                        <tr>
                            <td colspan="3">If your correct contact person’s name is not shown below, print in the box</td>
                        </tr>
                        <tr>
                            <td style="width: 20%;"></td>
                            <td style="width: 5%;"><img src="<?php echo base_url().'images/right-arrow-background.png'?>"></td>
                            <td><div class="info-content" style="width: 250px;">&nbsp;</div></td>
                        </tr>
                        </tbody>
                    </table>
                </td>
                <td class="footer" colspan="2" style="padding-left:10px">
                    <table style="width: 100%;">
                        <tbody>
                        <tr>
                            <td><strong>Declaration</strong><br/>
                                <span style="font-style: italic;">I declare that the information given in this return is true and correct.</span>
                            </td>
                            <td rowspan="2" style="text-align: left;width: 20%;font-size: 8px!important;">
                                <table class="indicator-table">
                                    <thead>
                                    <tr>
                                        <th colspan="4"><img src="<?php echo base_url().'images/right-arrow-background.png'?>" style="width: 20px;"> OFFICE USE ONLY</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Operator Code</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                        <td>Corresp. indicator</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                    </tr>
                                    <tr>
                                        <td>Payment attached</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                        <td>Return cat.</td>
                                        <td><input type="checkbox" name="checkbox"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: left;vertical-align: top;"><span style="font-size: 10px;">Signature</span></td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><?php echo date('d/m/Y');?><br/>Date</td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
$size = array(0,0,700,900);
$html = ob_get_clean();

$domPdf = new DOMPDF();

$domPdf->load_html($html,'UTF-8');
$domPdf->set_paper('A4', "landscape");

$domPdf->render();

// The next call will store the entire PDF as a string in $pdf
$pdf = $domPdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.
$pdfName = 'Monthly Schedule of '.date('d-F-y');
@ $domPdf->stream($pdfName.".pdf", array("Attachment" => 0));

$file_to_save = $dir.'/'.$pdfName.'.pdf';
//save the pdf file on the server
/*file_put_contents($file_to_save, $domPdf->output());
//print the pdf file to the screen for saving
header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="'.$pdfName.'.pdf"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file_to_save));
header('Accept-Ranges: bytes');
readfile($file_to_save);*/
?>