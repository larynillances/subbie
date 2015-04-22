<?php
if(count($job_name) >0):
    foreach($job_name as $jv):
        $address = (object)json_decode($jv->address);
    ?>
    <div class="row">
        <div class="col-md-1" style="white-space: nowrap;"><strong>Job Name:</strong></div>
        <div class="col-md-10">
            <?php echo $jv->job_ref.' - '.$address->number.' '.$address->name.', '.$address->suburb.', '.$address->city;?>
        </div>
    </div><br/>
    <?php
    endforeach;
endif;
?>
<table class="table table-colored-header">
    <thead>
    <tr>
        <th rowspan="2" style="vertical-align: middle;width: 10%">Date</th>
        <th colspan="3">Direct Labor</th>
        <th colspan="5">Direct Material</th>
        <th colspan="4">Applied Overhead</th>
        <th colspan="4">Traveling</th>
    </tr>
    <tr>
        <th style="width: 7%;">Hours</th>
        <th style="width: 7%;">Rate</th>
        <th style="width: 7%;">Total</th>
        <th style="width: 7%;">Order #</th>
        <th style="width: 7%;">Supplier</th>
        <th style="width: 7%;">Qty</th>
        <th style="width: 7%;">Cost Per<br/>Unit</th>
        <th style="width: 7%;">Total</th>
        <th style="width: 7%;">Basis</th>
        <th style="width: 7%;">Qty</th>
        <th style="width: 7%;">Rate</th>
        <th style="width: 7%;">Total</th>
        <th>Days</th>
        <th>Cost</th>
        <th>People</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $total_hours = 0;
    $total_labor = 0;
    if(count($labor) >0):
        foreach($labor as $date=>$job_id):
            $maxRow = 15 - count($job_id[$this->uri->segment(2)]);
            $labor_total = $hours[$date][$this->uri->segment(2)] * $job_id[$this->uri->segment(2)]['rate'];
            $total_labor += $labor_total;
            $total_hours += $hours[$date][$this->uri->segment(2)];
            ?>
            <tr>
                <td><?php echo date('d-M-y',strtotime($date));?></td>
                <td><?php echo $hours[$date][$this->uri->segment(2)];?></td>
                <td><?php echo '$'.$job_id[$this->uri->segment(2)]['rate'];?></td>
                <td><?php echo '$'.number_format($labor_total,2);?></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <?php
        endforeach;
    endif;
    ?>
    <?php
    $len = 10;
    $maxLen = $len - count($labor);
    for($i=1;$i<=$maxLen;$i++):
        ?>
        <tr>
            <?php
            for($k=1;$k<=17;$k++):
                ?>
                <td>&nbsp;</td>
                <?php
            endfor;
            ?>
        </tr>
        <?php
    endfor;
    ?>
    <tr class="danger">
        <td><strong>Total</strong></td>
        <td><?php echo $total_hours;?></td>
        <td>&nbsp;</td>
        <td><?php echo '$'.number_format($total_labor,2);?></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    </tbody>
</table>
<div class="row">
    <div class="col-lg-12">
        <a href="<?php echo base_url().'jobList'?>" class="btn btn-primary">Back</a>
    </div>
</div>