<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="modal-body">
    <label class="control-label">Termination Type:</label>
    <?php
    $ref = 0;
    if(count($termination_pay_list) > 0){
        foreach($termination_pay_list as $type){
            $is_check = count($termination_type) > 0 ? (in_array($type->id,$termination_type) ? 'CHECKED' : '') : '';
            ?>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="termination_type_id[]" value="<?php echo $type->id?>" <?php echo $is_check;?> > <?php echo $type->termination_type?>
                </label>
            </div>
            <?php
            $ref++;
        }
    }
    ?>
</div>
<div class="modal-footer">
    <button class="btn btn-sm btn-success" type="submit" name="select">Select</button>
    <button class="btn btn-sm btn-default" type="button" data-dismiss="modal">Cancel</button>
</div>
<?php
echo form_close();
?>