<table class="table table-responsive table-colored-header">
    <thead>
    <tr>
        <th style="width: 10%;">Job Ref</th>
        <th style="width: 10%;">Your Ref</th>
        <th style="width: 10%;">Invoice</th>
        <th style="width: 10%;">Sub-total</th>
        <th style="width: 10%;">GST</th>
        <th style="width: 10%;">Total</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(count($invoice) >0):
        foreach($invoice as $v):
            $subtotal = 0;
            $total = 0;
            if(count($v->total) >0){
                foreach($v->total as $value){
                    $subtotal += floatval($value);
                }
            }

            ?>
            <tr>
                <td><?php echo $v->job_ref;?></td>
                <td><?php echo $v->your_ref;?></td>
                <td>
                    <a href="<?php echo base_url().'jobInvoice/'.$v->client_id.'/'.$v->id;?>">view</a>
                </td>
                <td><?php echo '$ '.number_format($subtotal,2,'.',',');?></td>
                <td><?php echo '$ '.number_format(($subtotal * 0.15),2,'.',',');?></td>
                <td><?php echo '$ '.number_format(($subtotal + ($subtotal * 0.15)),2,'.',',');?></td>
            </tr>
        <?php
        endforeach;
    else:
    ?>
        <tr>
            <td colspan="6">No data have been found.</td>
        </tr>
    <?php
    endif;
    ?>
    </tbody>
</table>