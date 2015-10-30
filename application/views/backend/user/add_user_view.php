<?php
echo form_open('','class="form-horizontal"')
?>
    <div class="modal-body">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="name" class="col-sm-4 control-label">Name</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control input-sm required" id="name" name="name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="tel" class="col-sm-4 control-label">Phone</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control input-sm " id="tel" name="tel">
                    </div>
                </div>
                <div class="form-group">
                    <label for="mobile" class="col-sm-4 control-label">Mobile</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control input-sm " id="mobile" name="mobile">
                    </div>
                </div>
                <div class="form-group">
                    <label for="physical_address" class="col-sm-4 control-label">Address</label>
                    <div class="col-sm-6">
                        <textarea name="physical_address" class="form-control input-sm" id="physical_address" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="email" class="col-sm-3 control-label">Email</label>
                    <div class="col-sm-7">
                        <input type="text" class="form-control input-sm required" id="email" name="email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="username" class="col-sm-3 control-label">Username</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control input-sm required" id="username" name="username">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="col-sm-3 control-label">Password</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control input-sm required" id="password" name="password">
                    </div>
                </div>
                <div class="form-group">
                    <label for="active" class="col-sm-3 control-label">Active</label>
                    <div class="col-sm-8">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" id="active" value="1" /> &nbsp;
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="acc_type" class="col-sm-3 control-label">Account Type</label>
                    <div class="col-sm-7">
                        <?php
                        echo form_dropdown('account_type',$account_type,3,'class="form-control input-sm" id="acc_type"');
                        ?>
                    </div>
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
        $('.submit-btn').live('click',function(e){
            var hasEmpty = false;
            $('.required').each(function(e){
                if(!$(this).val()){
                    hasEmpty = true;
                    $(this).css({
                        border:'1px solid #a94442'
                    });
                }
            });
            if(hasEmpty){
                e.preventDefault();
            }
        });
    })
</script>