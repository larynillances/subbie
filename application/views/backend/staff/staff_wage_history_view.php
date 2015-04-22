<?php
echo form_open('','class="form-horizontal" role="form"');
$uri2 = $this->uri->segment(2);
?>
    <div class="form-group" style="width: 70%">
        <label class="col-sm-1 control-label" for="type">Type:</label>
        <div class="col-sm-2">
            <select id="type" name="type" class="form-control input-sm">
                <option value="1">Monthly</option>
                <option value="2">Yearly</option>
            </select>
        </div>
        <label class="col-sm-1 control-label date-label">Date:</label>
        <label class="col-sm-1 control-label year-label" style="display: none;">Year:</label>
        <div class="col-sm-2 month-dp">
            <?php echo form_dropdown('month',$month,$month_val,'class="form-control input-sm"')?>
        </div>
        <div class="col-sm-2">
            <?php echo form_dropdown('year',$year,$year_val,'class="form-control input-sm year"')?>
        </div>
        <div class="col-sm-4">
            <input type="submit" name="submit" class="btn btn-primary" value="Go">
            <a href="<?php echo base_url().'staffList'?>" class="btn btn-success"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
            <a href="<?php echo base_url().'staffWageHistory/'.$uri2.'/'.$type.'/'.$year_val.'/'.$month_val.'?print=1'?>" class="btn btn-success" target="_blank"><span class="glyphicon glyphicon-print"></span> Print</a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <?php
            if(count($name)>0):
                foreach($name as $v):
                    ?>
                    <div class="col-sm-3">
                        <h5><?php echo 'Name: '.$v->fname.' '.$v->lname;?></h5>
                    </div>
                    <div class="col-sm-2">
                        <h5><?php echo 'Tax No: '.$v->tax_number;?></h5>
                    </div>
                <?php
                endforeach;
            endif;
            ?>
        </div>
        <div class="col-sm-12" style="text-transform: uppercase;text-align: center">
            <h4 class="subheading">
                <?php
                echo $type == 2 ? 'Wage Summary for Year ' . $year_val : 'Month of ' . $thisMonth;
                ?>
            </h4>
        </div>
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
                ?>
                <tr>
                    <td style="vertical-align: middle;"><?php echo date('d-M-Y',strtotime('+6 days '.$v));?></td>
                    <?php
                    $data = @$staff[$v];
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
                        <td style="vertical-align: middle;"><?php echo $data['recruit']?></td>
                        <td style="vertical-align: middle;"><?php echo $data['admin']?></td>
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
                            <?php
                            echo $data['staff_id'] != 4 ? $data['nz_account'] : $account_one;
                            ?>
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
<?php
echo form_close();
?>

<script>
    $(function(e){
        $('#type').change(function(e){
            var year_label = $('.year-label');
            var date_label = $('.date-label');
            var month_dp = $('.month-dp');
            var subheading = $('.subheading');
            if($(this).val() == '2'){
                year_label.css({'display':'inline'});
                date_label.css({'display':'none'});
                month_dp.css({'display':'none'});
                subheading.html('Wage Summary of ' + $('.year').val())
            }else{
                year_label.css({'display':'none'});
                date_label.css({'display':'inline'});
                month_dp.css({'display':'inline'})
            }
        });
        $('.fixed-table').scrollTableBody({rowsToDisplay:12});
    })
</script>