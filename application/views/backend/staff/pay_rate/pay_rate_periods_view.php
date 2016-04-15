<?php
echo form_open('');
?>
<div class="row">
    <div class="col-sm-9">
        <div class="col-sm-2">
            <?php echo form_dropdown('staff_status',$staff_status,$status,'class="form-control input-sm"')?>
        </div>
        <div class="col-sm-3">
            <?php echo form_dropdown('project_type',$project_type,$project,'class="form-control input-sm"')?>
        </div>
        <input type="submit" class="btn btn-sm btn-success" name="go" value="Go">
        <a href="<?php echo base_url('payRatePeriods?p=1')?>" target="_blank" class="pull-right btn btn-sm btn-primary"><i class="glyphicon glyphicon-print"></i> Print All</a>
    </div>
</div><br/>
<?php
echo form_close()
?>
<div class="row">
    <div class="col-sm-9">
        <table class="table table-colored-header table-responsive table-hover">
            <thead>
            <tr>
                <th>Employee Name</th>
                <th>Status</th>
                <th>Rate Name</th>
                <th>Rate Cost</th>
                <th>Start Use</th>
                <th>End Use</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($staff_list) > 0){
                foreach($staff_list as $val){
                    $rate_data = @$staff_rate[$val->id];
                    ?>
                    <tr>
                        <td <?php echo count($rate_data) > 0 ? 'rowspan="'.count($rate_data).'"' : '';?>><?php echo $val->name;?></td>
                        <td <?php echo count($rate_data) > 0 ? 'rowspan="'.count($rate_data).'"' : '';?>><?php echo $val->staff_status;?></td>
                    <?php
                    $ref = 0;
                    if(count($rate_data) > 0){
                        foreach($rate_data as $rate){
                            echo $ref != 0 ? '<tr>' : '';
                            ?>
                                <td><?php echo $rate->rate_name;?></td>
                                <td><?php echo '$ '.$rate->rate_cost;?></td>
                                <td><?php echo date('d/m/Y',strtotime($rate->start_use));?></td>
                                <td><?php echo $rate->end_use != '0000-00-00' ? date('d/m/Y',strtotime($rate->end_use)) : 'Present';?></td>
                                <td>
                                    <a href="#" class="edit-rate-btn" data-value="<?php echo $val->name.' Pay Rate Period'?>" id="<?php echo $rate->id;?>"><i class="glyphicon glyphicon-pencil"></i></a>&nbsp;
                                    <a href="<?php echo base_url('payRatePeriods?p=1&id=' . $val->id)?>" target="_blank"><i class="glyphicon glyphicon-print"></i></a>
                                </td>
                            <?php
                            $ref++;
                            echo $ref != 0 ? '' : '</tr>';
                        }
                    }else{
                        ?>
                            <td colspan="6">No data found.</td>
                        </tr>
                    <?php
                    }
                }
            }else{
                ?>
                <tr>
                    <td colspan="7">No data found.</td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('.edit-rate-btn').click(function(e){
            e.preventDefault();
            $(this).modifiedModal({
                url: bu + 'payRatePeriods/' + this.id,
                type: 'small',
                title: $(this).data('value')
            });
        })
    });
</script>