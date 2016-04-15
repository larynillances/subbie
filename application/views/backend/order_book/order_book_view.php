<?php
echo form_open('','style="font-size:14px!important;"');
?>
<div class="row" style="margin: 0 auto;">
    <table style="width: 100%;">
        <tr style="vertical-align: top;">
            <td style="padding-top: 80px;">
                <?php
                if(count($supplier) >0):
                    foreach($supplier as $v):
                        ?>
                        <strong><?php echo $v->supplier_name;;?></strong><br/>
                        <strong>
                            <?php
                            echo str_replace("\n","<br/>",$v->address);
                            ?>
                        </strong><br/>
                        <?php
                    endforeach;
                endif;
                ?>
            </td>
            <td style="width: 20%;">
                <img src="<?php echo base_url().'images/subbie-small-logo.png'?>" width="100"><br/>
                <?php echo $invoice_info;?>
            </td>
        </tr>
    </table><br/><br/>
    <table style="width: 100%;">
        <thead>
        <tr>
            <th>Date: <?php echo date('d-M-Y')?></th>
            <th>Job No.: <?php echo $job_num;?></th>
            <th style="width: 20%;">Order No.: <?php echo $order_num;?></th>
        </tr>
        </thead>
    </table><br/>
    <table class="table table-invoice table-order">
        <thead>
        <tr style="border: 2px solid #000000">
            <th style="width: 20%">Quantity</th>
            <th style="width: 60%">Description</th>
            <th>Price</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $subtotal = 0;
        $uri = $this->uri->segment(3);
        $disable = count($order_list) == 0 || $uri ? 'disabled' : '';
        if(count($order_list) >0):
            foreach($order_list as $ov):
                $subtotal += $ov->price;
                ?>
                <tr>
                    <td><?php echo $ov->quantity;?></td>
                    <td><a href="#" class="edit-product" data-value="<?php echo $ov->supplier_id?>" id="<?php echo $ov->id;?>"><?php echo $ov->product_name;?></a></td>
                    <td><?php echo $ov->price;?></td>
                </tr>
                <?php
            endforeach;
        endif;
        $maxLen = 20 - count($order_list);
        for($i = 0; $i < $maxLen; $i++):
            ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        <?php
        endfor;
        ?>
        <tr style="border-top: 2px solid #000000;">
            <td colspan="2" class="tbl-footer">Sub Total</td>
            <td><?php echo '$ '.number_format($subtotal,2,'.',',');?></td>
        </tr>
        <tr>
            <td colspan="2" class="tbl-footer">GST @ 15 %</td>
            <td><?php echo '$ '.number_format($subtotal * 0.15,2,'.',',');?></td>
        </tr>
        <tr>
            <td colspan="2" class="tbl-footer">Total</td>
            <td><?php echo '$ '.number_format(($subtotal * 0.15) + $subtotal,2,'.',',');?></td>
        </tr>
    </table>
    <div class="row">
        <!--<div class="col-lg-12" style="font-size: 14px;">

        </div>-->
        <div class="col-md-12 text-right">
            <a href="<?php echo base_url().'orderSentList'?>" class="btn btn-success edit-btn" >Back</a>
            <!--<a href="#" class="btn btn-primary edit-btn" <?php /*echo $disable;*/?>>Edit</a>-->
            <input type="submit" name="send" class="btn btn-primary" value="Send" <?php echo $disable;?>>
            <a href="<?php echo base_url().'printOrder/'.$this->uri->segment(2)?>" class="btn btn-primary" <?php echo $disable;?> target="_blank">Print</a>
            <input type="submit" name="archive" class="btn btn-primary" value="Archive" <?php echo $disable;?>>
        </div>
    </div>
</div>
<?php
echo form_close();
?>
<script>
    $(function(e){
        $('.edit-product').click(function(e){
            $('.modal-title').html('Edit Product Entry');
            var url = bu + 'manageOrder/' + this.id + '/' + $(this).attr('data-value');
            $('.sm-load-page').load(url);
            $('.sm-modal').modal();
            /*$(this).newForm.addNewForm({
                title: 'Edit Product Entry',
                url: bu + 'manageOrder/' + this.id + '/' + $(this).attr('data-value'),
                toFind: '.form-horizontal'
            });*/
        });
    })
</script>