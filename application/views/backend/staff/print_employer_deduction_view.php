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
        .table{
            font-size: 12px;
            border-collapse: collapse;
        }
        table{
            border-collapse: collapse;
        }
        .table > tbody > tr > td{
            background: #e0e0e0;
            padding: 3px;
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
    $name_address = '';
    if(count($info_array) >0){
        foreach($info_array as $inv_data){
            $gst_num = $inv_data->gst_num;
            $name_address = str_replace("\n","<br/>",$inv_data->info_text);
        }
    }
    $gross_total = 0;
    $tax_total = 0;
    $st_loan_total = 0;
    $kiwi_total = 0;
    $emp_kiwi_total = 0;
    $esct_total = 0;
    if(count($staff) > 0){
        foreach($staff as $sv){
            $staff_wage = @$monthly_pay[$sv->id];
            if($staff_wage['hours'] != 0){
                $gross_total += $staff_wage['gross'];
                $tax_total += $staff_wage['tax'];
                $st_loan_total += $staff_wage['st_loan'];
                $kiwi_total += $staff_wage['kiwi'];
                $emp_kiwi_total += $staff_wage['emp_kiwi'];
                $esct_total += $staff_wage['esct'];
            }
        }
    }
    ?>
    <table class="table" style="width: 100%;">
        <thead>
        <tr>
            <th style="width: 50%;vertical-align: top;border-bottom: 1px solid #000000;text-align: left;">
                <img src="<?php echo base_url().'images/ird-logo.png'?>"><br/>
                <h3>Employer deductions</h3>
            </th>
            <th style="border-bottom: 1px solid #000000;text-align: right;vertical-align: bottom;"><h4><?php echo date('F Y',strtotime($date));?></h4></th>
        </tr>
        <tr>
            <th style="border-bottom: 1px solid #000000;text-align: left;vertical-align: bottom;padding-bottom: 10px;">
                <label>Name and Address</label>
                <div class="lg-info-content" style="width: 60%;">
                    <?php echo $name_address;?>
                </div>
            </th>
            <th style="border-bottom: 1px solid #000000">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="text-align: right;">
                            <p style="font-size: 10px;">Please see notes on the back to<br/>
                                help you complete this form and<br/>
                                the EMS schedule.<br/><br/>
                                Formore information:<br/>
                                website <strong>www.ird.govt.nz</strong><br/>
                                telephone 0800 377 772<br/>
                                <strong>INFOexpress 0800 257 773</strong></p>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right;">
                            <table style="width: 100%;">
                                <thead>
                                <tr>
                                    <th style="text-align: right;padding-right: 3px;">Employer’s<br/>IRD number</th>
                                    <th style="width: 8%;">
                                        <img src="<?php echo base_url().'images/arrow-1.png'?>">
                                    </th>
                                    <th style="width: 40%;">
                                        <div class="info-content">
                                            <?php echo $gst_num?>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th style="text-align: right;padding-right: 3px;">Period ended</th>
                                    <th style="width: 8%;">
                                        <img src="<?php echo base_url().'images/arrow-2.png'?>">
                                    </th>
                                    <th style="width: 40%;">
                                        <div class="info-content">
                                            <?php echo date('t/m/Y',strtotime($date));?>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </th>
        </tr>
        </thead>
        <tbody style="border:1px solid #000000!important;">
        <?php
        $total = 0;
        $over_all_total = 0;
        if(count($monthly_pay) >0){
            foreach($monthly_pay as $k=>$pay){
                $this_pay = $monthly_pay[$k];
                if($this_pay['hours'] != 0){
                    $total += @$this_pay['tax'];
                }
            }
        }
        $over_all_total = $total + $esct_total + $st_loan_total + $emp_kiwi_total + $kiwi_total;
        ?>
        <tr>
            <td>
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td>If your postal address is different from that printed<br/>above, please enter your new address below.</td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="width: 30%;">PAYE (incl. tax on schedular payments)</td>
                        <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-3-right.png'?>"></td>
                        <td style="font-size: 13px;width: 5%;">$</td>
                        <td><div class="info-content"><?php echo number_format($total,2)?></div></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <div class="lg-info-content" style="height: 50px;width: 60%;">&nbsp;</div>
                <!--<textarea class="form-control input-sm" style="width: 50%;" placeholder="Street or PO Box"></textarea>-->
            </td>
            <td>
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="width: 30%;">Child support deductions</td>
                        <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-4-right.png'?>"></td>
                        <td style="font-size: 13px;width: 5%;">$</td>
                        <td><div class="info-content"><?php echo 'nil'?></div></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <div class="info-content" style="width: 60%;">&nbsp;</div>
                <!--<input type="text" class="form-control input-sm" style="width: 50%;" placeholder="Suburb">-->
            </td>
            <td>
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="width: 30%;">Student loan deductions</td>
                        <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-5-right.png'?>"></td>
                        <td style="font-size: 13px;width: 5%;">$</td>
                        <td><div class="info-content"><?php echo number_format($st_loan_total,2)?></div></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <div class="info-content" style="width: 60%;">&nbsp;</div>
                <!--<input type="text" class="form-control input-sm" style="width: 50%;" placeholder="Town or City">-->
            </td>
            <td>
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="width: 30%;">KiwiSaver deductions</td>
                        <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-6-right.png'?>"></td>
                        <td style="font-size: 13px;width: 5%;">$</td>
                        <td><div class="info-content"><?php echo number_format($kiwi_total,2)?></div></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td rowspan="2">
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td>
                            <table style="width: 20%;" class="indicator-table">
                                <thead>
                                <tr>
                                    <th colspan="4"><img src="<?php echo base_url().'images/right-arrow-background.png'?>">OFFICE USE ONLY</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Operator<br/>Code</td>
                                    <td><input type="checkbox" name="checkbox"></td>
                                    <td>Corresp.<br/>indicator</td>
                                    <td><input type="checkbox" name="checkbox"></td>
                                </tr>
                                <tr>
                                    <td>Payment<br/>attached</td>
                                    <td><input type="checkbox" name="checkbox"></td>
                                    <td>Return<br/>cat.</td>
                                    <td><input type="checkbox" name="checkbox"></td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;font-style: italic;">Declaration I declare that the information given in this return is true and correct.</td>
                    </tr>
                    <tr>
                        <td style="width: 30%;background: #FFFFFF;"><span style="font-size: 10px;">Signature</span></td>
                    </tr>
                    <tr style="background: #FFFFFF;">
                        <td style="text-align: right;padding-right: 20%;">
                            <strong><?php echo date('d/m/Y');?><br/>Date</strong>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <table style="width: 100%">
                    <tbody>
                    <tr>
                        <td style="width: 30%;">Net KiwiSaver employer contributions</td>
                        <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-7-right.png'?>"></td>
                        <td style="font-size: 13px;width: 5%;">$</td>
                        <td><div class="info-content"><?php echo number_format($emp_kiwi_total,2)?></div></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td style="width: 30%;">ESCT deductions</td>
                        <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-8-right.png'?>"></td>
                        <td style="font-size: 13px;width: 5%;">$</td>
                        <td><div class="info-content"><?php echo number_format($esct_total,2)?></div></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #000000;">
                        <td style="width: 30%;font-weight: bold;">Add Boxes 3, 4, 5, 6, 7 and 8.This is the amount you need to pay</td>
                        <td style="width: 7%;"><img src="<?php echo base_url().'images/black-arrow-9-right.png'?>"></td>
                        <td style="font-size: 13px;width: 5%;">$</td>
                        <td><div class="info-content"><?php echo number_format($over_all_total,2)?></div></td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid #000000!important;">
                <span style="font-weight: bold;font-size: 17px;">Inland Revenue copy</span><br/>Please make a copy for your own records
            </td>
            <td style="border-bottom: 1px solid #000000!important;">
                <table style="width: 100%;">
                    <tbody>
                    <tr>
                        <td>Has payment been made electronically?</td>
                        <td style="width: 20%;">(Tick one) </td>
                        <td style="width: 12%;">
                            <table>
                                <tbody>
                                <tr>
                                    <?php
                                    $yes = $_GET['payment'] == 1 ? 'background: #656a6c;' : 'background: #f5f5f5;';
                                    $no = $_GET['payment'] == 0 ? 'background: #656a6c;' : 'background: #f5f5f5;';
                                    ?>
                                    <td><div style="<?php echo $yes;?>width: 10px;border:1px solid #000000;">&nbsp;</div></td>
                                    <td>Yes</td>
                                    <td><div style="<?php echo $no;?>width: 10px;border: 1px solid #000000">&nbsp;</div></td>
                                    <td>No</td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table style="width: 100%;">
                    <thead>
                    <tr>
                        <th><img src="<?php echo base_url().'images/ird-logo.png'?>"></th>
                        <th><span style="font-weight: bold;font-size: 17px;">Payment slip</span></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="2"><strong>Name and Address</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="lg-info-content" style="width: 50%;background: #ffffff">
                                <?php echo $name_address;?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><br/><br/><strong>This return and any payment are due</strong></td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td>
                <table style="width: 100%">
                    <thead>
                    <tr>
                        <th style="text-align: right;padding-right: 20px;height: 50px;" colspan="4">DED</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="3">IRD Number</td>
                        <td><div class="info-content"><?php echo $gst_num?></div></td>
                    </tr>
                    <tr>
                        <td colspan="3">Period ended</td>
                        <td><div class="info-content"><?php echo date('t/m/Y',strtotime($date))?></div></td>
                    </tr>
                    <tr>
                        <td style="width: 15%;">Amount of payment</td>
                        <td style="width: 5%;"><img src="<?php echo base_url().'images/black-arrow-10-right.png'?>"></td>
                        <td style="font-size: 17px;width: 5%;">$</td>
                        <td style="width: 60%;"><div class="info-content"><?php echo number_format($over_all_total,2)?></div></td>

                    </tr>
                    <tr>
                        <td colspan="4"><br/><br/><strong>Copy your total from Box 9 and include any late payment penalties and interest, for this period only.</strong></td>
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
/*$size = array(0,0,1000,1000);*/
$html = ob_get_clean();

$domPdf = new DOMPDF();

$domPdf->load_html($html,'UTF-8');
$domPdf->set_paper('A4', "portrait");

$domPdf->render();

// The next call will store the entire PDF as a string in $pdf
$pdf = $domPdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.
$pdfName = 'Deduction '.date('d-F-y');
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