<div class="row">
    <table style="width: 100%;">
        <tr style="vertical-align: top;">
            <td style="padding-top: 80px;">
                <?php
                if(count($client_data) > 0):
                    foreach($client_data as $v):
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
                <?php echo $invoice_info.'<br/>';?>
                Date: <strong class="this-date"><?php echo date('d-M-Y')?></strong>
                <input type="hidden" name="date" class="date-picker" value="<?php echo date('d-M-Y');?>">
            </td>
        </tr>
    </table>
    <table class="table table-invoice">
        <thead>
        <tr>
            <th colspan="5" class="clear-style" style="text-align: center!important;">
                <span style="padding: 5px 20px;" class="grey-background">STATEMENT</span>
            </th>
        </tr>
        </thead>
        <thead>
        <tr>
            <th style="width: 15%">Date</th>
            <th>Reference</th>
            <th style="width: 15%">Debits</th>
            <th style="width: 15%">Credits</th>
            <th style="width: 15%">Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $maxLen = 30;
        if(count($statement_data) >0):
            foreach($statement_data as $k=>$value):
            $maxLen = $maxLen - count($value);
            ?>
            <tr>
                <td rowspan="<?php echo count($value);?>" style="vertical-align: top;"><?php echo $k;?></td>
                <?php
                $ref = 0;
                if(count($value) >0):
                    foreach($value as $v):
                        echo $ref != 0 ? '<tr>' : '';
                        ?>
                        <td style="text-align: left;">
                            <?php if($v->type != 'opening'){
                                echo $v->type.' ';
                                ?>
                                <a href="<?php echo base_url().'editArchiveInvoice/'.$this->uri->segment(2).'/'.$v->reference;?>"><?php echo $v->reference;?></a>
                            <?php
                            }else{
                                echo $v->reference;
                            }?>
                        </td>
                        <td><?php echo $v->debits;?></td>
                        <td><a href="#" class="edit-btn" id="<?php echo $v->id;?>"><?php echo $v->credits;?></a></td>
                        <td class="success"><?php echo $v->balance;?></td>
                        <?php
                        echo $ref != 0 ? '</tr>' : '';
                        $ref++;
                    endforeach;
                endif;
            endforeach;
        endif;
        ?>
        <?php
        $hasPayment = $statement_count['payment'] == 0 ? 'disabled' : '';
        $hasData = $statement_count['has_info'] == 0 ? 'disabled' : '';
        $hasCredit = $statement_count['credit'] == 0 ? 'disabled' : '';

        for($i = 0; $i <= $maxLen; $i++):
            ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td class="success">&nbsp;</td>
            </tr>
        <?php
        endfor;
        ?>
        <tr style="border-top: 2px solid #000000!important;">
            <td colspan="5" class="align-left clear-style">
                <?php echo $terms_trade;?>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="col-md-12 text-right">
        <a href="<?php echo base_url().'invoiceList'?>" class="btn btn-success back-btn-class"><span class="glyphicon glyphicon-arrow-left"></span> Back</a>
        <a href="#" class="btn btn-primary add-payment" <?php echo $hasPayment;?>>Add Payment</a>
        <a href="<?php echo base_url().'creditNote/'.$this->uri->segment(2);?>" class="btn btn-primary" <?php echo $hasCredit;?>>Add Credit Note</a>
        <a href="<?php echo base_url().'manageStatement/print/'.$this->uri->segment(2).'?date='.date('d-M-Y')?>" class="btn btn-primary print-btn" <?php echo $hasData;?> target="_blank"><span class="glyphicon glyphicon-print"></span> Print PDF</a>
        <a href="<?php echo base_url().'manageStatement/archive/'.$this->uri->segment(2)?>" class="btn btn-primary" <?php echo $hasData;?>><span class="glyphicon glyphicon-save"></span> Archive</a>
    </div>
</div>
<style>
    .ui-datepicker-trigger{
        width: 15px;
    }
</style>
<script>
    $(function(e){
        var content = $('.sm-load-page');
        var data_value;
        var url;
        var modal_title = $('.sm-title');
        $('.edit-btn').click(function(){
            data_value = $(this).attr('data-value');
            modal_title.html('Edit Credit');
            url = bu + 'manageStatement/edit/<?php echo $this->uri->segment(2)?>/' + this.id;
            content.load(url);
            $('.sm-modal').modal();
        });
        $('.add-payment').click(function(){
            modal_title.html('Add Payment');
            url = bu + 'manageStatement/payment/<?php echo $this->uri->segment(2)?>';
            content.load(url);
            $('.sm-modal').modal();
        });
        var link = bu + 'manageStatement/print/<?php echo $this->uri->segment(2);?>';
        $('.date-picker').datepicker({
            showOn: 'button',
            buttonImageOnly: true,
            dateFormat:"dd-M-yy",
            buttonImage: bu + 'images/calendar-add.png',
            onSelect:function(){
                console.log($(this).val());
                $('.print-btn').attr('href',link + '?date=' + $(this).val());
                $('.this-date').html($(this).val());
            }
        });
    });
</script>