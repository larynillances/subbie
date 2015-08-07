<?php
echo form_open('','class="form-horizontal" role="form"');
$uri2 = $this->uri->segment(2);
?>
    <div class="form-group" style="width: 70%">
        <label class="col-sm-1 control-label" for="type">Type:</label>
        <div class="col-sm-2">
            <select id="type" name="type" class="form-control input-sm">
                <option value="1" <?php echo $type == 1 ? 'selected' : ''?> >Monthly</option>
                <option value="2" <?php echo $type == 2 ? 'selected' : ''?>>Yearly</option>
                <option value="3" <?php echo $type == 3 ? 'selected' : ''?>>Customize</option>
            </select>
        </div>
        <label class="col-sm-1 control-label date-label">Date:</label>
        <label class="col-sm-1 control-label year-label" style="display: none;">Year:</label>
        <div class="col-sm-2 month-dp">
            <?php echo form_dropdown('month',$month,$month_val,'class="form-control input-sm"')?>
        </div>
        <div class="col-sm-2 year-dp">
            <?php echo form_dropdown('year',$year,$year_val,'class="form-control input-sm year"')?>
        </div>
        <div class="date-range" style="display: none;">
            <div class="col-sm-4">
                <div class="col-sm-5" style="white-space: nowrap;margin-top: 2px;">
                    <span class="from-date date"><?php echo $start?></span>
                    <input type="hidden" name="start_date" class="from-date-picker" value="<?php echo $start?>">
                </div>
                <label class="control-label col-sm-2">To</label>
                <div class="col-sm-5" style="white-space: nowrap;margin-top: 2px;">
                    <span class="to-date date"><?php echo $end;?></span>
                    <input type="hidden" name="end_date" class="to-date-picker" value="<?php echo $end;?>">
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <input type="submit" name="submit" class="btn btn-primary" value="Go">
            <a href="<?php echo base_url().'staffList'?>" class="btn btn-success"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
            <a href="<?php echo base_url().'staffWageHistory/'.$uri2.'/'.$type.'/'.$year_val.'/'.$month_val.'/'.$start.'/'.$end.'?print=1'?>" class="btn btn-success" target="_blank"><span class="glyphicon glyphicon-print"></span> Print</a>
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
            <th>PAYE</th>
            <th>Kiwi</th>
            <th>ST Loan</th>
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
        $count = 1;
        $color = array(
            '1' => '#91C690',
            '2' => '#91C690',
            '3' => '#DC7864',
            '4' => '#DC7864',
            '5' => '#DCD195',
            '6' => '#DCD195',
        );
        if(count($date) >0):
            foreach($date as $v):

                $date = new DateTime($v);
                $week = $date->format("W");
                $modulus = $week ? $week % 2 : 0;
                $is_not_even = $start_week % 2;
                $condition = $is_not_even ? !$modulus : $modulus;
                ?>
                <tr <?php /*echo 'style="background:'.$color[$week].'!important;"'*/?> >
                    <td style="vertical-align: middle;"><?php echo date('d-M-Y',strtotime('+6 days '.$v));?></td>
                    <?php
                    $data = @$staff[$v];
                    $thisBalance =  @$total_bal[$v][$data['staff_id']]['balance'];
                    $thisFlight =  @$total_bal[$v][$data['staff_id']]['flight_debt'];
                    $thisVisa =  @$total_bal[$v][$data['staff_id']]['visa_debt'];
                    if($data['wage_type'] != 1):
                        if($data['hours'] != 0 && @$data['start_use']):
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
                            @$total['kiwi'] += floatval(str_replace('$','',$data['kiwi']));
                            @$total['st_loan'] += floatval(str_replace('$','',$data['st_loan']));
                            @$total['flight'] += floatval(str_replace('$','',$flight));
                            @$total['visa'] += floatval(str_replace('$','',$visa));
                            @$total['accommodation'] += floatval(str_replace('$','',$data['accommodation']));
                            @$total['transport'] += floatval(str_replace('$','',$data['transport']));
                            @$total['recruit'] += floatval(str_replace('$','',$recruit));
                            @$total['admin'] += floatval(str_replace('$','',$admin));
                            @$total['nett'] += $positive_nett;
                            @$total['installment'] += floatval(str_replace('$','',$installment));
                            @$total['distribution'] += $positive_distribution;
                            @$total['account_one'] += in_array($data['staff_id'],array(1,2,3)) ? $positive_php_one : 0;
                            @$total['account_two'] += floatval(str_replace('$','',$data['account_two']));
                            @$total['nz_account'] += !in_array($data['staff_id'],array(1,2,3)) ? floatval(str_replace('$','',$data['nz_account'])) : $positive_php_one;

                            ?>
                            <td style="vertical-align: middle;"><?php echo $data['hours']?></td>
                            <td style="vertical-align: middle;"><?php echo $data['gross']?></td>
                            <td style="vertical-align: middle;"><?php echo $data['tax']?></td>
                            <td style="vertical-align: middle;"><?php echo '$'.number_format($data['kiwi'],2)?></td>
                            <td style="vertical-align: middle;"><?php echo '$'.number_format($data['st_loan'],2)?></td>
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
                            <td style="vertical-align: middle;"><?php echo $visa > 0 || $visa != '' ? $data['recruit'] : ''?></td>
                            <td style="vertical-align: middle;"><?php echo $visa > 0 || $visa != '' ? $data['admin'] : ''?></td>
                            <td style="vertical-align: middle;">
                                <?php
                                echo $nett > 0 ? '$'.number_format($nett,2)
                                    : '<strong class="value-class">$'.number_format($nett,2).'</strong>';
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
                                echo $distribution > 0 ? '$'.number_format($distribution,2)
                                    : '<strong class="value-class">$'.number_format($distribution,2).'</strong>';
                                ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php
                                if(in_array($data['staff_id'],array(1,2,3)))
                                    echo $account_one > 0 ? '$'.number_format($account_one,2)
                                        : '<strong class="value-class">$'.number_format($account_one,2).'</strong>';
                                ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php echo $data['account_two'];?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php
                                echo !in_array($data['staff_id'],array(1,2,3)) ? $data['nz_account'] : $account_one;
                                ?>
                            </td>
                        <?php
                        else:
                            for($i=1;$i<=17;$i++):
                                echo '<td class="grey-background">&nbsp;</td>';
                            endfor;
                        endif;
                    else:
                        if($data['hours'] != 0
                            && $condition
                            && (@$data['start_use'] && $v <= date('Y-m-d'))):
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
                            @$total['kiwi'] += floatval(str_replace('$','',$data['kiwi']));
                            @$total['st_loan'] += floatval(str_replace('$','',$data['st_loan']));
                            @$total['flight'] += floatval(str_replace('$','',$flight));
                            @$total['visa'] += floatval(str_replace('$','',$visa));
                            @$total['accommodation'] += floatval(str_replace('$','',$data['accommodation']));
                            @$total['transport'] += floatval(str_replace('$','',$data['transport']));
                            @$total['recruit'] += floatval(str_replace('$','',$recruit));
                            @$total['admin'] += floatval(str_replace('$','',$admin));
                            @$total['nett'] += $positive_nett;
                            @$total['installment'] += floatval(str_replace('$','',$installment));
                            @$total['distribution'] += $positive_distribution;
                            @$total['account_one'] += in_array($data['staff_id'],array(1,2,3)) ? $positive_php_one : 0;
                            @$total['account_two'] += floatval(str_replace('$','',$data['account_two']));
                            @$total['nz_account'] += !in_array($data['staff_id'],array(1,2,3)) ? floatval(str_replace('$','',$data['nz_account'])) : $positive_php_one;

                            ?>
                            <td style="vertical-align: middle;"><?php echo $data['hours']?></td>
                            <td style="vertical-align: middle;"><?php echo $data['gross']?></td>
                            <td style="vertical-align: middle;"><?php echo $data['tax']?></td>
                            <td style="vertical-align: middle;"><?php echo '$'.number_format($data['kiwi'],2)?></td>
                            <td style="vertical-align: middle;"><?php echo '$'.number_format($data['st_loan'],2)?></td>
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
                            <td style="vertical-align: middle;"><?php echo $visa > 0 || $visa != '' ? $data['recruit'] : ''?></td>
                            <td style="vertical-align: middle;"><?php echo $visa > 0 || $visa != '' ? $data['admin'] : ''?></td>
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
                                if(in_array($data['staff_id'],array(1,2,3)))
                                    echo $account_one > 0 ? '$'.$account_one
                                        : '<strong class="value-class">$'.$account_one.'</strong>';
                                ?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php echo $data['account_two'];?>
                            </td>
                            <td style="vertical-align: middle;">
                                <?php
                                echo !in_array($data['staff_id'],array(1,2,3)) ? $data['nz_account'] : $account_one;
                                ?>
                            </td>
                        <?php
                        else:
                            for($i=1;$i<=17;$i++):
                                echo '<td class="grey-background">&nbsp;</td>';
                            endfor;
                        endif;
                    endif;
                    ?>
                </tr>
                <?php
                $ref++;
                $count++;
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
                for($i=1;$i<=17;$i++){
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
<style>
    .date{
        color: #ff0000;
    }
</style>
<script>
    $(function(e){
        var type = $('#type');
        var year_label = $('.year-label');
        var date_label = $('.date-label');
        var month_dp = $('.month-dp');
        var year_dp = $('.year-dp');
        var date_range = $('.date-range');
        var subheading = $('.subheading');

        type.change(function(e){
            if($(this).val() == 2){
                year_label.css({'display':'inline'});
                date_label.css({'display':'none'});
                month_dp.css({'display':'none'});
                year_dp.css({'display':'inline'});
                date_range.css({'display':'none'});
                subheading.html('Wage Summary of ' + $('.year').val())
            }
            else if($(this).val() == 3){
                year_label.css({'display':'none'});
                year_dp.css({'display':'none'});
                date_label.css({'display':'none'});
                month_dp.css({'display':'none'});
                date_range.css({'display':'inline'});
                subheading.html('Wage Summary of <?php echo date('d F Y',strtotime($start)) .' to '. date('d F Y',strtotime($end))?>');
            }
            else{
                year_label.css({'display':'none'});
                date_label.css({'display':'inline'});
                month_dp.css({'display':'inline'});
                date_range.css({'display':'none'});
                year_dp.css({'display':'inline'});
            }
        });
        var checkSelectedType = function(){
            if(type.val() == 2){
                year_label.css({'display':'inline'});
                date_label.css({'display':'none'});
                month_dp.css({'display':'none'});
                subheading.html('Wage Summary of ' + $('.year').val())
            }else if(type.val() == 3){
                year_label.css({'display':'none'});
                year_dp.css({'display':'none'});
                date_label.css({'display':'none'});
                month_dp.css({'display':'none'});
                date_range.css({'display':'inline'});
                subheading.html('Wage Summary of <?php echo date('d F Y',strtotime($start)) .' to '. date('d F Y',strtotime($end))?>');
            }
        };

        checkSelectedType();

        $('.from-date-picker').datepicker({
            showOn: 'button',
            buttonImageOnly: true,
            dateFormat:"dd-mm-yy",
            buttonImage: bu + 'images/calendar-add.png',
            onSelect:function(){
                $('.from-date').html($(this).val());
            },
            onClose: function( selectedDate ) {
                $( ".to-date-picker" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $('.to-date-picker').datepicker({
            showOn: 'button',
            buttonImageOnly: true,
            dateFormat:"dd-mm-yy",
            buttonImage: bu + 'images/calendar-add.png',
            onSelect:function(){
                $('.to-date').html($(this).val());
            },
            onClose: function( selectedDate ) {
                $( ".from-date-picker" ).datepicker( "option", "maxDate", selectedDate );
            }
        });

        $('.fixed-table').scrollTableBody({rowsToDisplay:12});
    })
</script>