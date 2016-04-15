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
    <div class="col-lg-4">
        <table class="table table-colored-header">
            <thead>
            <tr>
                <th>Client</th>
                <th>Date</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($pdf_statement) > 0):
                foreach($pdf_statement as $key=>$val):
                    ?>
                    <tr>
                        <td colspan="3" class="success" style="text-align: right;"><?php echo $key;?></td>
                    </tr>
                    <?php
                    if(count($val) > 0):
                        foreach($val as $v):
                            $year = date('Y',strtotime($v['date']));
                            $month = date('F',strtotime($v['date']));
                            ?>
                            <tr>
                                <td><?php echo $v['client_name']?></td>
                                <td><?php echo $v['archive_date']?></td>
                                <td><a href="<?php echo base_url().'pdf/statement/'.$year.'/'.$month.'/'.$v['file_name']?>" target="_blank">view</a></td>
                            </tr>
                            <?php
                        endforeach;
                    endif;
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="3">No data has been found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>