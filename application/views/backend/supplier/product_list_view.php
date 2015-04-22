<div class="form-group row">
    <div class="col-sm-3">
        <input type="button" name="addProduct" class="btn btn-primary add-product" id="<?php echo $this->uri->segment(2);?>" value="Add Product">
        <a href="<?php echo base_url().'supplierList'?>" class="btn btn-success">Back</a>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th style="width: 35%;">Product Name</th>
                <th>Quantity</th>
                <th>Type</th>
                <th style="width: 10%;">Price</th>
                <th style="width: 7%;"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($product_list) >0):
                foreach($product_list as $v):
                    ?>
                    <tr>
                        <td style="text-align: left;"><?php echo $v->product_name;?></td>
                        <td style="text-align: left;"><?php echo $v->quantity;?></td>
                        <td style="text-align: left;"><?php echo $v->type;?></td>
                        <td style="text-align: left;"><?php echo $v->price;?></td>
                        <td style="vertical-align: middle;">
                            <a href="#" class="edit-btn tooltip-class" title="Edit" id="<?php echo $v->id;?>" data-value="<?php echo $v->supplier_id;?>">
                                <span class="glyphicon glyphicon-pencil">
                            </a>
                        </td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="6">No data has been found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(e){
       $('.edit-btn').click(function(e){
           e.preventDefault();
           var url = bu + 'productManage/' + $(this).attr('data-value') + '/edit/' + this.id;
           $('.modal-title').html('Edit Product');
           $('.load-page').load(url);
           $('.my-modal').modal();
       });
        $('.add-product').click(function(e){
            e.preventDefault();
            var url = bu + 'productManage/' + this.id + '/add';
            $('.modal-title').html('Add Product');
            $('.load-page').load(url);
            $('.my-modal').modal();
        });
    });
</script>