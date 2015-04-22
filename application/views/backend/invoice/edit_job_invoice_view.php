<div class="row">
    <table style="width: 100%;">
        <tr style="vertical-align: top;">
            <td style="padding-top: 80px;">
                <?php
                if(count($client) > 0):
                    foreach($client as $v):
                        ?>
                        <strong><?php echo $v->client_name;?></strong><br/>
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
    </table>
    <table class="table table-invoice">
        <thead>
        <tr>
            <th colspan="2" class="clear-style">
                Date: <span class="this-date"><?php echo date('d-M-Y',strtotime($date))?></span>
                <input type="hidden" name="date" class="date-picker" value="<?php echo date('d-M-Y',strtotime($date));?>">
            </th>
            <th colspan="4" class="clear-style" style="padding-left: 20%">TAX INVOICE: <?php echo $inv_code?></th>
        </tr>
        </thead>
        <thead>
        <tr>
            <th style="width: 10%">Your Ref</th>
            <th style="width: 10%">Our Ref</th>
            <th>Job Name</th>
            <th>m&sup2;/hrs</th>
            <th style="width: 15%">Unit Price</th>
            <th style="width: 15%">Total</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $subtotal = 0;
        $inv_len = 0;
        if(count($invoice) >0):
            foreach($invoice as $iv):
                $address = (object)json_decode($iv->address);
                $this_add = $iv->job_id != 0 ? $address->number.' '.$address->name.', '.$address->suburb.', '.$address->city : '';
                /*$subtotal += $iv->unit_price;*/
                $inv_len += count($iv->job_name_array);
                ?>
                <tr>
                    <td style="vertical-align: top;"><?php echo $iv->your_ref;?></td>
                    <td><a href="#" class="edit-btn" id="<?php echo $iv->id;?>" data-value="<?php echo $iv->job_ref;?>"><?php echo $iv->job_ref;?></a></td>
                    <td style="text-align: left;padding-left: 20px!important;"><?php echo $iv->job_id != 0 ? $iv->reg_job_name : $iv->job_name;?></td>
                    <td><?php echo $iv->meter;?></td>
                    <td>
                        <?php

                        if(count($iv->unit_price_array) >0){
                            foreach($iv->unit_price_array as $unit){
                                $this_unit = floatval($unit);
                                echo $this_unit != 0 ? '$'.number_format(@$this_unit,2).'<br/>' : '<br/>';
                            }
                        }
                        ?>
                    </td>
                    <td>
                    <?php
                    if(count($iv->total) >0){
                        foreach($iv->total as $value){
                            $subtotal += floatval($value);
                            echo $value != 0 ? '$'.number_format(floatval($value),2).'<br/>' : '<br/>';
                        }
                    }
                    ?>
                    </td>
                </tr>
                <?php
            endforeach;
        endif;
        ?>
        <?php
        $checkData = count($invoice) == 0 ? 'disabled' : '';
        $maxLen = 30;
        $len = $maxLen - $inv_len;

        for($i = 0; $i <= $len; $i++):
        ?>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <?php
        endfor;
        ?>
        <tr class="border-top border-bottom">
            <td rowspan="5" colspan="4" class="align-left" style="border-right: none;">
                <?php echo $terms_trade;?>
            </td>
        </tr>
        <tr class="total border-top">
            <td class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000">Sub Total</td>
            <td style="border: none;">
                <?php echo '$ '.number_format($subtotal,2);?>
            </td>
        </tr>
        <tr class="total">
            <td class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000">GST Rate</td>
            <td style="border: none;"><?php echo '15%';?></td>
        </tr>
        <tr class="total">
            <td class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000">GST Total</td>
            <td style="border: none;"><?php echo '$ '.number_format($subtotal * 0.15,2);?></td>
        </tr>
        <tr class="total border-bottom">
            <td class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000">Total</td>
            <td style="border: none; font-weight: bold">
                <?php
                $total = $subtotal + ($subtotal * 0.15);
                $over_total = number_format($total,2);
                echo '$ '.$over_total;
                ?>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="col-md-12 text-right">
        <a href="<?php echo base_url().'archiveInvoice'?>" class="btn btn-success back-btn-class"><span class="glyphicon glyphicon-arrow-left"></span>  Back</a>
        <a href="<?php echo base_url().'invoiceManage/print/'.$this->uri->segment(2).'/'.$inv_code.'?total='.$total.'&date='.date('d-M-Y',strtotime($date))?>" class="btn btn-primary" target="_blank"><span class="glyphicon glyphicon-print"></span>  Print</a>
        <a href="<?php echo base_url().'invoiceManage/archive/'.$this->uri->segment(2).'/'.$inv_code.'?total='.$total.'&date='.date('d-M-Y',strtotime($date)).'&archive=true'?>" class="btn btn-primary archive-btn" <?php echo $checkData;?>><span class="glyphicon glyphicon-save"></span> Rearchive</a>
    </div>
</div>
<div class="modal fade modal-load bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Edit Invoice</h4>
            </div>
            <div class="content-loader"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<style>
    .ui-datepicker-trigger{
        width: 15px;
    }
</style>
<script>
    $(function(e){
        var content = $('.content-loader');
        $('.edit-btn').click(function(){
            var data_value = $(this).attr('data-value');
            var url = bu + 'invoiceManage/edit/<?php echo $this->uri->segment(2)?>/' + this.id +'/<?php echo $inv_code.'/'.true;?>?ref=' + data_value;
            console.log(url);
            content.load(url);
            $('.modal-load').modal();
        });
        var link = bu + 'invoiceManage/archive/<?php echo $this->uri->segment(2).'/'.$inv_code.'?total='.$total?>';
        $('.date-picker').datepicker({
            showOn: 'button',
            buttonImageOnly: true,
            dateFormat:"dd-M-yy",
            buttonImage: bu + 'images/calendar-add.png',
            onSelect:function(){
                console.log($(this).val());
                $('.archive-btn').attr('href',link + '&date=' + $(this).val() + '&archive=true');
                $('.this-date').html($(this).val());
            }
        });
    });
</script>