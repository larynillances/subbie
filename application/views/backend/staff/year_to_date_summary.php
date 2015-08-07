<div class="row">
    <div class="col-sm-4">
        <h4>Summary for All Months YTD Pay</h4>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Month</th>
                <th>Pay Earned</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            if(count($wage_date) > 0){
                foreach($wage_date as $key=>$val){
                    ?>
                    <tr>
                        <td colspan="2" class="text-right success" style="text-align: right!important;"><?php echo $key?></td>
                    </tr>
                    <?php
                    if(count($val) > 0){
                        foreach($val as $index=>$row){
                            $data = @$total_paid[$key][$index];
                            $_value = 0;
                            if(count($data) > 0){
                                foreach($data as $v){
                                    $_value += $v;
                                }
                            }
                            $total += $_value;
                            ?>
                            <tr>
                                <td><?php echo $index?></td>
                                <td><?php echo '$ '.number_format($_value,2)?></td>
                            </tr>
                        <?php
                        }
                    }
                }
            }
            ?>
            <tr class="danger">
                <td style="text-align: right!important;"><strong>Total:</strong></td>
                <td><strong><?php echo '$ '.number_format($total,2);?></strong></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-sm-4">
        <h4>Details for All Months YTD Pay</h4>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Date</th>
                <th>Pay Earned</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            if(count($monthly_date) > 0){
                foreach($monthly_date as $year=>$month){
                    ?>
                    <tr>
                        <td colspan="2" class="text-right success" style="text-align: right!important;"><?php echo $year?></td>
                    </tr>
                    <?php
                    if(count($month) > 0){
                        foreach($month as $m=>$date){
                            ?>
                            <tr>
                                <td colspan="2" class="text-right info" style="text-align: right!important;"><?php echo $m?></td>
                            </tr>
                            <?php
                            $sub_total = 0;
                            if(count($date) > 0){
                                foreach($date as $day=>$week){
                                    $data = @$monthly_total_paid[$day];
                                    $total += $data;
                                    $sub_total += $data;
                                    ?>
                                    <tr>
                                        <td><?php echo date('d-M-Y',strtotime($day))?></td>
                                        <td><?php echo '$ '.number_format($data,2);?></td>
                                    </tr>
                                <?php
                                }
                            }
                            ?>
                            <tr class="danger">
                                <td style="text-align: right!important;"><strong>Sub Total:</strong></td>
                                <td><strong><?php echo '$ '.number_format($sub_total,2);?></strong></td>
                            </tr>
                        <?php
                        }
                    }
                }
            }
            ?>
            <tr class="warning">
                <td style="text-align: right!important;"><strong>Total:</strong></td>
                <td><strong><?php echo '$ '.number_format($total,2);?></strong></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-sm-4">
        <h4>Current Month Pay Period</h4>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Date</th>
                <th>Pay Earned</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="2" class="text-right info" style="text-align: right!important;"><?php echo date('F Y')?></td>
            </tr>
            <?php
            $total = 0;
            $current = @$current_month[date('Y')][date('F')];
            if(count($current) > 0){
                foreach($current as $date=>$week){
                    $sub_total = 0;
                    $data = @$monthly_total_paid[$date];
                    $total += $data;
                    $sub_total += $data;
                    ?>
                    <tr>
                        <td><?php echo date('d-M-Y',strtotime($date))?></td>
                        <td><?php echo '$ '.number_format($data,2);?></td>
                    </tr>
                <?php
                }
            }
            ?>
            <tr class="warning">
                <td style="text-align: right!important;"><strong>Total:</strong></td>
                <td><strong><?php echo '$ '.number_format($total,2);?></strong></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>