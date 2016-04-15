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
            .table {
                width: 100%;
                border-collapse: collapse !important;
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
                padding: 5px;
            }
            .table-invoice > tbody > tr > td:last-child{
                border-right: 2px solid #000000;
            }
            .table-order tbody tr:last-child td{
                border-bottom: 2px solid #000000;
            }
            .table-order tbody tr td.tbl-footer{
                text-align: right;
                font-weight: bold;
            }
            .table-order tbody tr.tr-footer td{
                border-top: 2px solid #000000;
            }
        </style>
    </head>

    <body>
    <div id="wrap">
        <div id="content">
            <script type="text/php">
                if ( isset($pdf) ) {
                $font = Font_Metrics::get_font("verdana");;
                $size = 6;
                $color = array(0,0,0);
                $text_height = Font_Metrics::get_font_height($font, $size);

                $foot = $pdf->open_object();

                $w = $pdf->get_width();
                $h = $pdf->get_height();

                // Draw a line along the bottom
                $y = $h - $text_height - 24;
                $pdf->line(16, $y, $w - 16, $y, $color, 0.5);

                $pdf->close_object();
                $pdf->add_object($foot, "all");

                $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
                // Center the text
                $width = Font_Metrics::get_text_width("Page 1 of 2", $font, $size);
                $pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);
                }
            </script>

            <div class="content">
                <table style="width: 100%;">
                    <tr style="vertical-align: top;">
                        <td style="padding-top: 80px;">
                            <?php
                            if(count($supplier) >0):
                                foreach($supplier as $v):
                                    ?>
                                    <strong><?php echo $v->supplier_name;;?></strong><br/>
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
                        <td style="width: 20%;">
                            <img src="<?php echo base_url().'images/subbie-small-logo.png'?>" width="100"><br/>
                            <?php echo $invoice_info;?>
                        </td>
                    </tr>
                </table><br/><br/>
                <table style="width: 100%;">
                    <thead>
                    <tr>
                        <th style="text-align: left;">Date: <?php echo date('d-M-Y')?></th>
                        <th style="text-align: left">Job No.: <?php echo $job_num;?></th>
                        <th style="text-align: left;width: 26%;">Order No.: <?php echo $order_num;?></th>
                    </tr>
                    </thead>
                </table><br/>
                <table class="table table-invoice table-order">
                    <thead>
                    <tr style="border: 2px solid #000000">
                        <th style="width: 20%">Quantity</th>
                        <th style="width: 60%">Description</th>
                        <th>Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $subtotal = 0;
                    $uri = $this->uri->segment(3);
                    $disable = count($order_list) == 0 || $uri ? 'disabled' : '';
                    if(count($order_list) >0):
                        foreach($order_list as $ov):
                            $subtotal += $ov->price;
                            ?>
                            <tr>
                                <td><?php echo $ov->quantity;?></td>
                                <td><?php echo $ov->product_name;?></td>
                                <td><?php echo $ov->price;?></td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    $maxLen = 24 - count($order_list);
                    for($i = 0; $i < $maxLen; $i++):
                        ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    <?php
                    endfor;
                    ?>
                    <tr class="tr-footer">
                        <td colspan="2" class="tbl-footer">Sub Total</td>
                        <td><?php echo '$ '.number_format($subtotal,2,'.',',');?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="tbl-footer">GST @ 15 %</td>
                        <td><?php echo '$ '.number_format($subtotal * 0.15,2,'.',',');?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="tbl-footer">Total</td>
                        <td><?php echo '$ '.number_format(($subtotal * 0.15) + $subtotal,2,'.',',');?></td>
                    </tr>
                </table>
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

/*$file_to_save = $dir.'/'.$pdfName.'.pdf';
//save the pdf file on the server
file_put_contents($file_to_save, $domPdf->output());
//print the pdf file to the screen for saving
header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="'.$pdfName.'.pdf"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file_to_save));
header('Accept-Ranges: bytes');
readfile($file_to_save);*/
?>