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
                font-size: 11px;
                width: 100%!important;
            }
            .table-colored-header > thead > tr > th{
                /*text-transform: uppercase!important;*/
                background: #484848;
                border: 1px solid #a6a6a6;
                color: #ffffff;
                font-weight: normal;
                text-align: center;
                padding: 5px 2px;
            }
            .table-colored-header > tbody > tr > td{
                border: 1px solid #a6a6a6;
                text-align: center;
                padding: 5px 2px;
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
            .grey-background{
                background: #c4c4c4;
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
                <?php
                if(count($name)>0):
                    foreach($name as $v):
                        ?>
                        <table>
                            <tr>
                                <td style="width: 400px;"><h3><?php echo 'Name: ' . $v->fname.' '.$v->lname;?></h3></td>
                                <td style="width: 200px;"><h3><?php echo 'Tax No: '.$v->tax_number;?></h3></td>
                            </tr>
                        </table>
                    <?php
                    endforeach;
                endif;
                ?>
                <div class="col-sm-12" style="text-transform: uppercase;text-align: center">
                    <h3 class="subheading">
                        <?php
                        echo $type == 2 ? 'Wage Summary for Year ' . $year_val :
                            ($type == 3 ? 'Wage Summary for ' . date('d F Y',strtotime($start)) .' to '. date('d F Y',strtotime($end))
                                :'Month of ' . $thisMonth);
                        ?>
                    </h3>
                </div>
                <table class="table table-colored-header fixed-table">
                    <thead>
                    <tr>
                        <th>Week</th>
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
                        <th>Dist</th>
                        <th>PHP One</th>
                        <th>PHP Two</th>
                        <th>NZ ACC</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total = array();
                    $ref = 0;
                    if(count($date) >0):
                        foreach($date as $v):
                            $data = @$staff[$v];
                            ?>
                            <tr>
                                <td style="vertical-align: middle;white-space: nowrap!important;"><?php echo date('d-M-Y',strtotime('+6 days '.$v));?></td>
                                <?php
                                $thisBalance =  @$total_bal[$v][$data['staff_id']]['balance'];
                                $thisFlight =  @$total_bal[$v][$data['staff_id']]['flight_debt'];
                                $thisVisa =  @$total_bal[$v][$data['staff_id']]['visa_debt'];
                                if($data['hours'] != 0):
                                    $installment = floatval(str_replace('$','',$data['installment']));
                                    $installment = $thisBalance > 0 ? ($thisBalance <= $installment ? $thisBalance : $data['installment']) : 0;

                                    $flight = floatval(str_replace('$','',$data['flight']));
                                    $flight = $thisFlight > 0 ? ($thisFlight <= $flight ? $thisFlight : $data['flight']) : 0;

                                    $visa = floatval(str_replace('$','',$data['visa']));
                                    $visa = $thisVisa > 0 ? ($thisVisa <= $visa ? $thisVisa : $data['visa']): 0;

                                    $_visa = $thisVisa > 0 ? 0 : $data['visa'];
                                    $_flight = $thisFlight > 0 ? 0 : $data['flight'];

                                    $nett = floatval(str_replace('$','',$data['nett']));
                                    $nett = $nett + floatval(str_replace('$','',$_visa)) + floatval(str_replace('$','',$_flight));
                                    $recruit = $visa > 0 || $visa != '' ? $data['recruit'] : 0;
                                    $admin = $visa > 0 || $visa != '' ? $data['admin'] : 0;

                                    $distribution = $nett - floatval(str_replace('$','',$installment));
                                    $account_two = floatval(str_replace('$','',$data['account_two']));
                                    $nz_account = floatval(str_replace('$','',$data['nz_account']));
                                    $account_one = $distribution - ($account_two + $nz_account);

                                    $positive_nett = $nett > 0 ? floatval($nett) : 0;
                                    $positive_php_one = $account_one > 0 ? floatval($account_one) : 0;
                                    $positive_distribution = $distribution > 0 ? floatval($distribution) : 0;

                                    @$total['hours'] += $data['hours'];
                                    @$total['gross'] += floatval(str_replace('$','',$data['gross']));
                                    @$total['tax'] += floatval(str_replace('$','',$data['tax']));
                                    @$total['flight'] += floatval(str_replace('$','',$flight));
                                    @$total['visa'] += floatval(str_replace('$','',$visa));
                                    @$total['accommodation'] += floatval(str_replace('$','',$data['accommodation']));
                                    @$total['transport'] += floatval(str_replace('$','',$data['transport']));
                                    @$total['recruit'] += floatval(str_replace('$','',$recruit));
                                    @$total['admin'] += floatval(str_replace('$','',$admin));
                                    @$total['nett'] += $positive_nett;
                                    @$total['installment'] += floatval(str_replace('$','',$installment));
                                    @$total['distribution'] += $positive_distribution;
                                    @$total['account_one'] += $data['staff_id'] != 4 ? $positive_php_one : 0;
                                    @$total['account_two'] += floatval(str_replace('$','',$data['account_two']));
                                    @$total['nz_account'] += $data['staff_id'] != 4 ? floatval(str_replace('$','',$data['nz_account'])) : $positive_php_one;

                                    ?>
                                    <td style="vertical-align: middle;"><?php echo $data['hours']?></td>
                                    <td style="vertical-align: middle;"><?php echo $data['gross']?></td>
                                    <td style="vertical-align: middle;"><?php echo $data['tax']?></td>
                                    <td>
                                        <?php
                                        echo $flight != '' ? $flight.'<br/>' : '';
                                        echo '<strong class="value-class">';
                                        echo $thisFlight != 0 ? '$'.$thisFlight : '';
                                        echo '</strong>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo $visa != '' ? $visa.'<br/>' : '';
                                        echo '<strong class="value-class">';
                                        echo $thisVisa != 0 ? '$'.$thisVisa : '';
                                        echo '</strong>';
                                        ?>
                                    </td>
                                    <td style="vertical-align: middle;"><?php echo $data['accommodation']?></td>
                                    <td style="vertical-align: middle;"><?php echo $data['transport']?></td>
                                    <td style="vertical-align: middle;"><?php echo $recruit?></td>
                                    <td style="vertical-align: middle;"><?php echo $admin?></td>
                                    <td style="vertical-align: middle;">
                                        <?php
                                        echo $nett > 0 ? '$'.$nett
                                            : '<strong class="value-class">$'.$nett.'</strong>';
                                        ?>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <?php
                                        echo $installment != '' ? '$'.number_format($installment,2,'.',',') : '';
                                        echo '<br/>';
                                        echo '<strong class="value-class">';
                                        echo $thisBalance != 0 ? '$'.number_format($thisBalance,2,'.',',') : '';
                                        echo '</strong>';
                                        //echo '$'.$thisBalance;
                                        ?>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <?php
                                        echo $distribution > 0 ? '$'.$distribution
                                            : '<strong class="value-class">$'.$distribution.'</strong>';
                                        ?>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <?php
                                        if($data['staff_id'] != 4)
                                        echo $account_one > 0 ? '$'.$account_one
                                            : '<strong class="value-class">$'.$account_one.'</strong>';
                                        ?>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <?php echo $data['account_two'];?>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <?php echo $data['staff_id'] != 4 ? $data['nz_account'] : $account_one;?>
                                    </td>
                                <?php
                                else:
                                    for($i=1;$i<=15;$i++):
                                        echo '<td class="grey-background">&nbsp;</td>';
                                    endfor;
                                endif;
                                ?>
                            </tr>
                            <?php
                            $ref++;
                        endforeach;
                    endif;
                    ?>
                    <tr class="danger">
                        <td style="text-align: right;"><strong>Total:</strong></td>
                        <?php
                        $ref = 0;
                        if(count($total) > 0){
                            foreach($total as $tv){
                                ?>
                                <td><strong><?php echo $ref != 0 ? '$'.number_format($tv,2) : number_format($tv,2);?></strong></td>
                                <?php
                                $ref++;
                            }
                        }else{
                            for($i=1;$i<=15;$i++){
                                echo '<td><strong>$ 0.00</strong></td>';
                            }
                        }
                        ?>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </body>
    </html>
<?php
$size = 'A4';
$html = ob_get_clean();

$domPdf = new DOMPDF();
$domPdf->load_html($html,'UTF-8');
$domPdf->set_paper($size, "landscape");

$domPdf->render();

// The next call will store the entire PDF as a string in $pdf
$pdf = $domPdf->output();

// You can now write $pdf to disk, store it in a database or stream it
// to the client.
$pdfName = $type == 2 ? 'Wage Summary for Year ' . $year_val : 'Month of ' . $thisMonth;
@ $domPdf->stream($pdfName.".pdf", array("Attachment" => 0));

$file_to_save = $dir.'/'.$pdfName.'.pdf';
//save the pdf file on the server
//file_put_contents($file_to_save, $domPdf->output());
//print the pdf file to the screen for saving
/*header('Content-type: application/pdf');
header('Content-Disposition: inline; filename="'.$pdfName.'.pdf"');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . filesize($file_to_save));
header('Accept-Ranges: bytes');
readfile($file_to_save);*/
?>