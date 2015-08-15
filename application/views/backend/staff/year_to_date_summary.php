<div class="row">
    <div class="col-sm-4">
        <h4>Summary for All Months YTD Pay</h4>
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Month</th>
                <th>Gross</th>
                <th>Nett</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $nett_total = 0;
            $gross_total = 0;
            if(count($wage_date) > 0){
                foreach($wage_date as $key=>$val){
                    ?>
                    <tr>
                        <td colspan="3" class="text-right success" style="text-align: right!important;"><?php echo $key?></td>
                    </tr>
                    <?php
                    if(count($val) > 0){
                        foreach($val as $index=>$row){
                            $data = @$total_paid[$key][$index];
                            $nett_value = 0;
                            $gross_value = 0;
                            if(count($data) > 0){
                                foreach($data as $v){
                                    $nett_value += $v['distribution'];
                                    $gross_value += $v['gross'];
                                }
                            }
                            $nett_total += $nett_value;
                            $gross_total += $gross_value;
                            ?>
                            <tr>
                                <td><?php echo $index?></td>
                                <td><?php echo '$ '.number_format($gross_value,2)?></td>
                                <td><?php echo '$ '.number_format($nett_value,2)?></td>
                            </tr>
                        <?php
                        }
                    }
                }
            }
            ?>
            <tr class="danger">
                <td style="text-align: right!important;"><strong>Total:</strong></td>
                <td><strong><?php echo '$ '.number_format($gross_total,2);?></strong></td>
                <td><strong><?php echo '$ '.number_format($nett_total,2);?></strong></td>
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
                <th>Gross</th>
                <th>Nett</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $gross_total = 0;
            $nett_total = 0;
            if(count($monthly_date) > 0){
                foreach($monthly_date as $year=>$month){
                    ?>
                    <tr>
                        <td colspan="3" class="text-right success" style="text-align: right!important;"><?php echo $year?></td>
                    </tr>
                    <?php
                    if(count($month) > 0){
                        foreach($month as $m=>$date){
                            ?>
                            <tr>
                                <td colspan="3" class="text-right info" style="text-align: right!important;"><?php echo $m?></td>
                            </tr>
                            <?php
                            $nett_sub_total = 0;
                            $gross_sub_total = 0;
                            if(count($date) > 0){
                                foreach($date as $day=>$week){
                                    $data = @$monthly_total_paid[$day];
                                    $nett_total += $data['distribution'];
                                    $gross_total += $data['gross'];
                                    $nett_sub_total += $data['distribution'];
                                    $gross_sub_total += $data['gross'];
                                    ?>
                                    <tr>
                                        <td><?php echo date('d-M-Y',strtotime($day))?></td>
                                        <td><?php echo '$ '.number_format($data['gross'],2);?></td>
                                        <td><?php echo '$ '.number_format($data['distribution'],2);?></td>
                                    </tr>
                                <?php
                                }
                            }
                            ?>
                            <tr class="danger">
                                <td style="text-align: right!important;"><strong>Sub Total:</strong></td>
                                <td><strong><?php echo '$ '.number_format($gross_sub_total,2);?></strong></td>
                                <td><strong><?php echo '$ '.number_format($nett_sub_total,2);?></strong></td>
                            </tr>
                        <?php
                        }
                    }
                }
            }
            ?>
            <tr class="warning">
                <td style="text-align: right!important;"><strong>Total:</strong></td>
                <td><strong><?php echo '$ '.number_format($gross_total,2);?></strong></td>
                <td><strong><?php echo '$ '.number_format($nett_total,2);?></strong></td>
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
                <th>Gross</th>
                <th>Nett</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="3" class="text-right info" style="text-align: right!important;"><?php echo date('F Y')?></td>
            </tr>
            <?php
            $nett_total = 0;
            $gross_total = 0;
            $current = @$current_month[date('Y')][date('F')];
            if(count($current) > 0){
                foreach($current as $date=>$week){
                    $sub_total = 0;
                    $data = @$monthly_total_paid[$date];
                    $nett_total += $data['distribution'];
                    $gross_total += $data['gross'];
                    ?>
                    <tr>
                        <td><?php echo date('d-M-Y',strtotime($date))?></td>
                        <td><?php echo '$ '.number_format($data['gross'],2);?></td>
                        <td><?php echo '$ '.number_format($data['distribution'],2);?></td>
                    </tr>
                <?php
                }
            }
            ?>
            <tr class="warning">
                <td style="text-align: right!important;"><strong>Total:</strong></td>
                <td><strong><?php echo '$ '.number_format($gross_total,2);?></strong></td>
                <td><strong><?php echo '$ '.number_format($nett_total,2);?></strong></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>