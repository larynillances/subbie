<?php
echo form_open('','class="form-horizontal"');
?>
<div class="row">
    <table style="width: 100%;">
        <tr style="vertical-align: top;">
            <td style="padding-top: 80px;">
                <?php
                $code = '';
                if(count($client_data) > 0):
                    foreach($client_data as $v):
                        $code = $v->client_code;
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
            <th colspan="2" class="clear-style">Date: <?php echo date('d-M-Y')?></th>
            <th colspan="4" class="clear-style" style="padding-left: 20%">CREDIT NOTE: <?php echo $credit_ref;?></th>
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
        if(count($credit) >0):
            foreach($credit as $v):
                $job_arr = explode("\n",$v->job_name);
                $v->job_name = str_replace("\n","<br/>",$v->job_name);
                $this_total = $v->area != 0 ? $v->area * $v->price : $v->price;
                $inv_len += count($job_arr);
                $subtotal += $this_total;
                ?>
                <tr>
                    <td><?php echo $v->client_ref;?></td>
                    <td>
                        <a href="#" class="edit-credit-btn" id="<?php echo $v->id;?>" data-value="<?php echo $v->job_ref;?>"><?php echo $v->job_ref;?></a>
                        <input type="hidden" name="reference" value="<?php echo $v->job_ref;?>">
                    </td>
                    <td style="text-align: left;padding-left: 20px!important;"><?php echo $v->job_id != 0 ? $v->reg_job_name : $v->job_name;?></td>
                    <td><?php echo $v->area;?></td>
                    <td><?php echo '$'.number_format($v->price,2);?></td>
                    <td><?php echo '$'.number_format($this_total,2);?></td>
                </tr>
            <?php
            endforeach;
        endif;
        ?>
        <?php
        $checkData = count($credit) == 0 ? '<a href="#" class="btn btn-primary add-credit"><span class="glyphicon glyphicon-plus"></span>  Add Credit</a>' :
                                           '<a href="#" class="btn btn-primary archive-btn-class">Archive</a>
                                           <a href="'.base_url().'creditNote/'.$this->uri->segment(2).'?print=true" class="btn btn-primary print-btn" target="_blank">
                                           <span class="glyphicon glyphicon-print"></span> Print
                                           </a>';
        $maxLen = 25;
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
            <td rowspan="5" colspan="4" class="align-left" style="border-right: none;">&nbsp;</td>
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
                <input type="hidden" name="total" value="<?php echo $total;?>">
            </td>
        </tr>
        </tbody>
    </table>
    <div class="col-md-12 text-right">
        <a href="<?php echo base_url().'statement/'.$this->uri->segment(2)?>" class="btn btn-success"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
        <?php echo $checkData;?>
    </div>
</div>
<?php
echo form_close();
?>
<script>
    $(function(e){
        var content = $('.sm-load-page');
        $('.add-credit').click(function(){
            var url = bu + 'manageCreditNote/add/<?php echo $this->uri->segment(2)?>';
            $('.modal-title').html('Add Credit Note');
            content.load(url);
            $('.sm-modal').modal();
        });
        $('.edit-credit-btn').click(function(){
            var url = bu + 'manageCreditNote/edit/<?php echo $this->uri->segment(2)?>/' + this.id;
            $('.modal-title').html('Edit Credit Note');
            content.load(url);
            $('.sm-modal').modal();
        });
        $('.archive-btn-class').click(function(e){
            e.preventDefault();
            var data = $('.form-horizontal').serializeArray();
            data.push({name:'archive',value:1});
            $.post(bu + 'creditNote/<?php echo $this->uri->segment(2)?>',
                data,
                function(data){
                    location.reload();
                }
            );
        });
    });
</script>