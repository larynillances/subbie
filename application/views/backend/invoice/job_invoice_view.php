<div class="row">
    <table style="width: 100%;">
        <tr style="vertical-align: top;">
            <td style="padding-top: 80px;">
                <?php
                $id = $this->uri->segment(2);
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
        <?php
        if($id == 19){
         ?>
            <tr>
                <th colspan="3" class="clear-style">
                    Date: <span class="this-date"><?php echo date('d-M-Y')?></span>
                    <input type="hidden" name="date" class="date-picker" value="<?php echo date('d-M-Y');?>">
                </th>
                <th colspan="4" class="clear-style">TAX INVOICE: <?php echo $inv_code?></th>
            </tr>
            <tr>
                <th colspan="4" style="text-align: left;">Job Name: <?php echo @$invoice_info_data->job_name;?></th>
                <th colspan="3" style="text-align: left;">Contract Amount: <?php echo @$invoice_info_data->contract_amount ? '$ ' . number_format($invoice_info_data->contract_amount,2) : '$ 0.00';?></th>
                <th colspan="3" style="text-align: left;">VO #: <?php echo @$invoice_info_data->order_number;?></th>
                <th colspan="3" style="text-align: left;">Trade: <?php echo @$invoice_info_data->trade;?></th>
            </tr>
            <tr class="small-font">
                <th style="width: 10%">Our Ref</th>
                <th style="width: 10%">Your Ref</th>
                <th style="width: 50%;">Description of Work</th>
                <th style="width: 10%">Unit Price</th>
                <th style="width: 10%">Contract<br/><span>(Excluding GST)</span></th>
                <th style="width: 10%">Retention<br/><span>(10%)</span></th>
                <th style="width: 10%;border-right: 2px #000000 solid;">Total<br/><span>(Including GST)</span></th>
                <th style="width: 10%;">Variation<br/><span>(Excluding GST)</span></th>
                <th style="width: 10%">Retention<br/><span>(10%)</span></th>
                <th style="width: 10%;border-right: 2px #000000 solid;">Total<br/><span>(Including GST)</span></th>
                <th style="width: 10%">Contract<br/><span>(Excluding GST)</span></th>
                <th style="width: 10%">Retention<br/><span>(10%)</span></th>
                <th style="width: 10%;">Total<br/><span>(Including GST)</span></th>
            </tr>
        <?php
        }
        else{
            ?>
            <tr>
                <th colspan="2" class="clear-style">
                    Date: <span class="this-date"><?php echo date('d-M-Y')?></span>
                    <input type="hidden" name="date" class="date-picker" value="<?php echo date('d-M-Y');?>">
                </th>
                <th colspan="4" class="clear-style" style="padding-left: 20%">TAX INVOICE: <?php echo $inv_code?></th>
            </tr>
            <tr>
                <th style="width: 10%">Your Ref</th>
                <th style="width: 10%">Our Ref</th>
                <th>Job Name</th>
                <th>m&sup2;/hrs</th>
                <th style="width: 15%">Unit Price</th>
                <th style="width: 15%">Total</th>
            </tr>
        <?php
        }
        ?>
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
                if($id == 19){
                    ?>
                    <tr>
                        <td>
                            <?php
                            if($account_type == 3){
                                echo $iv->job_ref;
                            }else{
                                ?>
                                <a href="#" class="edit-btn" id="<?php echo $iv->id;?>" data-value="<?php echo $iv->job_ref;?>"><?php echo $iv->job_ref;?></a>
                            <?php
                            }?>
                        </td>
                        <td style="vertical-align: top;"><?php echo $iv->your_ref;?></td>
                        <td style="text-align: left;padding-left: 20px!important;"><?php echo $iv->work_description;?></td>
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
                        <?php
                        if($iv->invoice_type && $iv->invoice_type == 1){
                            ?>
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
                            <td>
                                <?php
                                if(count($iv->retention) >0){
                                    foreach($iv->retention as $value){
                                        echo $value != 0 ? '$'.number_format(floatval($value),2).'<br/>' : '<br/>';
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if(count($iv->over_all_total) >0){
                                    foreach($iv->over_all_total as $value){
                                        echo $value != 0 ? '$'.number_format(floatval($value),2).'<br/>' : '<br/>';
                                    }
                                }
                                ?>
                            </td>
                            <?php
                        }
                        else{
                            ?>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        <?php
                        }
                        ?>
                        <?php
                        if($iv->invoice_type && $iv->invoice_type == 2){
                            ?>
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
                            <td>
                                <?php
                                if(count($iv->retention) >0){
                                    foreach($iv->retention as $value){
                                        echo $value != 0 ? '$'.number_format(floatval($value),2).'<br/>' : '<br/>';
                                    }
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if(count($iv->over_all_total) >0){
                                    foreach($iv->over_all_total as $value){
                                        echo $value != 0 ? '$'.number_format(floatval($value),2).'<br/>' : '<br/>';
                                    }
                                }
                                ?>
                            </td>
                        <?php
                        }
                        else{
                            ?>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        <?php
                        }
                        ?>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <?php
                }
                else{
                    ?>
                    <tr>
                        <td style="vertical-align: top;"><?php echo $iv->your_ref;?></td>
                        <td>
                            <?php
                            if($account_type == 3){
                                echo $iv->job_ref;
                            }else{
                                ?>
                                <a href="#" class="edit-btn" id="<?php echo $iv->id;?>" data-value="<?php echo $iv->job_ref;?>"><?php echo $iv->job_ref;?></a>
                            <?php
                            }?>
                        </td>
                        <td style="text-align: left;padding-left: 20px!important;"><?php echo $iv->job_name;?></td>
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
                }
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
            <?php
            $len_ = $id == 19 ? 13 : 6;
            for($k = 1; $k <= $len_; $k++){
                ?>
                <td>&nbsp;</td>
            <?php
            }
            ?>
        </tr>
        <?php
        endfor;
        if($id == 19){
            ?>
            <tr class="new-invoice total border-top">
                <td colspan="3" class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000;border-left: 2px solid #000000">Sub Total</td>
                <td colspan="4" style="border-right: 2px solid #000000;">
                    <?php echo @$invoice_info_data->invoice_type == 1 ? '$ '.number_format($subtotal,2) : '&nbsp;';?>
                </td>
                <td colspan="3" style="border-right: 2px solid #000000;">
                    <?php echo @$invoice_info_data->invoice_type == 2 ? '$ '.number_format($subtotal,2) : '&nbsp;';?>
                </td>
                <td colspan="3" style="border-right: 2px solid #000000;">&nbsp;</td>
            </tr>
            <tr class="new-invoice total">
                <td colspan="3" class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000;border-left: 2px solid #000000">GST Rate</td>
                <td colspan="4" style="border-right: 2px solid #000000;"><?php echo @$invoice_info_data->invoice_type == 1 ? '15%' : '&nbsp;';?></td>
                <td colspan="3" style="border-right: 2px solid #000000;"><?php echo @$invoice_info_data->invoice_type == 2 ? '15%' : '&nbsp;';?></td>
                <td colspan="3" style="border-right: 2px solid #000000;">&nbsp;</td>
            </tr>
            <tr class="new-invoice total">
                <td colspan="3" class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000;border-left: 2px solid #000000">GST Total</td>
                <td colspan="4" style="border-right: 2px solid #000000;"><?php echo @$invoice_info_data->invoice_type == 1 ? '$ '.number_format($subtotal * 0.15,2) : '&nbsp;';?></td>
                <td colspan="3" style="border-right: 2px solid #000000;"><?php echo @$invoice_info_data->invoice_type == 2 ? '$ '.number_format($subtotal * 0.15,2) : '&nbsp;';?></td>
                <td colspan="3" style="border-right: 2px solid #000000;">&nbsp;</td>
            </tr>
            <tr class="new-invoice total border-bottom">
                <td colspan="3" class="font-bold align-right" style="border-left: none;border-right: 2px solid #000000;border-left: 2px solid #000000">Total</td>
                <td colspan="4" style="border-right: 2px solid #000000; font-weight: bold">
                    <?php
                    $total = $subtotal + ($subtotal * 0.15);
                    $over_total = number_format($total,2);
                    echo @$invoice_info_data->invoice_type == 1 ? '$ '.$over_total : '&nbsp;';
                    ?>
                </td>
                <td colspan="3" style="border-right: 2px solid #000000; font-weight: bold">
                    <?php
                    $total = $subtotal + ($subtotal * 0.15);
                    $over_total = number_format($total,2);
                    echo @$invoice_info_data->invoice_type == 2 ? '$ '.$over_total : '&nbsp;';
                    ?>
                </td>
                <td colspan="3" style="border-right: 2px solid #000000;">&nbsp;</td>
            </tr>
            <tr class="border-top border-bottom">
                <td colspan="13" class="align-left">
                    <?php echo $terms_trade;?>
                </td>
            </tr>
            <?php
        }
        else{
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
        <?php
        }
        ?>
        </tbody>
    </table>
    <div class="col-md-12 text-right">
        <a href="<?php echo base_url().'invoiceList'?>" class="btn btn-success back-btn-class"><span class="glyphicon glyphicon-arrow-left"></span>  Back</a>
        <!--<a href="<?php /*echo base_url().'invoiceManage/print/'.$this->uri->segment(2).'/'.$inv_code*/?>" class="btn btn-primary" <?php /*echo $checkData;*/?> target="_blank"><span class="glyphicon glyphicon-print"></span>  Print</a>-->
        <?php
        if($account_type != 3){
            $uri = $this->uri->segment(3) ? '/' . $this->uri->segment(3) : '';
            ?>
            <a href="<?php echo base_url().'invoiceManage/archive/'.$this->uri->segment(2).'/'.$inv_code .$uri .'?total='.$total.'&date='.date('d-M-Y')?>" class="btn btn-primary archive-btn" <?php echo $checkData;?>><span class="glyphicon glyphicon-save"></span> Archive</a>
        <?php
        }?>
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
    .table-invoice > thead > tr.small-font > th{
        background: none;
        font-size: 11px;
        vertical-align: middle;
    }
    .table-invoice > thead > tr.small-font > th > span{
        font-weight: normal;
        font-style: italic;
    }
    .table-invoice > tbody > tr.new-invoice > td{
        text-align: right!important;
    }
</style>
<script>
    $(function(e){
        var content = $('.content-loader');
        $('.edit-btn').click(function(){
            var data_value = $(this).attr('data-value');
            var url = bu + 'invoiceManage/edit/<?php echo $this->uri->segment(2)?>/' + this.id +'?ref=' + data_value ;
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
                $('.archive-btn').attr('href',link + '&date=' + $(this).val());
                $('.this-date').html($(this).val());
            }
        });
    });
</script>