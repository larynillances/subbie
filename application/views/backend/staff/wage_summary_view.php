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
        <input type="submit" name="search" class="btn btn-primary" value="Go">
        <a href="<?php echo base_url().'printSummary/wage/'.$thisMonth.'/'.$thisYear?>" class="btn btn-success" target="_blank">Print</a>
    </div>
</div>
<table class="table table-colored-header table-responsive table-striped table-fixed-header">
    <thead class="header">
    <tr class="headerTr">
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
                <td rowspan="<?php echo count($this_data)?>"><?php echo date('d-m-Y',strtotime('+6 days'.$v));?></td>
            <?php
            $ref = 0;
            if(count($this_data) >0):
                foreach($this_data as $val):
                    echo $ref != 0 ? '<tr>' : '';
                        ?>
                    <td class="column">
                        <?php
                        $tooltip = 'class="tooltip-class" data-toggle="tooltip" data-placement="top" title="Tax No.: '.$val['tax_number'].'"';
                        $name = $val['hours'] != 0 ? '<a href="'.base_url().'printPaySlip/'.$val['id'].'/'.$this_date.'" target="_blank">'.$val['name'].'</a>' : $val['name'];
                        echo $val['name'] != '' ? $name.'<br/><strong '.$tooltip.'>Tax: ######</strong>' :'';
                        ?>
                    </td>
                    <?php
                    if($val['hours'] != 0):
                        $php_two_convert = $val['account_two']* $val['rate_value'];
                        $php_one_convert = $val['account_one']* $val['rate_value'];
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
                            $thisBalance =  @$total_bal[$v][$val['id']]['balance'];
                            echo $thisBalance > 0 ? ($val['deduction'] ? $val['deduction'].'<br/>' : '') : '';
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
        $('.table-fixed-header').scrollTableBody({rowsToDisplay:10});
    })
</script>