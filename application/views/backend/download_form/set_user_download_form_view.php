<?php
echo form_open_multipart('','class="form-horizontal"');
?>
<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-3" for="menu">User Type:</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('account_type_id[]',$account_type,'','multiple="multiple" class="multiple-selected form-control input-sm"')?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-3" for="menu">Form:</label>
        <div class="col-sm-8">
            <?php echo form_dropdown('form_id[]',$downloadable_form,'','multiple="multiple" class="multiple-selected form-control input-sm"')?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <input type="submit" class="btn btn-sm btn-primary" name="submit" value="Submit">
    <input type="button" class="btn btn-sm btn-default" value="Cancel" data-dismiss="modal">
</div>
<?php
echo form_close();
?>
<script>
    $(function(){
        $('.multiple-selected').multiselect();
    })
</script>