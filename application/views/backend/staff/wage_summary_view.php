<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="form-group">
    <label class="col-sm-1 control-label" >Date:</label>
    <div class="col-sm-2">
        <?php echo form_dropdown('month',$month,$thisMonth,'class="form-control input-sm"')?>
    </div>
    <div class="col-sm-1">
        <?php echo form_dropdown('year',$year,$thisYear,'class="form-control input-sm"')?>
    </div>
    <div class="col-sm-2">
        <?php echo form_dropdown('project_type',$project_type,$thisProject,'class="form-control input-sm"')?>
    </div>
    <div class="col-sm-5">
        <input type="submit" name="search" class="btn btn-primary" value="Go">
        <a href="<?php echo base_url().'printSummary/wage/'.$thisMonth.'/'.$thisYear?>" class="btn btn-success" target="_blank">Print</a>
    </div>
</div>
<table class="table table-colored-header table-responsive table-striped table-fixed-header">
    <thead class="header">
    <tr>
        <th>Date</th>
        <th>Name</th>
        <th>Hours</th>
        <th>Gross</th>
        <th>Tax</th>
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
            $_date = new DateTime($v);
            $week = $_date->format("W");
            $_year = $_date->format("Y");
            $_what_date = $week == 30 && $_year == 2015 ? date('d-m-Y',strtotime('+5 days'.$v)) : date('d-m-Y',strtotime('+6 days'.$v));
            $this_data = @$wage_data[$this_date];
            $tr_style = count($this_data) == 1 ? 'style="border-bottom: 2px solid #000000"' : '';
            ?>
            <tr <?php echo $tr_style;?> >
                <td rowspan="<?php echo count($this_data)?>" style="vertical-align: middle;border-bottom: 2px solid #000000">
                    <?php
                    echo date('D',strtotime($v)).' ('.date('d/m/y',strtotime($v)).') to <br/>';
                    echo date('D',strtotime($_what_date)).' ('.date('d/m/y',strtotime($_what_date)).')<br/>';
                    echo '<strong style="white-space: nowrap;">[Week '.$week.']</strong>';
                    ;?>
                </td>
            <?php
            $ref = 0;

            if(count($this_data) >0):
                foreach($this_data as $val):
                    $date = new DateTime($this_date);
                    $week = $date->format("W");

                    $last_pay = @$last_pay_data[$val['id']];
                    $flag_staff = $last_pay['last_week'] == $week ? 'style="background:#FFDD90!important"' : '';
                    $has_holiday_pay = $last_pay['last_week'] == $week ? 1 : 0;
                    $is_final_pay = $last_pay['last_week'] == $week ? '&nbsp;<strong class="tooltip-class" title="Final Pay Period">?</strong>' : '';
                    $final_pay_url = '<a href="'.base_url().'printFinalPaySlip/' . $val['id'] . '/' . $this_date . '/' . $week .'?v=1&type=2" class="payslip-view-btn" id="'.$val['id'].'" data-type="1" data-value="'.$this_date.'">'.$val['name'].'</a>';

                    $style = (count($this_data) - 1) == $ref ? 'style="border-bottom: 2px solid #000000!important"' : '';
                    echo $ref != 0 ? '<tr '.$style.'>' : '';
                        ?>
                    <td class="column details-column" <?php echo $flag_staff;?>>
                        <?php
                        $tooltip = 'class="tooltip-class" data-toggle="tooltip" data-placement="top" title="IRD No.: '.$val['ird_num'].'"';
                        $url = '<a href="'.base_url().'printPaySlip/' . $val['id'] . '/' . $this_date . '/' . $week . '?view=1&type=1" class="payslip-view-btn" id="'.$val['id'].'" data-type="2" data-value="'.$this_date.'">'.$val['name'].'</a>';
                        if($val['wage_type'] == 1){
                            $name = $val['hours'] != 0 && (@$val['start_use'] && $this_date <= date('Y-m-d')) ? $url : $val['name'];
                        }else{
                            $name = $val['hours'] != 0 && @$val['start_use'] ? ($last_pay['last_week'] == $week ? $final_pay_url : $url) : $val['name'];
                        }
                        ?>
                        <table style="width: 100%;">
                            <tr>
                                <td style="white-space: nowrap;text-align: left;"><?php echo '<strong>'.$name.'</strong>'.$is_final_pay;?></td>
                                <td style="white-space: nowrap;text-align: right;padding-left: 7px;"><?php echo '<span '.$tooltip.'>[ IRD: '.$val['ird_num'].' ]</span>';?></td>
                            </tr>
                            <?php
                            if($has_holiday_pay){
                                ?>
                                <tr>
                                    <td style="white-space: nowrap;text-align: left;"><strong>Holiday Pay:</strong></td>
                                    <td style="white-space: nowrap;text-align: right;padding-left: 7px;"><strong style="color: #0000ff"><?php echo '$'.number_format($last_pay['annual_leave_pay'],2)?></strong></td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap;text-align: left;"><strong>Holiday PAYE:</strong></td>
                                    <td style="white-space: nowrap;text-align: right;padding-left: 7px;"><strong style="color: #0000ff"><?php echo '$'.number_format($last_pay['annual_tax'],2)?></strong></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </td>
                    <?php
                    if($val['wage_type'] != 1):
                        if($val['hours'] != 0 && @$val['start_use']):
                            $php_two_convert = $val['account_two']* $val['rate_value'];
                            $php_one_convert = $val['account_one']* $val['rate_value'];
                        ?>
                            <td class="column text-center" <?php echo $flag_staff;?>>
                                <?php
                                echo number_format($val['hours'],2);
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo '$'.number_format($val['gross'],2);
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo '$'.number_format($val['tax'],2);
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo $val['has_kiwi'] ? '$'.number_format($val['kiwi'],2) : '';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo $val['has_st_loan'] ? '$'.number_format($val['st_loan'],2) : '';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo $val['flight'] != '' ? '$'.$val['flight'].'<br/>' : '';
                                $flight_debt =  @$total_bal[$v][$val['id']]['flight_debt'];
                                echo '<strong class="value-class">';
                                echo $flight_debt != 0 ? '$'.number_format($flight_debt,2) : '';
                                echo '</strong>';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo $val['visa'] != '' ? '$'.$val['visa'].'<br/>' : '';
                                $visa_debt =  @$total_bal[$v][$val['id']]['visa_debt'];
                                echo '<strong class="value-class">';
                                echo $visa_debt != 0 ? '$'.number_format($visa_debt,2) : '';
                                echo '</strong>';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo $val['accommodation'] ? '$'.number_format($val['accommodation'],2) : '';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>><?php echo $val['transport'] ? '$'.number_format($val['transport'],2) : '';?></td>
                            <td class="column" <?php echo $flag_staff;?>><?php echo $val['recruit'] ? '$'.number_format($val['recruit'],2) : '';?></td>
                            <td class="column" <?php echo $flag_staff;?>><?php echo $val['admin'] ? '$'.number_format($val['admin'],2) : '';?></td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                $nett = floatval(str_replace('$','',$val['nett']));
                                echo $nett > 0 ? '$'.number_format($val['nett'],2) : '<strong class="value-class">$'.number_format($val['nett']).'</strong>';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                $thisBalance =  @$total_bal[$v][$val['id']]['balance'];
                                echo $thisBalance > 0 ? ($val['deduction'] ? $val['deduction'].'<br/>' : '') : '';
                                echo '<strong class="value-class">';
                                echo $thisBalance != 0 ? '$'.$thisBalance : '';
                                echo '</strong>';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                $distribution = floatval(str_replace('$','',$val['distribution']));
                                echo $distribution > 0 ? '$'.number_format($val['distribution'],2) : '<strong class="value-class">$'.number_format($val['distribution'],2).'</strong>';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
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
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                if($val['account_two'] != ''){
                                    $account_two = $val['account_two'] != '' ? '$'.number_format($val['account_two'],2).'<br/>' : '$0.00'.'<br/>';
                                    echo '<strong>'.$account_two;
                                    echo '<span class="value-class">';
                                    echo $php_two_convert > 0 ? $val['symbols'].number_format($php_two_convert,2) : $val['symbols'].'0.00';
                                    echo '</span></strong>';
                                }
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                $nz_account = $val['nz_account'] != '' ? '$'.number_format($val['nz_account'],2) : '';
                                echo '<strong>'. $nz_account .'</strong><br/>';
                                ?>
                            </td>
                            <?php
                            else:
                                for($i=0;$i<=16;$i++):
                                    ?>
                                    <td>&nbsp;</td>
                                <?php
                                endfor;
                            endif;
                    else:
                        if($val['hours'] != 0
                            && (@$val['start_use'] && $this_date <= date('Y-m-d'))):
                            $php_two_convert = $val['account_two']* $val['rate_value'];
                            $php_one_convert = $val['account_one']* $val['rate_value'];
                            ?>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo number_format($val['hours'],2);
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo '$'.number_format($val['gross'],2);
                                ?>
                            </td>
                            <td class="column">
                                <?php
                                echo '$'.number_format($val['tax'],2);
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo $val['has_kiwi'] ? '$'.number_format($val['kiwi'],2) : '';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo $val['has_st_loan'] ? '$'.number_format($val['st_loan'],2) : '';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo $val['flight'] != '' ? '$'.$val['flight'].'<br/>' : '';
                                $flight_debt =  @$total_bal[$v][$val['id']]['flight_debt'];
                                echo '<strong class="value-class">';
                                echo $flight_debt != 0 ? '$'.number_format($flight_debt,2) : '';
                                echo '</strong>';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo $val['visa'] != '' ? '$'.$val['visa'].'<br/>' : '';
                                $visa_debt =  @$total_bal[$v][$val['id']]['visa_debt'];
                                echo '<strong class="value-class">';
                                echo $visa_debt != 0 ? '$'.number_format($visa_debt,2) : '';
                                echo '</strong>';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                echo $val['accommodation'] ? '$'.number_format($val['accommodation'],2) : '';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php echo $val['transport'] ? '$'.number_format($val['transport'],2) : '';?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php echo $val['recruit'] ? '$'.number_format($val['recruit'],2) : '';?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php echo $val['admin'] ? '$'.number_format($val['admin'],2) : '';?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                $nett = floatval(str_replace('$','',$val['nett']));
                                echo $nett > 0 ? '$'.number_format($val['nett'],2) : '<strong class="value-class">$'.number_format($val['nett']).'</strong>';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                $thisBalance =  @$total_bal[$v][$val['id']]['balance'];
                                echo $thisBalance > 0 ? ($val['deduction'] ? $val['deduction'].'<br/>' : '') : '';
                                echo '<strong class="value-class">';
                                echo $thisBalance != 0 ? '$'.$thisBalance : '';
                                echo '</strong>';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                $distribution = floatval(str_replace('$','',$val['distribution']));
                                echo $distribution > 0 ? '$'.number_format($val['distribution'],2) : '<strong class="value-class">$'.number_format($val['distribution'],2).'</strong>';
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
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
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                if($val['account_two'] != ''){
                                    $account_two = $val['account_two'] != '' ? '$'.number_format($val['account_two'],2).'<br/>' : '$0.00'.'<br/>';
                                    echo '<strong>'.$account_two;
                                    echo '<span class="value-class">';
                                    echo $php_two_convert > 0 ? $val['symbols'].number_format($php_two_convert,2) : $val['symbols'].'0.00';
                                    echo '</span></strong>';
                                }
                                ?>
                            </td>
                            <td class="column" <?php echo $flag_staff;?>>
                                <?php
                                $nz_account = $val['nz_account'] != '' ? '$'.number_format($val['nz_account'],2) : '';
                                echo '<strong>'. $nz_account .'</strong><br/>';
                                ?>
                            </td>
                        <?php
                        else:
                            for($i=0;$i<=16;$i++):
                                ?>
                                <td class="column">&nbsp;</td>
                            <?php
                            endfor;
                        endif;
                    endif;
                    $ref++;
                    echo $ref != 0 ? '</tr>' : '';
                endforeach;
            else:
                echo '<td class="column" colspan="18" style="border-bottom: 2px solid #000000">';
                echo '<strong>No pay has been found for this week period.</strong>';
                echo '</td>';
                echo '</tr>';
            endif;
        endforeach;
    else:
        ?>
        <tr>
            <td colspan="19" style="text-align: center">
                No data has been found.
            </td>
        </tr>
    <?php
    endif;
    ?>
    </tbody>
</table>
<?php
echo form_close();
?>
<style>
    .header-title{
        background: #484848;
        color: #ffffff;
        padding: 2px;
    }
    .table-fixed-header > thead.header-copy > .headerTr{
        width: 1140px!important;
        vertical-align: middle;
    }
    .table-fixed-header tr td.details-column{
        vertical-align: middle;
    }
</style>
<script>
    $(function(e){
        var checkEmpty = function(){
            $('.column').each(function(e){
                if($(this).html() == ' '){
                    $(this).css({
                        background:'#d2d2d2!important'
                    });
                }
            });
        };
        checkEmpty();
        $('.addWageBtn').click(function(e){
            $(this).newForm.addNewForm({
                title: 'Add Employee Wage',
                url: '<?php echo base_url().'addWage'?>',
                toFind:'.form-horizontal'
            });
        });

        // make the header fixed on scroll
        //$('.table-fixed-header').fixedHeader();
        //$('.table-fixed-header').scrollTableBody({rowsToDisplay:30});
        /*$('.table-fixed-header').stickyTableHeaders();*/
        $('.payslip-view-btn').click(function(e){
            e.preventDefault();
            var url = this.href;
            $(this).newForm.addLoadingForm();
            if($(this).data('type') == 1){
                $.post(bu + 'printFinalPaySlip/' + this.id + '/' + $(this).data('value'),{save:1},function(data){
                    if(data){
                        location.replace(url);
                    }
                });
            }else{
                $.post(bu + 'printPaySlip/' + this.id + '/' + $(this).data('value'),{save:1},function(data){
                    if(data){
                        location.replace(url);
                    }
                });
            }
        });
    })
</script>