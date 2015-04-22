<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
        <?php
        switch($action):
            case 'edit':
                ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Team:</label>
                    <div class="col-sm-8">
                        <?php echo form_dropdown('team_id',$team,$team_id,'class="form-control input-sm"')?>
                    </div>
                </div>
                <?php
                break;
            case 'add':
                ?>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Team:</label>
                    <div class="col-sm-8">
                        <?php echo form_dropdown('team_id',$team,'','class="form-control input-sm"')?>
                    </div>
                </div>
                <?php
                break;
            default:
                break;
        endswitch;
        ?>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
        <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
    </div>
<?php
echo form_close();
?>