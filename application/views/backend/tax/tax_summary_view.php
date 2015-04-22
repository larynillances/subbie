<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="form-group">
    <label class="col-sm-1 control-label" >Year:</label>
    <div class="col-sm-2">
        <?php echo form_dropdown('year',$year,$this_year,'class="form-control input-sm"')?>
    </div>
    <div class="col-sm-9">
        <input type="submit" name="search" class="btn btn-primary" value="Go">
    </div>
</div>
<?php
echo form_close()
;?>
<div class="row">
    <div class="col-lg-5">
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Month</th>
                <th>Total Gross</th>
                <th>Tax</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total_gross = 0;
            $total_tax = 0;
            if(count($month) >0):
                foreach($month as $m_key=>$m_val):
                    $year_month = $this_year.'-'.$m_key;
                    $data = @$tax_summary[$year_month];
                    $total_gross += $data['gross'];
                    $total_tax += $data['tax'];
                ?>
                <tr>
                    <td><?php echo $m_val;?></td>
                    <td><?php echo $data['gross'] ? '$'.number_format($data['gross'],2) : '&nbsp;';?></td>
                    <td><?php echo $data['tax'] ? '$'.number_format($data['tax'],2) : '&nbsp;';?></td>
                </tr>
                <?php
                endforeach;
            endif;
            ?>
            <tr class="danger">
                <td style="background: none;font-weight: bold;text-align: right">Total</td>
                <td><?php echo '$'.number_format($total_gross,2);?></td>
                <td><?php echo '$'.number_format($total_tax,2);?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>