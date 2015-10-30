<table id="table-scroll" class="table table-colored-header table-responsive">
    <thead>
    <tr>
        <th></th>
        <th>Product Desc.</th>
        <th>Supplier</th>
        <th>Price</th>
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
                <td><?php echo $v->product_name;?></td>
                <td><?php echo $v->price;?></td>
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