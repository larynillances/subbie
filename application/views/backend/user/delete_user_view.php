<?php
echo form_open('','class="form-horizontal"');
    ?>
    <div class="modal-body">
        <h5>Are you sure you want to delete user?</h5>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
        <button type="submit" class="btn btn-primary" name="submit">Yes</button>
    </div>
    <?php
echo form_close();
?>