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
            }
            .table-colored-header > tbody > tr > td{
                border: 1px solid #d2d2d2;
                text-align: center;
                padding: 5px 10px;
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
                <table class="table-colored-header table">
                    <thead>
                    <tr>
                        <th colspan="15" style="background: none;color: #000000;border: none">
                            <div style="font-weight: bold;text-transform: uppercase;font-size: 15px;">Monthly pay of <?php echo $this_month_year;?></div>
                        </th>
                    </tr>
                    </thead>
                    <thead>
                    <tr>
                        <th style="width: 10%;">Name</th>
                        <th>Hours</th>
                        <th>Gross</th>
                        <th>Tax</th>
                        <th>Flight</th>
                        <th>Visa</th>
                        <th>Accom</th>
                        <th>Loans</th>
                        <th>Trans</th>
                        <th>Recruit</th>
                        <th>Admin</th>
                        <th>NET</th>
                        <th>PHP One</th>
                        <th>PHP Two</th>
                        <th>NZ ACC</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $hours = 0;
                    $gross = 0;
                    $tax = 0;
                    $flight = 0;
                    $visa = 0;
                    $accommodation = 0;
                    $transport = 0;
                    $recruit = 0;
                    $admin = 0;
                    $installment = 0;
                    $total_net = 0;
                    $total_php_one = 0;
                    $total_php_two = 0;
                    $total_acc_nz = 0;
                    if(count($staff)>0):
                        foreach($staff as $v):
                            $monthYear = $this->uri->segment(3).'-'.$this->uri->segment(4);
                            @$balance = @$total_bal[$monthYear][$v->id];
                            $flight_debt = @$balance['flight_debt'] > 0 ? '$'.number_format(@$balance['flight_debt'],2) : '&nbsp;';
                            $visa_debt = @$balance['visa_debt'] > 0 ? '$'.number_format(@$balance['visa_debt'],2) : '&nbsp;';
                            $loan = @$balance['balance'] > 0 ? '$'.number_format(@$balance['balance'],2) : '&nbsp;';
                            ?>
                            <tr>
                                <td style="text-align: left;"><?php echo $v->fname.' '.$v->lname;?></td>
                                <?php
                                @$staff_data = @$monthly_pay[$v->id];
                                if(count(@$staff_data)>0):
                                    if(@$staff_data['hours'] != 0):
                                        $hours += @$staff_data['hours'];
                                        $gross += @$staff_data['gross'];
                                        $tax += @$staff_data['tax'];
                                        $flight += @$staff_data['flight'];
                                        $visa += @$staff_data['visa'];
                                        $accommodation += @$staff_data['accommodation'];
                                        $transport += @$staff_data['transport'];
                                        $recruit += @$staff_data['recruit'];
                                        $installment += @$staff_data['installment'];
                                        $admin += @$staff_data['admin'];
                                        $total_deduct = @$staff_data['tax'] + @$staff_data['installment'] + @$staff_data['flight'] + @$staff_data['visa'] + @$staff_data['accommodation'] + @$staff_data['transport'] + @$staff_data['recruit'] + @$staff_data['admin'];
                                        $net = @$staff_data['gross'] - $total_deduct;
                                        $php_one = $net - (@$staff_data['nz_account'] + @$staff_data['account_two']);
                                        $total_php_one += $php_one;
                                        $php_converted = $php_one * @$staff_data['rate_value'];
                                        $account_two_converted = @$staff_data['account_two'] * @$staff_data['rate_value'];
                                        $total_php_two += @$staff_data['account_two'];
                                        $total_acc_nz += @$staff_data['nz_account'];
                                        $total_net += $net;
                                        @$staff_data['symbols'] = @$staff_data['symbols'] == '₱' ? 'Php' : @$staff_data['symbols'];
                                        ?>
                                        <td class="column"><?php echo @$staff_data['hours'];?></td>
                                        <td class="column"><?php echo @$staff_data['gross'] ? '$'.number_format(@$staff_data['gross'],2) : '';?></td>
                                        <td class="column"><?php echo @$staff_data['gross'] != 0 ? '$'.number_format(@$staff_data['tax'],2) : '';?></td>
                                        <td class="column">
                                            <?php
                                            echo @$staff_data['flight'] != 0 ? '$'.number_format(@$staff_data['flight'],2) : '';
                                            echo '<br/>';
                                            echo '<strong class="value-class">'.$flight_debt.'</strong>';
                                            ?>
                                        </td>
                                        <td class="column">
                                            <?php
                                            echo @$staff_data['visa'] != 0 ? '$'.number_format(@$staff_data['visa'],2) : '';
                                            echo '<br/>';
                                            echo '<strong class="value-class">'.$visa_debt.'</strong>';
                                            ?>
                                        </td>
                                        <td class="column"><?php echo @$staff_data['accommodation'] != 0 ? '$'.number_format(@$staff_data['accommodation'],2) : '';?></td>
                                        <td class="column">
                                            <?php
                                            echo @$staff_data['installment'] != 0 ? '$'.number_format(@$staff_data['installment'],2) : '';
                                            echo '<br/>';
                                            echo '<strong class="value-class">'.$loan.'</strong>';
                                            ?>
                                        </td>
                                        <td class="column"><?php echo @$staff_data['transport'] != 0 ? '$'.number_format(@$staff_data['transport'],2) : '';?></td>
                                        <td class="column"><?php echo @$staff_data['recruit'] != 0 ? '$'.number_format(@$staff_data['recruit'],2) : '';?></td>
                                        <td class="column"><?php echo @$staff_data['admin'] != 0 ? '$'.number_format(@$staff_data['admin'],2) : '';?></td>
                                        <td class="column"><?php echo @$staff_data['gross'] != 0 ? '$'.number_format($net,2) : '';?></td>
                                        <td class="column">
                                            <?php
                                            echo '$'.number_format($php_one,2);
                                            echo '<br/>';
                                            echo '<strong class="value-class">';
                                            echo $php_one > 0 ? @$staff_data['symbols'].number_format($php_converted,2) : @$staff_data['symbols'].'0.00';
                                            echo '</strong>';
                                            ?>
                                        </td>
                                        <td class="column">
                                            <?php
                                            echo @@$staff_data['account_two'] != '' ? '$'.number_format(@$staff_data['account_two'],2): '&nbsp;';
                                            echo '<br/>';
                                            echo '<strong class="value-class">';
                                            echo $account_two_converted > 0 ? @$staff_data['symbols'].number_format($account_two_converted,2) : @$staff_data['symbols'].'0.00';
                                            echo '</strong>';
                                            ?>
                                        </td>
                                        <td class="column">
                                            <?php
                                            echo @$staff_data['nz_account'] != '' ? '$'.number_format(@$staff_data['nz_account'],2) : '';
                                            ?>
                                        </td>
                                    <?php
                                    else:
                                        for($i=0;$i<=13;$i++):
                                            ?>
                                            <td class="column"></td>
                                        <?php
                                        endfor;
                                    endif;
                                else:
                                    for($i=0;$i<=13;$i++):
                                        ?>
                                        <td class="column"></td>
                                    <?php
                                    endfor;
                                endif;
                                ?>
                            </tr>
                        <?php
                        endforeach;
                    else:
                        ?>
                        <tr>
                            <td colspan="15">No data was found.</td>
                        </tr>
                    <?php
                    endif;
                    ?>
                    <tr class="danger">
                        <td style="text-align: right;font-weight: bold;text-transform: uppercase;background: none;">Total</td>
                        <td><?php echo $hours;?></td>
                        <td><?php echo '$'.number_format($gross,2);?></td>
                        <td><?php echo '$'.number_format($tax,2);?></td>
                        <td><?php echo '$'.number_format($flight,2);?></td>
                        <td><?php echo '$'.number_format($visa,2);?></td>
                        <td><?php echo '$'.number_format($accommodation,2);?></td>
                        <td><?php echo '$'.number_format($installment,2);?></td>
                        <td><?php echo '$'.number_format($transport,2);?></td>
                        <td><?php echo '$'.number_format($recruit,2);?></td>
                        <td><?php echo '$'.number_format($admin,2);?></td>
                        <td><?php echo '$'.number_format($total_net,2);?></td>
                        <td><?php echo $total_php_one > 0 ? '$'.number_format($total_php_one,2) : '$0.00'?></td>
                        <td><?php echo '$'.number_format($total_php_two,2);?></td>
                        <td><?php echo '$'.number_format($total_acc_nz,2);?></td>
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
$pdfName = $this_month_year;
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