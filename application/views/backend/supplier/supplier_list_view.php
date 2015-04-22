<div style="padding: 5px">
    <input type="button" name="addSupplier" class="btn btn-success add-supplier" value="Add Supplier">
</div>
<table class="table table-colored-header">
    <thead>
    <tr>
        <th>Name</th>
        <th>Address</th>
        <th>Mobile</th>
        <th>Phone</th>
        <th>Fax</th>
        <th>Email</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(count($supplier) >0):
        foreach($supplier as $v):
            ?>
            <tr>
                <td style="text-align: left"><?php echo $v->supplier_name;?></td>
                <td style="text-align: left"><?php echo $v->address;?></td>
                <td><?php echo $v->mobile;?></td>
                <td><?php echo $v->phone;?></td>
                <td><?php echo $v->fax;?></td>
                <td><?php echo $v->email;?></td>
                <td>
                    <a href="<?php echo base_url().'productListTable/'.$v->id?>" class="product-btn tooltip-class" data-toggle="tooltip" data-placement="top" title="Product List">
                        <span class="glyphicon glyphicon-list-alt"></span>
                    </a>&nbsp;
                    <a href="#" id="<?php echo $v->id;?>" class="edit-supplier tooltip-class" data-toggle="tooltip" data-placement="top" title="Edit">
                        <span class="glyphicon glyphicon-pencil"></span>
                    </a>&nbsp;
                    <a href="#" class="tooltip-class" data-toggle="tooltip" data-placement="top" title="Delete">
                        <span class="glyphicon glyphicon-remove"></span>
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
<script>
    $(function(){
        $('.add-supplier').click(function(e){
            $(this).newForm.addNewForm({
                title: 'Add Supplier',
                url: bu + 'manageSupplier/add',
                toFind:'.form-horizontal'
            });
        });
        $('.edit-supplier').click(function(e){
            $(this).newForm.addNewForm({
                title: 'Edit Client',
                url: bu + 'manageSupplier/edit/' + this.id,
                toFind:'.form-horizontal'
            });
        });
    });
</script>