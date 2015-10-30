<table id="table-scroll" class="table table-colored-header table-responsive">
    <thead>
    <tr>
        <th>Product Desc.</th>
        <th>Supplier</th>
        <th style="width: 25%;">Qty</th>
        <th>Price</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    $disabled = count($product_list) > 0 ? 'class="text-danger"' : 'class="text-danger disabled-btn"';
    if(count($product_list) >0):
        foreach($product_list as $v):
            ?>
            <tr>
                <td style="text-align: left;vertical-align: middle;"><?php echo $v->product_name;?></td>
                <td><?php echo $v->supplier_name;?></td>
                <td>
                    <input type="text" name="quantity[<?php echo $v->id;?>]" class="form-control input-sm quantity number" disabled>
                </td>
                <td><?php echo $v->price;?></td>
                <td style="width: 5%;">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="checkbox" class="select-this">
                        </label>
                    </div>
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
<div class="row">
    <div class="col-sm-12">
        <a href="#" class="text-success" style="font-size: 16px;"><i class="glyphicon glyphicon-plus"></i></a>
        <a href="#" <?php echo $disabled;?> style="font-size: 16px;"><i class="glyphicon glyphicon-minus"></i></a>
    </div>
</div>
<style>
    .disabled-btn{
        pointer-events: none;
        color: #808080;
    }
</style>
<script>
    $(function(e){
        var selectThis = $('.select-this');
        var job_name = '<?php echo $job_name;?>';

        selectThis.click(function(e){
            var quantity = $(this).parent().parent().parent().parent().find('.quantity');
            quantity.attr('disabled','disabled');
            quantity.removeClass('is_required');
            if($(this).is(':checked')){
                quantity.removeAttr('disabled');
                quantity.addClass('is_required');
            }
        });
        $('.glyphicon').click(function(e){
            e.preventDefault();
            if($(this).hasClass('glyphicon-plus')){
                var url = bu + 'productTableLoad/<?php echo $this->uri->segment(2)?>';
                var job_id = <?php echo $this->uri->segment(2)?>;
                $.post(url,
                    {
                        submit: 1,
                        job_id: job_id
                    },
                    function(data){
                        $(this).modifiedModal({
                            url: bu + 'productTableLoad/<?php echo $this->uri->segment(2)?>/select',
                            title: 'Select Product for <strong>' + job_name + '</strong>'
                        });
                    }
                );

            }
            else{

            }
        });
    })
</script>