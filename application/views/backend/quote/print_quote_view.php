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
                font-size: 12px;
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
                <div class="row">
                    <table style="width: 100%;">
                        <tr style="vertical-align: top;">
                            <td style="padding-top: 80px;">
                                <?php
                                $quote_num = '';
                                if(count($client_data) > 0):
                                    foreach($client_data as $v):
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
                            <td style="width: 20%;">
                                <img src="<?php echo base_url().'images/subbie-small-logo.png'?>" width="100"><br/>
                                <?php echo $invoice_info;?>
                            </td>
                        </tr>
                    </table><br/><br/>
                    <?php
                    if(count($quotation)>0):
                        foreach($quotation as $v):
                            $address = $v->address ? (object)json_decode($v->address) : array();
                            $this_add = $v->job_name;//$v->address ? $address->number.' '.$address->name.', '.$address->suburb.', '.$address->city : $v->job_address;
                            $quote_num = $v->quote_num;
                            ?>
                            <table style="width: 100%;">
                                <thead>
                                <tr>
                                    <th style="text-align: left;">Date: <?php echo date('d-M-Y')?></th>
                                    <th style="width: 30%;">Quote No.: <?php echo $v->quote_num?></th>
                                </tr>
                                </thead>
                            </table><br/><br/>
                            <div class="row">
                                <div class="col-lg-12" style="font-size: 14px;">
                                    <p>
                                        Thank you for the opportunity to quote for the painting works for the Interior/Exterior at
                                        <strong class="input-data"><?php echo $this_add?></strong>.
                                        We submit our quotation for the above contract as per plans specifications provided for the sum
                                        of $ <strong class="input-data"><?php echo number_format($v->price,2,'.',',');?></strong> plus GST $ <strong class="input-data"><?php echo number_format($v->gst,2,'.',',')?></strong>.<br/><br/><br/>

                                        Note:<br/>
                                        Please note the following items:<br/><br/>
                                        Scaffolding/and/or/scissors lift provided by Main Contractor.<br/>
                                        <strong class="input-data">
                                            <?php
                                            echo str_replace("\n","<br/>",$v->tags);
                                            ?>
                                        </strong><br/><br/>
                                        This quotation holds good for 90 days from the above date.<br/><br/><br/>

                                        Payment terms:<br/>
                                        All contracts to be paid in full on the 20th of the month following date of invoice. Extras to contract will
                                        be charge at $50 per hour per man, materials will be charge at $25 per litre.<br/><br/><br/>

                                        Yours faithfully,<br/><br/><br/>
                                        Operation Manager<br/>
                                    </p>
                                    <table style="width: 100%">
                                        <tr>
                                            <td style="border-bottom: 1px solid #000000">&nbsp;</td>
                                        </tr>
                                    </table><br/><br/>
                                    <p>
                                        We accept the quotation from the Subbie Solutions Ltd for the painting works at <strong class="input-data"><?php echo $this_add?></strong>.
                                        For the sum of $ <strong class="input-data"><?php echo number_format($v->price,2,'.',',')?></strong> plus GST $ <strong class="input-data"><?php echo number_format($v->gst,2,'.',',')?></strong>.<br/><br/>

                                    </p>
                                    <table style="width: 100%">
                                        <tr>
                                            <td style="width: 5%;">Signed:</td>
                                            <td style="width: 20%;border-bottom: 1px solid #000000">&nbsp;</td>
                                            <td style="">&nbsp;</td>
                                            <td style="width: 5%">Date:</td>
                                            <td style="width: 15%;border-bottom: 1px solid #000000">&nbsp;</td>
                                        </tr>
                                    </table>
                                </div>
                            </div><br/><br/><br/>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </div>
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
$pdfName = $quote_num.'-'.date('d F Y');
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