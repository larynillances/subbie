<?php
echo form_open('','class="form-horizontal"');
if(count($user) > 0):
    foreach($user as $uv):
        ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name" class="col-sm-4 control-label">Name</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control input-sm" id="name" name="name" value="<?php echo $uv->name;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tel" class="col-sm-4 control-label">Telephone</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control input-sm " id="tel" name="tel" value="<?php echo $uv->tel;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mobile" class="col-sm-4 control-label">Mobile</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control input-sm " id="mobile" name="mobile" value="<?php echo $uv->mobile;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="physical_address" class="col-sm-4 control-label">Address</label>
                        <div class="col-sm-6">
                            <textarea name="physical_address" class="form-control input-sm" id="physical_address" rows="4"><?php echo $uv->physical_address;?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="email" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control input-sm" id="email" name="email" value="<?php echo $uv->email;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="username" class="col-sm-3 control-label">Username</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control input-sm" id="username" name="username" value="<?php echo $uv->username;?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-sm-3 control-label">Password</label>
                        <div class="col-sm-6">
                            <input type="password" class="form-control input-sm" id="password" name="password" value="<?php echo $this->encrypt->decode($uv->password);?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="active" class="col-sm-3 control-label">Active</label>
                        <div class="col-sm-8">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_active" id="active" value="1" <?php echo $uv->is_active == 1 ? 'checked' : '';?>/> &nbsp;
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="acc_type" class="col-sm-3 control-label">Account Type</label>
                        <div class="col-sm-7">
                            <?php
                            echo form_dropdown('account_type',$account_type,$uv->account_type,'class="form-control input-sm" id="acc_type"');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary" name="submit">Save</button>
        </div>
        <?php
        endforeach;
    endif;
echo form_close();
?>