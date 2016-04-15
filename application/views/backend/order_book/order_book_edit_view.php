<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($order) >0):
        foreach($order as $v):
            ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-4 control-label text-left">Product</label>
                    <div class="col-sm-8">
                        <?php echo form_dropdown('product_id',$product_list, $v->product_id,'class="form-control input-sm is_required"')?>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label text-left">Quantity</label>
                    <div class="col-sm-8">
                        <input type="text" name="quantity" class="form-control input-sm number" value="<?php echo $v->quantity;?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary submit-btn-class" name="save">Submit</button>
                <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
            </div>
            <?php
        endforeach;
    endif;
echo form_close();
?>