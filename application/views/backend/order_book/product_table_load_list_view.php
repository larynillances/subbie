<table id="table-scroll" class="table table-colored-header table-responsive">
    <thead>
    <tr>
        <th></th>
        <th>Brand Name</th>
        <th style="width: 25%;">Qty</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(count($product_list) >0):
        foreach($product_list as $v):
            ?>
            <tr>
                <td style="width: 5%;">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="checkbox" class="select-this">
                        </label>
                    </div>
                </td>
                <td style="text-align: left;vertical-align: middle;"><?php echo $v->product_name;?></td>
                <td>
                    <input type="text" name="quantity[<?php echo $v->id;?>]" class="form-control input-sm quantity number" disabled>
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
<script>
    $(function(e){
        var selectThis = $('.select-this');
        /*$('#table-scroll').scrollTableBody({rowsToDisplay:3});*/
        selectThis.click(function(e){
            var quantity = $(this).parent().parent().parent().parent().find('.quantity');
            quantity.attr('disabled','disabled');
            quantity.removeClass('is_required');
            if($(this).is(':checked')){
                quantity.removeAttr('disabled');
                quantity.addClass('is_required');
            }
        });
    })
</script>