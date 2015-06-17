<table class="table table-responsive table-colored-header">
    <thead>
    <tr>
        <th style="width: 20%;">Client</th>
        <th style="width: 10%;">Job Summary</th>
        <th style="width: 10%;">Invoice</th>
        <th style="width: 10%;">Invoice Summary</th>
        <th style="width: 10%;">Statement</th>
        <th style="width: 10%;">Sub-total</th>
        <th style="width: 10%;">GST</th>
        <th style="width: 10%;">Total</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $subtotal = 0;
    $total = 0;
    $gst_total = 0;
    if(count($client_list) >0):
        foreach($client_list as $v):
            $subtotal += $v->subtotal;
            $total += $v->total;
            $gst_total += $v->gst_total;
        ?>
        <tr>
            <td><?php echo $v->client_name;?></td>
            <td>
                <a href="#">view</a>
            </td>
            <td>
                <!--<a href="<?php /*echo base_url().'jobInvoice/'.$v->id;*/?>">view</a>-->
                <a href="<?php echo base_url().'invoiceList/list/'.$v->id;?>">view</a>
            </td>
            <td>
                <a href="<?php echo base_url().'invoiceSummary/'.$v->id;?>">view</a>
            </td>
            <td>
                <a href="<?php echo base_url().'statement/'.$v->id;?>">view</a>
            </td>
            <td><?php echo '$ '.number_format($v->subtotal,2,'.',',');?></td>
            <td><?php echo '$ '.number_format($v->gst_total,2,'.',',');?></td>
            <td><?php echo '$ '.number_format($v->total,2,'.',',');?></td>
        </tr>
        <?php
        endforeach;
    endif;
    ?>
    <tr>
        <td colspan="5" class="align-right font-bold">Total</td>
        <td class="grey-background"><?php echo '$ '.number_format($subtotal,2,'.',',');?></td>
        <td class="grey-background"><?php echo '$ '.number_format($gst_total,2,'.',',');?></td>
        <td class="grey-background"><?php echo '$ '.number_format($total,2,'.',',');?></td>
    </tr>
    </tbody>
</table>