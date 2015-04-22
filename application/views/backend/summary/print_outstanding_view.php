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
            .table-colored-header > thead > tr > th{
                text-align: center;
                font-weight: bold;
                background: #a8a8a8;
                border: 1px solid #000000;
                padding: 5px;
            }
            .table-colored-header > tbody > tr > td{
                text-align: center;
                border: 1px solid #000000;
                padding: 2px;
            }

            .clear-style{
                border: none!important;
                background: none!important;
                text-align: left!important;
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
        <table class="table table-colored-header">
            <thead>
            <tr>
                <th colspan="5" style="background: none;border: none;padding-bottom: 15px;">Outstanding Balance for the Month of <?php echo date('F');?></th>
            </tr>
            <tr>
                <th>Client</th>
                <th style="width: 15%;">Date</th>
                <th style="width: 15%;">NETT</th>
                <th style="width: 15%;">GST</th>
                <th style="width: 15%;">Gross</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_net = 0;
            $total_gst = 0;
            $total_gross = 0;
            if(count($client) >0):
                foreach($client as $key=>$val):
                    $total_net += $val->net;
                    $total_gst += $val->gst;
                    $total_gross += $val->gross;
                    ?>
                    <tr>
                        <td style="text-align: left;"><?php echo $val->name;?></td>
                        <td style="text-align: right;"><?php echo $val->date;?></td>
                        <td style="text-align: right;"><?php echo $val->net != '' ? '$'.number_format($val->net,2) : '$0.00';?></td>
                        <td style="text-align: right;"><?php echo $val->gst != '' ? '$'.number_format($val->gst,2) : '$0.00';?></td>
                        <td style="text-align: right;"><?php echo $val->gross != '' ? '$'.number_format($val->gross,2) : '$0.00';?></td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="5">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            <tr class="danger">
                <td colspan="2" style="text-align: right;"><strong>Total:</strong></td>
                <td style="text-align: right;"><?php echo '$'.number_format($total_net,2)?></td>
                <td style="text-align: right;"><?php echo '$'.number_format($total_gst,2)?></td>
                <td style="text-align: right;"><?php echo '$'.number_format($total_gross,2)?></td>
            </tr>
            </tbody>
        </table>
    </div>
    </body>
    </html>
<?php
$size = array(0,0,1000,1000);
$html = ob_get_clean();

$domPdf = new DOMPDF();

$domPdf->load_html($html,'UTF-8');
$domPdf->set_paper("A4", "portrait");

$domPdf->render();

// The next call will store the entire PDF as a string in $pdf
$pdf = $domPdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.
$pdfName = 'Outstanding for '.date('d-F-y');
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