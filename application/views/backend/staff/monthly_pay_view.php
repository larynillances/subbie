<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="form-group">
    <label class="col-sm-1 control-label" >Date:</label>
    <div class="col-sm-2">
        <?php echo form_dropdown('month',$month,$thisMonth,'class="form-control input-sm"')?>
    </div>
    <div class="col-sm-2">
        <?php echo form_dropdown('year',$year,$thisYear,'class="form-control input-sm"')?>
    </div>
    <div class="col-sm-7">
        <input type="submit" name="search" class="btn btn-primary btn-sm" value="Go">
        <a href="<?php echo base_url().'printSummary/monthly/'.$thisMonth.'/'.$thisYear?>" class="btn btn-success btn-sm" target="_blank">Print</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th colspan="15" style="background: none;color: #000000;border: none">
                    <div style="font-weight: bold;text-transform: uppercase;font-size: 15px;">Month of <?php echo date('F Y',strtotime($thisYear.'-'.$thisMonth.'-01'))?></div>
                </th>
            </tr>
            </thead>
            <thead>
            <tr class="header">
                <th style="width: 15%;">Name</th>
                <th>Hours</th>
                <th>Gross</th>
                <th>Tax</th>
                <th>Kiwi</th>
                <th>ST Loan</th>
                <th>Flight</th>
                <th>Visa</th>
                <th>Accom</th>
                <th>Loans</th>
                <th>Trans</th>
                <th>Recruit</th>
                <th>Admin</th>
                <th>NETT</th>
                <th>PHP One</th>
                <th>PHP Two</th>
                <th>NZ ACC</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $hours = 0;
            $gross = 0;
            $kiwi = 0;
            $st_loan = 0;
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
            $monthYear = $thisYear.'-'.$thisMonth;

            if(count($staff)>0):
                foreach($staff as $v):
                    $balance = @$total_bal[$monthYear][$v->id];
                    $flight_debt = $balance['flight_debt'] > 0 ? '$'.number_format($balance['flight_debt'],2) : '&nbsp;';
                    $visa_debt = $balance['visa_debt'] > 0 ? '$'.number_format($balance['visa_debt'],2) : '&nbsp;';
                    $loan = $balance['balance'] > 0 ? '$'.number_format($balance['balance'],2) : '&nbsp;';
                    ?>
                    <tr>
                        <td style="text-align: left;"><?php echo $v->fname.' '.$v->lname;?></td>
                        <?php
                        $staff_data = @$monthly_pay[$v->id];
                        if(count($staff_data)>0):
                            if($staff_data['hours'] != 0):
                                $hours += $staff_data['hours'];
                                $gross += $staff_data['gross'];
                                $tax += $staff_data['tax'];
                                $flight += $staff_data['flight'];
                                $visa += $staff_data['visa'];
                                $kiwi += $staff_data['kiwi'];
                                $st_loan += $staff_data['st_loan'];
                                $accommodation += $staff_data['accommodation'];
                                $transport += $staff_data['transport'];
                                $recruit += $staff_data['recruit'];
                                $installment += $staff_data['installment'];
                                $admin += $staff_data['admin'];
                                $total_deduct = $staff_data['tax'] + $staff_data['installment'] + $staff_data['flight'] + $staff_data['visa'] + $staff_data['accommodation'] + $staff_data['transport'] + $staff_data['recruit'] + $staff_data['admin'];
                                $net = $staff_data['gross'] - $total_deduct;
                                $php_one = $net - ($staff_data['nz_account'] + $staff_data['account_two']);
                                $total_php_one += $staff_data['account_one'];
                                $php_converted = $staff_data['account_one'] * @$staff_data['rate_value'];
                                $account_two_converted = $staff_data['account_two'] * @$staff_data['rate_value'];
                                $total_php_two += $staff_data['account_two'];
                                $total_acc_nz += $staff_data['nz_account'];
                                $total_net += $staff_data['nett'];
                                ?>
                                <td class="column"><?php echo $staff_data['hours'];?></td>
                                <td class="column"><?php echo $staff_data['gross'] ? '$'.number_format($staff_data['gross'],2) : '';?></td>
                                <td class="column"><?php echo $staff_data['tax'] != 0 ? '$'.number_format($staff_data['tax'],2) : '';?></td>
                                <td class="column"><?php echo $staff_data['kiwi'] != 0 ? '$'.number_format($staff_data['kiwi'],2) : '';?></td>
                                <td class="column"><?php echo $staff_data['st_loan'] != 0 ? '$'.number_format($staff_data['st_loan'],2) : '';?></td>
                                <td class="column">
                                    <?php
                                    echo $staff_data['flight'] != 0 ? '$'.number_format($staff_data['flight'],2) : '';
                                    echo '<br/>';
                                    echo '<strong class="value-class">'.$flight_debt.'</strong>';
                                    ?>
                                </td>
                                <td class="column">
                                    <?php
                                    echo $staff_data['visa'] != 0 ? '$'.number_format($staff_data['visa'],2) : '';
                                    echo '<br/>';
                                    echo '<strong class="value-class">'.$visa_debt.'</strong>';
                                    ?>
                                </td>
                                <td class="column"><?php echo $staff_data['accommodation'] != 0 ? '$'.number_format($staff_data['accommodation'],2) : '';?></td>
                                <td class="column">
                                    <?php
                                    echo $staff_data['installment'] != 0 ? '$'.number_format($staff_data['installment'],2) : '';
                                    echo '<br/>';
                                    echo '<strong class="value-class">'.$loan.'</strong>';
                                    ?>
                                </td>
                                <td class="column"><?php echo $staff_data['transport'] != 0 ? '$'.number_format($staff_data['transport'],2) : '';?></td>
                                <td class="column"><?php echo $staff_data['recruit'] != 0 ? '$'.number_format($staff_data['recruit'],2) : '';?></td>
                                <td class="column"><?php echo $staff_data['admin'] != 0 ? '$'.number_format($staff_data['admin'],2) : '';?></td>
                                <td class="column"><?php echo $staff_data['distribution'] != 0 ? '$'.number_format($staff_data['distribution'],2) : '';?></td>
                                <td class="column">
                                    <?php
                                    if($staff_data['account_one'] != ''){
                                        echo '$'.number_format($staff_data['account_one'],2);
                                        echo '<br/>';
                                        echo '<strong class="value-class">';
                                        echo $staff_data['account_one'] > 0 ? $staff_data['symbols'].number_format($php_converted,2) : $staff_data['symbols'].'0.00';
                                        echo '</strong>';
                                    }else{
                                        echo '&nbsp;';
                                    }
                                    ?>
                                </td>
                                <td class="column">
                                    <?php
                                    if($staff_data['account_two'] != ''){
                                        echo '$'.number_format($staff_data['account_two'],2);
                                        echo '<br/>';
                                        echo '<strong class="value-class">';
                                        echo $account_two_converted > 0 ? $staff_data['symbols'].number_format($account_two_converted,2) : $staff_data['symbols'].'0.00';
                                        echo '</strong>';
                                    }else{
                                        echo '&nbsp;';
                                    }
                                    ?>
                                </td>
                                <td class="column">
                                    <?php
                                    echo $staff_data['nz_account'] != '' ? '$'.number_format($staff_data['nz_account'],2) : '';
                                    ?>
                                </td>
                            <?php
                            else:
                                for($i=0;$i<=15;$i++):
                                    ?>
                                    <td class="column"></td>
                                <?php
                                endfor;
                            endif;
                        else:
                            for($i=0;$i<=15;$i++):
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
                    <td colspan="17">No data was found.</td>
                </tr>
            <?php
            endif;
            ?>
            <tr class="danger">
                <td style="text-align: right;font-weight: bold;text-transform: uppercase;background: none;">Total</td>
                <td><?php echo $hours;?></td>
                <td><?php echo '$'.number_format($gross,2);?></td>
                <td><?php echo '$'.number_format($tax,2);?></td>
                <td><?php echo '$'.number_format($kiwi,2);?></td>
                <td><?php echo '$'.number_format($st_loan,2);?></td>
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
<div class="row">
    <div class="col-lg-4">
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th colspan="3" style="background: none;color: #000000;border: none">
                    <div style="font-weight: bold;text-transform: uppercase;font-size: 15px;">Tax Schedule</div>
                </th>
            </tr>
            </thead>
            <thead>
            <tr>
                <th>Name</th>
                <th>Gross</th>
                <th>Tax</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_tax = 0;
            $total_gross = 0;
            if(count($staff)>0):
                foreach($staff as $v):
                ?>
                <tr>
                    <td style="text-align: left;"><?php echo $v->fname.' '.$v->lname;?></td>
                    <?php
                    $staff_data = @$monthly_pay[$v->id];
                    if(count($staff_data)>0):
                        if($staff_data['hours'] != 0):
                            $total_tax += @$staff_data['tax'];
                            $total_gross += @$staff_data['gross'];
                            ?>
                            <td><?php echo @$staff_data['gross'] ? '$'.number_format(@$staff_data['gross'],2) : '&nbsp;'?></td>
                            <td><?php echo @$staff_data['tax'] ? '$'.number_format(@$staff_data['tax'],2) : '&nbsp;'?></td>
                            <?php
                        endif;
                    else:
                        ?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    <?php
                    endif;
                    ?>
                </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="3">No data was found.</td>
                </tr>
            <?php
            endif;
            ?>
            <tr class="danger">
                <td style="background: none;font-weight: bold;text-align: right;">Total</td>
                <td><?php echo '$'.number_format($total_gross,2);;?></td>
                <td><?php echo '$'.number_format($total_tax,2);?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
echo form_close();
?>
<style>
    table.table thead tr.header th{
        vertical-align: middle;
    }
    table.table tbody tr td{
        vertical-align: middle;
    }
</style>
<script>
    $(function(e){
       var checkEmpty = function(){
           $('.column').each(function(e){
              if(!$(this).html()){
                  $(this).css({
                     background:'rgba(188, 188, 188, 0.28)'
                  });
              }
           });
       };
        checkEmpty();
    });
</script>