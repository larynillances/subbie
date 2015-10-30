<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($product) > 0):
        foreach($product as $pv):
            ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label col-sm-3">Product Name</label>
                    <div class="col-sm-8">
                        <input type="text" name="product_name" class="form-control input-sm required" value="<?php echo $pv->product_name;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">Quantity</label>
                    <div class="col-sm-8">
                        <input type="text" name="quantity" class="form-control input-sm number" value="<?php echo $pv->quantity;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">Type</label>
                    <div class="col-sm-8">
                        <input type="text" name="type" class="form-control input-sm" value="<?php echo $pv->type;?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">Price</label>
                    <div class="col-sm-8">
                        <input type="text" name="price" class="form-control input-sm number required" value="<?php echo $pv->price;?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary btn-sm submit-btn" name="submit">Submit</button>
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
            <?php
        endforeach;
    endif;
echo form_close();
?>