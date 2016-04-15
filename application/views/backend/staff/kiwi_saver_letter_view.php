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
            .letter-header{
                margin: 0 0 20px 0;
            }
            .employee-info{
                margin: 30px 0;
            }
            .text-center{
                text-align: center;
            }
            p{
                text-align: justify;
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
            <?php
            $staff_name = '';
            if(count($staff_info) > 0){
                foreach($staff_info as $sv){
                    $staff_name = $sv->fname.''.$sv->lname;
                    $then = date('Y-m-d H:i:s',strtotime($sv->date_employed));
                    $then = new DateTime($then);

                    $now = new DateTime();

                    $sinceThen = $then->diff($now);

                    $total_week = DateDiff('ww',date('d F Y',strtotime($sv->date_employed)),date('d F Y'));
                    $fname = explode(' ',$sv->fname);
                    ?>
                    <div>
                        <div class="letter-header">
                            <?php echo $subbie_info->letter_header;?>
                        </div>
                        <div class="employee-info">
                            <?php echo $sv->fname.' '.$sv->lname;?>
                        </div>
                        <div class="employee-info" style="margin-bottom: 15px;!important;">
                            <?php echo 'Dear '.$fname[0].',';?>
                        </div>
                        <div class="letter-body">
                            <div class="text-center">Re: Kiwisaver contributions</div>
                            <p>
                                With the new Payroll system being developed, it has come to our attention that Kiwisaver contributions
                                should have automatically been made for you since you commenced work with Subbie Solutions, unless you
                                specifically decline to make Kiwisaver deductions from your pay.
                            </p>
                            <p>
                                There are 2 options available to you, to either indicate that you do wish for Kiwisaver deductions to
                                be made from your pay, or that you decline to make Kiwisaver contributions.
                            </p>
                            <p>
                                It is your choice to make, this is not mandatory, but as far as whether we do pay any Kiwisaver contributions
                                to IRD on your behalf or not, you need to make the decision one way or another and promptly, to correct this
                                oversight with previous pays.
                            </p>
                            <p>
                                By signing and returning the bottom portion of this letter, either Accepting or Declining to make Kiwisaver
                                Contributions, we will then either set your Employee Details to show that you do not wish to make Kiwisaver
                                Contributions and no further changes need to happen to your pay, or if you choose to start then they must be
                                backdated to when you commenced work with Subbie Solutions and will need to be deducted from your next pay.
                            </p>
                            <p>
                                You are shown as commencing employment with Subbie Solutions on <strong><?php echo date('j F Y',strtotime($sv->date_employed))?></strong>.
                            </p>
                            <p>
                                You have been employed by Subbie Solutions for <strong><?php echo $sinceThen->y;?> years</strong>, <strong><?php echo $sinceThen->m;?> months</strong>, <strong><?php echo @$sinceThen->weekday ? @$sinceThen->weekday : 0;?> weeks</strong>,
                                which amounts to a total of <strong><?php echo $total_week;?> weeks</strong> for which Kiwisaver Contributions would need to be made.
                            </p>
                            <p>
                                The deductions for each week <?php echo $sv->id == 8 ? '<strong>('.$kiwi_rate.'%)</strong>' : ''?>, along with any amounts already deducted for you <strong>(3%)</strong>, and the grand total for all of those weeks, is shown below.
                                <!--The deduction for each week, and the grand total for all of those weeks, is shown below.-->
                            </p>
                            <p>
                                <?php
                                $total = 0;
                                $total_diff = 0;
                                $ref = 1;
                                if(count($kiwi_list) > 0){
                                    foreach($kiwi_list as $start_date=>$val){
                                        $_date = new DateTime($start_date);
                                        $week = $_date->format('W');
                                        $year = $_date->format('Y');
                                        if($val['hours'] > 0){
                                            $num = explode('.',$val['kiwi']);
                                            $num_digit = strlen($num[0]);
                                            $space = $num_digit < 3 ? ($num_digit < 2 ? '&nbsp;&nbsp;&nbsp;' : '&nbsp;') : '';

                                            echo '<div style="white-space: nowrap;">';
                                            echo '<strong>'.$year.' Week '.$week.' </strong>';
                                            echo 'Kiwisaver Amount';
                                            echo ' '.$kiwi_rate.'%';
                                            echo '<strong> '.'$' . $space . number_format($val['kiwi'],2).'</strong>';
                                            echo $sv->id == 8 ? '' : '<br/>';
                                            if($sv->id == 8){
                                                $diff = number_format($val['kiwi'],2,'.','') - number_format($kiwi_diff[$start_date]['kiwi'],2,'.','');
                                                $total_diff += floatval(number_format($kiwi_diff[$start_date]['kiwi'],2,'.',''));
                                                $num = explode('.',$diff);
                                                $num_digit = strlen($num[0]);
                                                $space = $num_digit < 3 ? ($num_digit < 2 ? '&nbsp;&nbsp;&nbsp;' : '&nbsp;') : '';

                                                echo ' Already deducted 3% ';
                                                echo '<strong> '.'$ '.number_format($kiwi_diff[$start_date]['kiwi'],2).'</strong>';
                                                echo ' Difference <strong>$' . $space . number_format($diff,2).'</strong>';
                                            }
                                            echo '</div>';
                                            $total += $val['kiwi'];
                                            $ref++;
                                        }
                                    }
                                }
                                $style = $ref < 15 ? '' : 'style="page-break-before: always;"';
                                $style_ = $ref < 15 ? '' : 'style="page-break-before: always;border-top: 1px dotted #000000;"';
                                ?>
                            </p>
                            <p>
                                If you wish to commence Kiwisaver Contributions, the amount of <strong><?php echo '$'.number_format(($total - $total_diff),2);?></strong> will be deducted from your next pay.<br/><br/>

                                We do apologise for the oversight in not applying Kiwisaver Deductions automatically from your pay
                                immediately you started working with us, and with your help we'll have this rectified promptly.<br/><br/>

                                Kind regards<br/><br/><br/><br/>
                                Tony Boniface<br/>
                                Operations Manager<br/>
                                Subbie Solutions
                            </p>
                        </div><br/><br/>
                        <!--<div class="letter-footer" <?php /*echo $style;*/?> >
                            <p>
                                Kind regards<br/><br/><br/><br/>
                                Tony Boniface<br/>
                                Operations Manager<br/>
                                Subbie Solutions
                            </p><br/>
                        </div><br/><br/>-->
                        <div <?php echo $style_;?> >
                            <p>
                                Cross out whichever option does not apply to you.
                            </p><br/>
                            <p>
                                <strong>Option 1 [Accept Kiwisaver]:</strong>
                            </p>
                            <p>
                                I, <strong><?php echo $sv->fname.' '.$sv->lname;?></strong>, request Subbie Solutions Ltd to commence deducting
                                Kiwisaver Contributions from my pay, and backdate these deductions to the date I commenced work
                                with Subbie Solutions Ltd. These deductions will be made at the minimum Kiwisaver rate of <?php echo $sv->id == 8 ? $kiwi_rate.'% that I have requested' : $kiwi_rate.'%';?>.
                            </p>
                            <p>
                                I understand that any previous Kiwisaver Employee Contributions not yet paid, that are required
                                to bring my Kiwisaver payments to IRD up to date, will be deducted from my next pay, for payment
                                to the IRD Kiwisaver scheme.
                            </p><br/>
                            <p>
                                <strong>Option 2 [Decline Kiwisaver]:</strong>
                            </p>
                            <p>
                                I, <strong><?php echo $sv->fname.' '.$sv->lname;?></strong>, request Subbie Solutions Ltd not to make any deductions
                                from my Pay for the Kiwisaver Scheme.
                            </p>
                            <p>
                                I understand that declining Kiwisaver will make this action applicable from the date which I commenced
                                working for Subbie Solutions Ltd and if I wish to commence at a later date must fill out the appropriate Kiwisaver form.
                            </p><br/><br/><br/>
                            <table>
                                <tr>
                                    <td>Signed:</td>
                                    <td style="width: 400px;border-bottom: 1px solid #000000;">&nbsp;</td>
                                    <td>Date:</td>
                                    <td style="width: 30px;border-bottom: 1px solid #000000;">&nbsp;</td>
                                    <td><?php echo date('F Y');?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
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
$pdfName = date('Ymd-Hi') . '_' . $staff_name.'_KiwisaverLetter';
@$domPdf->stream($pdfName.".pdf", array("Attachment" => 0));
//$file_to_save = $dir.'/'.$pdfName.'.pdf';
//save the pdf file on the server
//file_put_contents($file_to_save, $pdf);

?>