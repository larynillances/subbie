<div class="container-fluid">
    <div class="form-horizontal">
        <div class="row">
            <div class="col-sm-12" style="padding: 5px;">
                <label class="control-label col-sm-1" style="margin-left: -20px;">Year:</label>
                <div class="col-sm-1">
                    <?php echo form_dropdown('_year',$year,@$filter_data->_year,'class="form-control input-sm year select-dp" style="width:120%;"')?>
                </div>
                <label class="control-label col-sm-1">Period:</label>
                <div class="col-sm-2">
                    <?php echo form_dropdown('_period',$period,@$filter_data->_period,'class="form-control input-sm period select-dp"')?>
                </div>
                <label class="control-label col-sm-1">Month:</label>
                <div class="col-sm-2">
                    <?php echo form_dropdown('_month',$month,@$filter_data->_month,'class="form-control input-sm month select-dp"')?>
                </div>
                <div class="col-sm-2">
                    <input type="text" class="form-control input-sm filter-name" name="filter" placeholder="Employee's Name" value="<?php echo @$filter_data->filter;?>">
                </div>
            </div>
        </div>
    </div><br/>
    <div class="row">
        <div class="col-sm-10">
            <table class="table table-colored-header table-responsive">
                <thead>
                <tr>
                    <th>Employee's Name</th>
                    <th>Reason</th>
                    <th>CR/DR</th>
                    <th>Amount</th>
                </tr>
                </thead>
                <tbody>
                <?php
                if(count($adjustment) > 0){
                    foreach($adjustment as $year=>$pay){
                        ?>
                        <tr>
                            <td colspan="4" class="success" style="text-align: left!important;"><strong><?php echo $year;?></strong></td>
                        </tr>
                        <?php
                        $count = 1;
                        if(count($pay) > 0){
                            foreach($pay as $pay_period=>$staff_data){
                                ?>
                                <tr>
                                    <td colspan="4" class="info text-center">
                                        <strong><?php echo 'Pay Period #' . $count . ' ' . $pay_period;?></strong>
                                    </td>
                                </tr>
                                <?php
                                $pay_subtotal = array();
                                if(count($staff_data) > 0){
                                    foreach($staff_data as $staff_name => $data){
                                        $rowspan = count($data) > 0 ? count($data) : 1;
                                        ?>
                                        <tr>
                                        <td rowspan="<?php echo $rowspan;?>"><?php echo $staff_name;?></td>
                                        <?php
                                        $ref = 0;
                                        $subtotal = array();
                                        if(count($data) > 0){
                                            foreach($data as $key=>$val){
                                                $amount = str_replace('-','',$val->amount);
                                                if($val->adjustment_type_id == 1){
                                                    @$subtotal[$staff_name][$pay_period] -= floatval($amount);
                                                    @$pay_subtotal[$pay_period] -= floatval($amount);
                                                }
                                                else{
                                                    @$subtotal[$staff_name][$pay_period] += floatval($amount);
                                                    @$pay_subtotal[$pay_period] += floatval($amount);
                                                }
                                                ?>
                                                <td style="text-align: left!important;"><?php echo $val->notes?></td>
                                                <td><?php echo $val->adjustment_type?></td>
                                                <td><?php echo number_format($val->amount,2)?></td>
                                                <?php
                                                $ref++;
                                                echo $ref != 0 ? '</tr>' : '';
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="3" class="danger" style="text-align: right!important;">Sub-Total:</td>
                                            <td>
                                                <?php
                                                $subtotal_val = @$subtotal[$staff_name][$pay_period];
                                                echo '<strong>'.number_format($subtotal_val,2).'</strong>';
                                                ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                $count++;
                                ?>
                                <tr>
                                    <td colspan="3" class="warning" style="text-align: right!important;"><strong>Pay Period Sub-Total:</strong></td>
                                    <td>
                                        <?php
                                        $pay_subtotal_val = @$pay_subtotal[$pay_period];
                                        echo '<strong>'.number_format($pay_subtotal_val,2).'</strong>';
                                        ?>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                    }
                }
                else{
                    ?>
                    <tr>
                        <td colspan="4">No data has found.</td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(function(e){
        $('.select-dp').change(function(e){
            var is_year = $(this).hasClass('year');
            var is_month = $(this).hasClass('month');
            var data = $("input[value!=''],select[value!='']").serializeArray();
            data.push({name:'submit',value:1});

            if(is_year){
                data.push({name:'type',value:1});
            }
            else if(is_month){
                data.push({name:'type',value:2});
            }
            else{
                data.push({name:'type',value:3});
            }
            $(this).newForm.addLoadingForm();
            $.post(bu + 'adjustmentsReport', data,function(data){
                location.reload();
            })
        });
        $('.filter-name').focusout(function (e) {
            var data = $("input[value!=''],select[value!='']").serializeArray();
            data.push({name:'submit',value:1},{name:'type', value:1});
            $(this).newForm.addLoadingForm();
            $.post(bu + 'adjustmentsReport', data,function(data){
                location.reload();
            })
        });
    });
</script>