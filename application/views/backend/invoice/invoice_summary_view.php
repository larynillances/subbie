<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="row">
    <div class="col-lg-8">
        <div class="form-group">
            <div class="col-sm-3">
                <?php echo form_dropdown('month',$month,$whatMonth,'class="form-control input-sm"')?>
            </div>
            <div class="col-sm-2">
                <?php echo form_dropdown('year',$year,$whatYear,'class="form-control input-sm"')?>
            </div>
            <div class="col-sm-4">
                <input type="submit" name="submit" class="btn btn-primary" value="Go">
                <a href="<?php echo base_url().'invoiceList'?>" class="btn btn-success back-btn-class">Back</a>
            </div>
        </div>
    </div>
</div>
<?php
echo form_close();
?>
<div class="row">
    <div class="col-lg-10">
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th style="width: 25%;">Client</th>
                <th>Period to</th>
                <th>Invoice Ref.</th>
                <th style="width: 15%;">Invoice Amount (incl. GST)</th>
                <th style="width: 15%;">GST</th>
                <th style="width: 15%;">NETT</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($invoice) >0):
                foreach($invoice as $key=>$val):
                    ?>
                    <tr>
                        <td colspan="6" class="success" style="text-align: right;"><?php echo $key;?></td>
                    </tr>
                    <?php
                    if(count($val) > 0 ):
                        foreach($val as $iv):
                            $gst = ($iv->debits * 0.13042);
                            $subtotal = ($iv->debits - $gst);
                            ?>
                            <tr>
                                <td><?php echo $iv->client_name;?></td>
                                <td><?php echo $iv->date;?></td>
                                <td><?php echo $iv->reference;?></td>
                                <td><?php echo $iv->debits ? '$'.number_format($iv->debits,2) : '$0.00';?></td>
                                <td><?php echo $iv->debits ? '$'.number_format($gst,2) : '$0.00';?></td>
                                <td><?php echo $iv->debits ? '$'.number_format($subtotal,2) : '$0.00';?></td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="6">No data was found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>