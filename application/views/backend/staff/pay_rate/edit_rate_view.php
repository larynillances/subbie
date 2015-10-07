<?php
echo form_open('','class="form-horizontal"');
$end_use = $staff_rate->end_use != '0000-00-00' ? date('d-m-Y',strtotime($staff_rate->end_use)) : '';
?>
<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-4">Rate:</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('rate_id',$rate,$staff_rate->rate_id,'class="form-control input-sm"')?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label tooltip-class" data-placement="top" title="Current Pay Rate Start Date" for="start_use">Start Date:</label>
        <div class="col-sm-8">
            <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                <input type='text' class="input-sm form-control date-class tooltip-class required" data-toggle="tooltip" data-placement="top" title="Current Pay Rate Start Date" id="start_use" name="start_use" placeholder="dd-mm-yyyy" value="<?php echo date('d-m-Y',strtotime($staff_rate->start_use))?>">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label tooltip-class" data-placement="top" title="Current Pay Rate End Date" for="end_use">End Date:</label>
        <div class="col-sm-8">
            <div class='input-group date datetimepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                <input type='text' class="input-sm form-control date-class tooltip-class required" data-toggle="tooltip" data-placement="top" title="Current Pay Rate End Date" id="end_use" name="end_use" placeholder="dd-mm-yyyy" value="<?php echo $end_use?>">
                        <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                        </span>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="submit" name="submit" value="Submit" class="btn btn-sm btn-success">
    <input type="button" name="button" value="Cancel" class="btn btn-sm btn-default" data-dismiss="modal">
</div>
<?php
echo form_close();
?>
<script>
    $(function(){
        $('.tooltip-class').tooltip();
        $('.datetimepicker').datetimepicker({
            pickTime: false
        });
    })
</script>