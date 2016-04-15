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
            .table-colored-header{
                width: 100%;
                border-collapse: collapse;
                font-size: 12px!important;
            }
            .table-colored-header > thead > tr > th{
                /*text-transform: uppercase!important;*/
                padding: 5px;
                font-size: 11px!important;
                background: #484848;
                border: 1px solid #d2d2d2;
                color: #ffffff;
                text-align: center;
                vertical-align: middle;
            }
            .table-colored-header > tbody > tr > td{
                border: 1px solid #d2d2d2;
                text-align: center;
                padding: 4px 5px;
                vertical-align: middle;
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
                <div style="position: absolute;margin: -38px 620px 0;">
                    <img src="<?php echo base_url('images/subbie-small-logo.png')?>" width="90">
                </div>
                <h3 style="text-align: center;"><?php echo @$staff_data->name;?> Pay Rate Periods</h3>
                <table class="table table-colored-header table-responsive">
                    <thead>
                    <tr>
                        <th>Rate Name</th>
                        <th>Rate Cost</th>
                        <th>Start Use</th>
                        <th>End Use</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $id = isset($_GET['id']) ? $_GET['id'] : 0;
                    if(count(@$staff_rate[$id]) > 0){
                        foreach(@$staff_rate[$id] as $rate){
                            $current = $rate->end_use == '0000-00-00' ? 'class="current-rate"' : '';
                            ?>
                            <tr <?php echo $current;?> >
                                <td><?php echo $rate->rate_name;?></td>
                                <td><?php echo '$ '.$rate->rate_cost;?></td>
                                <td><?php echo date('d/m/Y',strtotime($rate->start_use));?></td>
                                <td><?php echo $rate->end_use != '0000-00-00' ? date('d/m/Y',strtotime($rate->end_use)) : 'Present';?></td>
                            </tr>
                        <?php
                        }
                    }else{
                        ?>
                        <tr>
                            <td colspan="4">No data have been found.</td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
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
$pdfName = date('Ymd').'_PayRatePeriods';
@$domPdf->stream($pdfName.".pdf", array("Attachment" => 0));
$file_to_save = $dir.'/'.$pdfName.'.pdf';
//save the pdf file on the server
file_put_contents($file_to_save, $pdf);
?>