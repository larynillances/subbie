<div class="modal-body">
    <table class="table table-colored-header table-responsive">
        <thead>
        <tr>
            <th>Rate Name</th>
            <th>Rate Cost</th>
            <th>Start Use</th>
            <th>End Use</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
        if(count(@$staff_rate[$id]) > 0){
            foreach(@$staff_rate[$id] as $rate){
                $current = $rate->end_use == '0000-00-00' ? 'class="current-rate"' : '';
                ?>
                <tr <?php echo $current;?> >
                    <td><?php echo $rate->rate_name;?></td>
                    <td><?php echo '$ '.$rate->rate_cost;?></td>
                    <td><?php echo date('d/m/Y',strtotime($rate->start_use));?></td>
                    <td><?php echo $rate->end_use != '0000-00-00' ? date('d/m/Y',strtotime($rate->end_use)) : 'Present';?></td>
                </tr>
                <?php
            }
        }else{
            ?>
            <tr>
                <td colspan="4">No data have been found.</td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <a href="<?php echo base_url('payRatePeriods?p=1')?>" class="btn btn-sm btn-success">Print All</i></a>
    <a href="<?php echo base_url('payRatePeriods?p=1&id=' . $id)?>" class="btn btn-sm btn-primary">Print Rate</i></a>
    <input type="button" name="button" value="Cancel" class="btn btn-sm btn-default" data-dismiss="modal">
</div>
<style>
    .table-colored-header .current-rate td{
        background: #74b375;
    }
</style>
<script>
    $(function(){
        $('.btn-success,.btn-primary').click(function(e){
            e.preventDefault();
            $('.modal').modal('hide');
            var myWindow = window.open(
                this.href,
                'Pay Rate Periods'
            );

            $(myWindow).load(function(){
                location.replace(bu + 'wageManage');
            });
        })
    })
</script>