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
            .header-title{
                background: #484848;
                color: #ffffff;
                padding: 2px;
            }
            .value-class{
                color: #ff0000;
                text-transform: capitalize!important;
            }
            .table-colored-header{
                border-collapse: collapse;
                margin:0 -20px;
                font-size: 12px;
                width: 100%!important;
            }
            .table-colored-header > thead > tr > th{
                /*text-transform: uppercase!important;*/
                background: #484848;
                border: 1px solid #d2d2d2;
                color: #ffffff;
                font-weight: normal;
                text-align: center;
                padding: 5px 10px;
                vertical-align: middle;
            }
            .table-colored-header > tbody > tr > td{
                border: 1px solid #d2d2d2;
                text-align: center;
                padding: 5px 10px;
                vertical-align: middle;
            }
            .table > thead > tr > td.danger,
            .table > tbody > tr > td.danger,
            .table > tfoot > tr > td.danger,
            .table > thead > tr > th.danger,
            .table > tbody > tr > th.danger,
            .table > tfoot > tr > th.danger,
            .table > thead > tr.danger > td,
            .table > tbody > tr.danger > td,
            .table > tfoot > tr.danger > td,
            .table > thead > tr.danger > th,
            .table > tbody > tr.danger > th,
            .table > tfoot > tr.danger > th {
                background-color: #f2dede;
            }
            .table > tbody > tr.has-increase > td{
                background: #63c65c!important;
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
    <div id="wrap">
        <div id="content">
            <div class="content">
                <div style="position: absolute;margin: -15px 940px 0;">
                    <img src="<?php echo base_url('images/subbie-small-logo.png')?>" width="80">
                </div>
                <div class="col-sm-12">
                    <table style="width: 100%">
                        <thead>
                        <tr>
                            <th style="text-align: center;">
                                <h2><?php echo $page_name;?></h2>
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div><br/>
                <table class="table table-colored-header">
                    <thead>
                    <tr>
                        <th>Year</th>
                        <th>Week No.</th>
                        <th>Week Ending</th>
                        <th>Frequency</th>
                        <th>PAYE Code</th>
                        <th>Wage Type</th>
                        <th>Rate Type</th>
                        <th>Kiwi Emp.</th>
                        <th>Kiwi Empr.</th>
                        <th>ESCT</th>
                        <th>Pay Increase</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(count($staff_data) > 0){
                        foreach($staff_data as $sv){
                            echo $sv->has_pay_increase ? '<tr class="has-increase">' : '<tr>';
                            ?>
                                <td><?php echo $sv->year;?></td>
                                <td><?php echo $sv->week;?></td>
                                <td><?php echo $sv->week_ending;?></td>
                                <td><?php echo $sv->frequency;?></td>
                                <td><?php echo $sv->tax_code;?></td>
                                <td><?php echo $sv->description;?></td>
                                <td><?php echo $sv->rate_name;?></td>
                                <td><?php echo $sv->kiwi;?></td>
                                <td><?php echo $sv->emp_kiwi;?></td>
                                <td><?php echo $sv->esct_rate;?></td>
                                <td><?php echo $sv->pay_increase;?></td>
                        <?php
                            echo '</tr>';
                        }
                    }else{
                        ?>
                        <tr>
                            <td colspan="11">No data was found.</td>
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
$domPdf->set_paper('A4', "landscape");

$domPdf->render();

// The next call will store the entire PDF as a string in $pdf
$pdf = $domPdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.
$pdfName = $file_name;

@$domPdf->stream($pdfName.".pdf", array("Attachment" => 0));
$file_to_save = $dir.'/'.$pdfName.'.pdf';
//save the pdf file on the server
file_put_contents($file_to_save, $pdf);
?>