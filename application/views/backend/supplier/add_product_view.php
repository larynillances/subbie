<?php
echo form_open('','class="form-horizontal" role="form"');
    ?>
    <div class="modal-body">
        <div class="form-group">
            <label class="control-label col-sm-4">Product Name</label>
            <div class="col-sm-8">
                <input type="text" name="product_name" class="form-control input-sm required">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Quantity</label>
            <div class="col-sm-8">
                <input type="text" name="quantity" class="form-control input-sm number">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Type</label>
            <div class="col-sm-8">
                <input type="text" name="type" class="form-control input-sm">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label col-sm-4">Price</label>
            <div class="col-sm-8">
                <input type="text" name="price" class="form-control input-sm number required">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
    <?php
echo form_close();
?>