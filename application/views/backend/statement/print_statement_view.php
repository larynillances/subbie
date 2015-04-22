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
            .success{
                background-color: #dff0d8!important;
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
            .grey-background{
                background: #a8a8a8;
            }
            .font-bold{
                font-weight: bold;
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
                            <?php echo $invoice_info.'<br/>';?>
                            Date: <strong><?php echo $_GET['date'];?></strong>
                        </td>
                    </tr>
                </table>
                <table class="table table-invoice">
                    <thead>
                    <tr>
                        <th colspan="5" class="clear-style" style="text-align: center!important;">
                            <span style="padding: 5px 20px;" class="grey-background">STATEMENT</span>
                        </th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th style="width: 15%">Date</th>
                        <th>Reference</th>
                        <th style="width: 15%">Debits</th>
                        <th style="width: 15%">Credits</th>
                        <th style="width: 15%">Balance</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $maxLen = 30;
                    if(count($statement_data) >0):
                        foreach($statement_data as $k=>$value):
                        $maxLen = $maxLen - count($value);
                        ?>
                        <tr>
                            <td rowspan="<?php echo count($value);?>" style="vertical-align: top;"><?php echo $k;?></td>
                            <?php
                            $ref = 0;
                            if(count($value) >0):
                                foreach($value as $v):
                                    echo $ref != 0 ? '<tr>' : '';
                                    ?>
                                    <td style="text-align: left;"><?php echo $v->type != 'opening' ? $v->type.' '.$v->reference : $v->reference;?></td>
                                    <td><?php echo $v->debits;?></td>
                                    <td><?php echo $v->credits;?></td>
                                    <td class="success"><?php echo $v->balance;?></td>
                                    <?php
                                    echo $ref != 0 ? '</tr>' : '';
                                    $ref++;
                                endforeach;
                            endif;
                        endforeach;
                    endif;
                    ?>
                    <?php
                    for($i = 0; $i <= $maxLen; $i++):
                        ?>
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td class="success">&nbsp;</td>
                        </tr>
                    <?php
                    endfor;
                    ?>
                    <tr>
                        <td colspan="5" class="align-left clear-style" style="border-top: 2px solid #000000!important;">
                            <?php echo @$terms_trade;?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </body>
    </html>
<?php
$size = array(0,0,1000,1000);
$html = ob_get_clean();

$domPdf = new DOMPDF();
$domPdf->load_html($html,'UTF-8');
$domPdf->set_paper('A4', "portrait");

$domPdf->render();

// The next call will store the entire PDF as a string in $pdf
$pdf = $domPdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.
$pdfName = $this->uri->segment(3).' Statement '.date('d-F-y',strtotime($_GET['date']));
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