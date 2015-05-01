<?php
echo form_open('','class="form-horizontal" role="form"');
$type = array(0 => 'Monthly', 1 => 'Yearly');
?>
<div class="row">
    <div class="col-lg-8">
        <div class="form-group">
            <div class="col-sm-2">
                <?php echo form_dropdown('type',$type,'','class="form-control input-sm action_type"')?>
            </div>
            <div class="col-sm-3 month-class">
                <?php echo form_dropdown('month',$month,$whatMonth,'class="form-control input-sm"')?>
            </div>
            <div class="col-sm-2">
                <?php echo form_dropdown('year',$year,$whatYear,'class="form-control input-sm"')?>
            </div>
            <div class="col-sm-4">
                <input type="submit" name="submit" class="btn btn-primary" value="Go">
            </div>
        </div>
    </div>
</div>
<?php
echo form_close();
?>
<div class="row">
    <div class="col-lg-8">
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Client</th>
                <th>Code</th>
                <th>Invoice Ref.</th>
                <th>File Name</th>
                <th>Amount</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($invoice_list) >0):
                foreach($invoice_list as $key=>$val):
                    ?>
                    <tr>
                        <td colspan="6" class="success" style="text-align: right;"><?php echo $key;?></td>
                    </tr>
                    <?php
                    if(count($val) > 0 ):
                        foreach($val as $iv):
                            $inv_ref = explode(' ',$iv->file_name);
                        ?>
                        <tr>
                            <td><?php echo $iv->client_name;?></td>
                            <td><?php echo $iv->client_code;?></td>
                            <td><?php echo $inv_ref[0];?></td>
                            <td><?php echo $iv->file_name;?></td>
                            <td><?php echo $iv->amount;?></td>
                            <td>
                                <a href="<?php echo base_url().'pdf/invoice/'.date('Y',strtotime($iv->date)).'/'.date('F',strtotime($iv->date)).'/'.$iv->file_name?>" target="_blank">
                                    view
                                </a>&nbsp;
                                <a href="<?php echo base_url().'editArchiveInvoice/'.$iv->client_id.'/'.$inv_ref[0]?>">edit</a>
                                &nbsp;
                                <a href="<?php echo base_url().'exportArchiveInvoice/'.$iv->client_id.'/'.$iv->id?>" class="export-btn" data-type="<?php echo $inv_ref[0];?>">export</a>
                            </td>
                        </tr>
                        <?php
                        endforeach;
                    endif;
                endforeach;
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