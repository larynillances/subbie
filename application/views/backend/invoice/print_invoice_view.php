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
            .header-title{
                background: #484848;
                color: #ffffff;
                padding: 2px;
            }
            .value-class{
                color: #ff0000;
                text-transform: capitalize!important;
            }
            .table{
                border-collapse: collapse;
                width: 100%;
            }
            .table-invoice > thead > tr > th{
                text-align: center;
                font-weight: bold;
                background: #a8a8a8;
                border: 2px solid #000000;
                padding: 5px;
            }
            .table-invoice > tbody > tr > td{
                text-align: center;
                border-left: 2px solid #000000;
                padding: 2px;
                font-size: 11px!important;
            }
            .table-invoice > tbody > tr > td:last-child{
                border-right: 2px solid #000000;
            }
            .table-invoice tbody tr.border-top td{
                border-top: 2px solid #000000!important;
            }
            .clear-style{
                border: none!important;
                background: none!important;
                text-align: left!important;
            }
            .table-invoice tbody tr.border-bottom td{
                border-bottom: 2px solid #000000!important;
            }
            .table-invoice tbody tr.total td{
                border-right: 2px solid #000000!important;
            }
            .align-left{
                text-align: left!important;
            }
            .font-bold{
                font-weight: bold;
            }
        </style>
    </head>

    <body>
    <div id="content">
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
        <table class="table table-invoice">
            <thead>
            <tr>
                <td colspan="3" style="padding-top: 30px;">
                    <?php
                    if(count($client) > 0):
                        foreach($client as $v):
                            ?>
                            <strong><?php echo $v->client_name;?></strong><br/>
                            <strong>
                                <?php
                                echo str_replace("\n","<br/>",$v->address);
                                ?>
                            </strong><br/>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </td>
                <td colspan="3" style="width: 20%;padding-left: 80px;">
                    <img src="<?php echo base_url().'images/subbie-small-logo.png'?>" width="100"><br/>
                    <?php echo @$invoice_info;?>
                </td>
            </tr>
            </thead>
            <thead>
            <tr>
                <th colspan="2" class="clear-style">Date: <?php echo $_GET['date']?></th>
                <th colspan="4" class="clear-style" style="padding-left: 20%">TAX INVOICE: <?php echo @$inv_code?></th>
            </tr>
            </thead>
            <thead>
            <tr>
                <th style="width: 10%">Your Ref</th>
                <th style="width: 12%">Our Ref</th>
                <th>Job Name</th>
                <th style="width: 10%">m&sup2;/hrs</th>
                <th style="width: 10%">Unit Price</th>
                <th style="width: 15%">Total</th>
            </tr>
            </thead>
            <tbody class="data-content">
            <?php
            $subtotal = 0;
            $inv_len = 0;
            if(count(@$invoice) >0){
                foreach(@$invoice as $iv){
                    $address = (object)json_decode($iv->address);
                    $this_add = @$iv->job_id != 0 ? @$address->number.' '.@$address->name.', '.@$address->suburb.', '.@$address->city : '';
                    $inv_len += @count($iv->unit_price_array);
                    ?>
                    <tr>
                        <td style="vertical-align: top;"><?php echo $iv->your_ref;?></td>
                        <td style="vertical-align: top;"><?php echo $iv->job_ref;?></td>
                        <td style="text-align: left;padding-left: 20px!important;"><?php echo $iv->job_name;?></td>
                        <td><?php echo $iv->meter;?></td>
                        <td>
                            <?php
                            if(count($iv->unit_price_array) >0){
                                foreach($iv->unit_price_array as $unit){
                                    $this_unit = @floatval($unit);
                                    echo @$this_unit != 0 ? '$'.number_format(@$this_unit,2).'<br/>' : '<br/>';
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if(count($iv->total) >0){
                                foreach($iv->total as $value){
                                    $subtotal += @floatval($value);
                                    echo $value != 0 ? '$'.number_format(floatval($value),2).'<br/>' : '<br/>';
                                }
                            }
                            ?>
                        </td>
                    </tr>
                <?php
                }
            }

            $maxLen = $inv_len >= 29 ? 40 : 30;

            $len = $maxLen - $inv_len;

            for($i = 0; $i <= $len; $i++){
                ?>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            <?php
            }
            ?>
            <tr class="border-top">
                <td colspan="4" rowspan="4" class="align-left" style="border-bottom: 2px solid #000000;vertical-align: top;"><?php echo $terms_trade;?></td>
                <td class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000">Sub Total</td>
                <td>
                    <?php
                    echo '$ '.number_format($subtotal,2);?>
                </td>
            </tr>
            <tr class="total">
                <td class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000">GST Rate</td>
                <td style="border: none;"><?php echo '15%';?></td>
            </tr>
            <tr class="total">
                <td class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000">GST Total</td>
                <td style="border: none;"><?php echo '$ '.number_format($subtotal * 0.15,2);?></td>
            </tr>
            <tr class="total border-bottom">
                <td class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000">Total</td>
                <td style="border: none; font-weight: bold">
                    <?php
                    $total = $subtotal + ($subtotal * 0.15);
                    echo '$ '.number_format($total,2);
                    ?>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    </body>
    </html>
<?php
//$size = array(0,0,1000,1000);
$html = ob_get_clean();

$domPdf = new DOMPDF();

$domPdf->load_html($html,'UTF-8');
$domPdf->set_paper('A4', "portrait");

$domPdf->render();

// The next call will store the entire PDF as a string in $pdf
$pdf = $domPdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.
$pdfName = $this->uri->segment(4).' '.date('d-F-y',strtotime($_GET['date']));
@ $domPdf->stream($pdfName.".pdf", array("Attachment" => 0));

$file_to_save = $dir.'/'.$pdfName.'.pdf';
//save the pdf file on the server
file_put_contents($file_to_save, $domPdf->output());
//print the pdf file to the screen for saving
/*header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="'.$pdfName.'.pdf"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file_to_save));
header('Accept-Ranges: bytes');
readfile($file_to_save);*/
?>