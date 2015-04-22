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
                font-size: 11px;
                width: 100%!important;
            }
            .table-colored-header > thead > tr > th{
                /*text-transform: uppercase!important;*/
                background: #484848;
                border: 1px solid #d2d2d2;
                color: #ffffff;
                font-weight: normal;
                text-align: center;
                padding: 5px 2px;
            }
            .table-colored-header > tbody > tr > td{
                border: 1px solid #d2d2d2;
                text-align: center;
                padding: 2px;
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
            <table class="table-colored-header">
                <thead>
                <tr>
                    <th colspan="17" style="background: none;border: none;color: #000000;">
                        <h3><?php echo 'Wage Summary of '.$this_month_year;?></h3>
                    </th>
                </tr>
                </thead>
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Hours</th>
                    <th>Gross</th>
                    <th>Tax</th>
                    <th>Flight</th>
                    <th>Visa</th>
                    <th>Accom</th>
                    <th>Trans</th>
                    <th>Recruit</th>
                    <th>Admin</th>
                    <th>Nett</th>
                    <th>Loan</th>
                    <th>Distrib</th>
                    <th>PHP One</th>
                    <th>PHP Two</th>
                    <th>NZ ACC</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(count($date) >0):
                    foreach($date as $v):
                        $this_date = $v;
                        ?>
                        <tr>
                        <?php
                        $this_data = @$wage_data[$this_date];
                        ?>
                        <td rowspan="<?php echo count($this_data)?>"><?php echo date('d-m-Y',strtotime('+6 days '.$v))?></td>
                        <?php
                        $ref = 0;
                        if(count($this_data) >0):
                            foreach($this_data as $val):
                                echo $ref != 0 ? '<tr>' : '';
                                $val['symbols'] = $val['symbols'] == '₱' ? 'PHP' : $val['symbols'];
                                ?>
                                <td class="column">
                                    <?php
                                    echo $val['name'] != '' ? $val['name'].'<br/><strong>Tax: '.$val['tax_number'].'</strong>' :'';
                                    ?>
                                </td>
                                <?php
                                if($val['hours'] != 0):
                                    $php_two_convert = $val['account_two']* $val['rate_value'];
                                    $php_one_convert = $val['account_one'] * $val['rate_value'];
                                    ?>
                                    <td class="column" style="text-align: center">
                                        <?php
                                        echo $val['hours'];
                                        ?>
                                    </td>
                                    <td class="column">
                                        <?php
                                        echo $val['gross'];
                                        ?>
                                    </td>
                                    <td class="column">
                                        <?php
                                        echo $val['tax'];
                                        ?>
                                    </td>
                                    <td class="column">
                                        <?php
                                        echo $val['flight'] != '' ? '$'.$val['flight'].'<br/>' : '';
                                        $flight_debt =  @$total_bal[$v][$val['id']]['flight_debt'];
                                        echo '<strong class="value-class">';
                                        echo $flight_debt != 0 ? '$'.$flight_debt : '';
                                        echo '</strong>';
                                        ?>
                                    </td>
                                    <td class="column">
                                        <?php
                                        echo $val['visa'] != '' ? '$'.$val['visa'].'<br/>' : '';
                                        $visa_debt =  @$total_bal[$v][$val['id']]['visa_debt'];
                                        echo '<strong class="value-class">';
                                        echo $visa_debt != 0 ? '$'.$visa_debt : '';
                                        echo '</strong>';
                                        ?>
                                    </td>
                                    <td class="column">
                                        <?php
                                        echo $val['accommodation'];
                                        ?>
                                    </td>
                                    <td class="column"><?php echo $val['transport'];?></td>
                                    <td class="column"><?php echo $val['recruit'];?></td>
                                    <td class="column"><?php echo $val['admin'];?></td>
                                    <td class="column">
                                        <?php
                                        $nett = floatval(str_replace('$','',$val['nett']));
                                        echo $nett > 0 ? $val['nett'] : '<strong class="value-class">'.$val['nett'].'</strong>';
                                        ?>
                                    </td>
                                    <td class="column">
                                        <?php
                                        echo $val['deduction'] ? $val['deduction'].'<br/>' : '';
                                        $thisBalance =  @$total_bal[$v][$val['id']]['balance'];
                                        echo '<strong class="value-class">';
                                        echo $thisBalance != 0 ? '$'.$thisBalance : '';
                                        echo '</strong>';
                                        ?>
                                    </td>
                                    <td class="column">
                                        <?php
                                        $distribution = floatval(str_replace('$','',$val['distribution']));
                                        echo $distribution > 0 ? '$'.$val['distribution'] : '<strong class="value-class">$'.$val['distribution'].'</strong>';
                                        ?>
                                    </td>
                                    <td class="column" style="text-transform: uppercase">
                                        <?php
                                        if($val['account_one'] != ''){
                                            $account_one = $val['account_one'] != '' ?
                                                ($val['account_one'] > 0 ? '$'.number_format($val['account_one'],2).'<br/>' : '<strong class="value-class">$'.$val['account_one'].'</strong><br/>')
                                                : '$0.00'.'<br/>';
                                            echo '<strong>'.$account_one;
                                            echo '<span class="value-class">';
                                            echo $php_one_convert > 0 ? $val['symbols'].number_format($php_one_convert,2) : $val['symbols'].'0.00';
                                            echo '</span></strong>';
                                        }
                                        ?>
                                    </td>
                                    <td class="column" style="text-transform: uppercase">
                                        <?php
                                        if($val['account_two'] != ''){
                                            $account_two = $val['account_two'] != '' ? '$'.number_format($val['account_two'],2).'<br/>' : '$0.00'.'<br/>';
                                            /*echo '<strong class="header-title">'.$val['currency'].'-Two</strong><br/>';*/
                                            echo '<strong>'.$account_two;
                                            echo '<span class="value-class">';
                                            echo $php_two_convert > 0 ? $val['symbols'].number_format($php_two_convert,2) : $val['symbols'].'0.00';
                                            echo '</span></strong>';
                                        }
                                        ?>
                                    </td>
                                    <td class="column">
                                        <?php
                                        $nz_account = $val['nz_account'] != '' ? '$'.$val['nz_account'] : '';
                                        echo '<strong>'. $nz_account .'</strong><br/>';
                                        ?>
                                    </td>
                                <?php
                                else:
                                    for($i=0;$i<=14;$i++):
                                        ?>
                                        <td>&nbsp;</td>
                                    <?php
                                    endfor;
                                endif;
                                $ref++;
                                echo $ref != 0 ? '</tr>' : '';
                            endforeach;
                        else:
                            for($i=0;$i<=15;$i++):
                                ?>
                                <td>&nbsp;</td>

                            <?php
                            endfor;
                            echo '</tr>';
                        endif;
                    endforeach;
                else:
                    ?>
                    <tr>
                        <td colspan="17" style="text-align: center">
                            No data has been found.
                        </td>
                    </tr>
                <?php
                endif;
                ?>
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
$domPdf->set_paper($size, "landscape");

$domPdf->render();

// The next call will store the entire PDF as a string in $pdf
$pdf = $domPdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.
$pdfName = $this_month_year;
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