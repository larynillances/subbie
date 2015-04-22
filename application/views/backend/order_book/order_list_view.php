<table class="table table-colored-header">
    <thead>
    <tr>
        <th>Supplier</th>
        <th>Order #</th>
        <th>Job Name</th>
        <th>Sent Date</th>
        <th>Subtotal</th>
        <th>GST Total</th>
        <th>Total</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(count($order_list) > 0):
        foreach($order_list as $val):
            $uri = '';
            ?>
            <tr>
                <td><?php echo $val->supplier_name?></td>
                <td><?php echo $val->order_ref?></td>
                <td><?php echo $val->job_name?></td>
                <td><?php echo $val->date?></td>
                <td><?php echo '$ '.number_format($val->subTotal,2,'.',',');?></td>
                <td><?php echo '$ '.number_format(($val->subTotal * 0.15),2,'.',',');?></td>
                <td><?php echo '$ '.number_format(($val->subTotal * 0.15) + $val->subTotal,2,'.',',');?></td>
                <td><a href="<?php echo base_url().'orderBook/'.$val->supplier_id.'/'.$val->order_ref;?>">view</a></td>
            </tr>
            <?php
        endforeach;
    else:
        ?>
        <tr>
            <td colspan="8">No data has been found.</td>
        </tr>
        <?php
    endif;
    ?>
    </tbody>
</table>