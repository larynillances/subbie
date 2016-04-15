<?php
echo form_open('','class="form-horizontal" role="form"')
?>
<div class="modal-body">
    <div class="form-group row">
        <label class="col-sm-2 control-label">Date</label>
        <div class="col-sm-10">
            <div class='input-group date datepicker' id='datetimepicker1' data-date-format="DD-MM-YYYY">
                <input type='text' name="completed_date" class="form-control change-class input-sm" placeholder="Date Complete"/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    <button type="submit" class="btn btn-primary submit-btn" name="submit">Save</button>
</div>
<?php
echo form_close();
?>
<script>
    $(function(e){
        $('.datepicker').datetimepicker({
            pickTime: false
        });
    });
</script>