<?php
echo form_open('','class="form-horizontal" role="form"');
$type = array(0 => 'Monthly', 1 => 'Yearly');
?>
<div class="row">
    <div class="col-lg-9">
        <div class="form-group">
            <div class="col-sm-2">
                <?php echo form_dropdown('type',$type,'','class="form-control input-sm action_type"')?>
            </div>

            <div class="col-sm-2 month-class">
                <?php echo form_dropdown('month',$month,$whatMonth,'class="form-control input-sm"')?>
            </div>
            <div class="col-sm-2">
                <?php echo form_dropdown('year',$year,$whatYear,'class="form-control input-sm"')?>
            </div>
            <div class="col-sm-4">
                <?php echo form_dropdown('client',$client,$client_key,'class="form-control input-sm"')?>
            </div>
            <div class="col-sm-2">
                <input type="submit" name="submit" class="btn btn-primary" value="Go">
            </div>
        </div>
    </div>
</div>
<?php
echo form_close();
?>
<div class="row">
    <div class="col-lg-9">
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Client</th>
                <th>Code</th>
                <th>Credit Ref.</th>
                <th>File Name</th>
                <th>Amount</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $total = 0;
            if(count($invoice_list) >0):
                foreach($invoice_list as $key=>$val):
                    ?>
                    <tr>
                        <td colspan="6" class="success" style="text-align: right;"><?php echo $key;?></td>
                    </tr>
                    <?php
                    if(count($val) > 0 ):
                        foreach($val as $iv):
                            $inv_ref = explode('_',$iv->file_name);
                            $total += $iv->original_amount;
                        ?>
                        <tr>
                            <td><?php echo $iv->client_name;?></td>
                            <td><?php echo $iv->client_code;?></td>
                            <td><?php echo $inv_ref[0].' '.$inv_ref[1].' '.$inv_ref[2];?></td>
                            <td><?php echo $iv->file_name;?></td>
                            <td style="text-align: right!important;"><?php echo $iv->amount;?></td>
                            <td>
                                <a href="<?php echo base_url().'pdf/credit note/'.date('Y',strtotime($iv->date)).'/'.date('F',strtotime($iv->date)).'/'.$iv->file_name?>" target="_blank">
                                    view
                                </a>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                    endif;
                endforeach;
                ?>
                    <tr class="danger">
                        <td style="text-align: right!important;" colspan="4"><strong>Total:</strong></td>
                        <td style="text-align: right!important;"><strong><?php echo number_format($total,2);?></strong></td>
                        <td>&nbsp;</td>
                    </tr>
                <?php
            else:
            ?>
                <tr>
                    <td colspan="6">No data has found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(e){
       $('.export-btn').click(function(e){
           e.preventDefault();
           $(this).modifiedModal({
               url: this.href,
               title: 'Invoice ' + $(this).data('type')
           });
       });
    });
</script>