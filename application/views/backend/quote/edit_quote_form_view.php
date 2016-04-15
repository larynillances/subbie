<?php
echo form_open('');
?>
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
                <?php echo $invoice_info;?>
            </td>
        </tr>
    </table><br/><br/>
    <?php
    if(count($quotation)>0):
        foreach($quotation as $v):
            $address = $v->address ? (object)json_decode($v->address) : array();
            $this_add = $v->address ? $address->number.' '.$address->name.', '.$address->suburb.', '.$address->city : $v->job_address;
            ?>
        <table style="width: 100%;">
            <thead>
            <tr>
                <th>Date: <?php echo date('d-M-Y')?></th>
                <th style="width: 20%;">Quote No.: <?php echo $v->quote_num?></th>
            </tr>
            </thead>
        </table><br/><br/>
        <div class="row">
            <div class="col-lg-12" style="font-size: 14px;">
                <p>
                    Thank you for the opportunity to quote for the painting works for the Interior/Exterior at
                    <strong class="input-data"><?php echo $this_add?></strong>.
                    We submit our quotation for the above contract as per plans specifications provided for the sum
                    of $ <strong class="input-data"><?php echo number_format($v->price,2,'.',',');?></strong> plus GST $ <strong class="input-data"><?php echo number_format($v->gst,2,'.',',')?></strong>.<br/><br/><br/>

                    Note:<br/>
                    Please note the following items:<br/><br/>
                    Scaffolding/and/or/scissors lift provided by Main Contractor.<br/>
                    <strong class="input-data">
                        <?php
                        echo str_replace("\n","<br/>",$v->tags);
                        ?>
                    </strong><br/><br/>
                    This quotation holds good for 90 days from the above date.<br/><br/><br/>

                    Payment terms:<br/>
                    All contracts to be paid in full on the 20th of the month following date of invoice. Extras to contract will
                    be charge at $50 per hour per man, materials will be charge at $25 per litre.<br/><br/><br/>

                    Yours faithfully,<br/><br/><br/>
                    Tony Boniface<br/>
                    Operation Manager<br/>
                </p>
                <table style="width: 100%">
                    <tr>
                        <td style="border-bottom: 1px solid #000000">&nbsp;</td>
                    </tr>
                </table><br/><br/>
                <p>
                    We accept the quotation from the Subbie Solutions Ltd for the painting works at <strong class="input-data"><?php echo $this_add?></strong>.
                    For the sum of $ <strong class="input-data"><?php echo number_format($v->price,2,'.',',')?></strong> plus GST $ <strong class="input-data"><?php echo number_format($v->gst,2,'.',',')?></strong>.<br/><br/>

                </p>
                <table style="width: 100%">
                    <tr>
                        <td style="width: 5%;">Signed:</td>
                        <td style="width: 20%;border-bottom: 1px solid #000000">&nbsp;</td>
                        <td style="">&nbsp;</td>
                        <td style="width: 5%">Date:</td>
                        <td style="width: 15%;border-bottom: 1px solid #000000">&nbsp;</td>
                    </tr>
                </table>
            </div>
        </div><br/><br/><br/>
        <div class="col-md-12 text-right">
            <a href="<?php echo base_url().'quoteList'?>" class="btn btn-success">Back</a>
            <input type="submit" name="send" class="btn btn-primary" value="Send">
            <a href="<?php echo base_url().'printQuote/'.$v->client_id.'/'.$v->id?>" class="btn btn-primary print-btn" style="display: none;" target="_blank">Print</a>
            <input type="submit" name="archive" class="btn btn-primary archive-quote" value="Archive">
        </div>
        <?php
        endforeach;
    endif;
    ?>
</div>
<?php
echo form_close();
?>
<div class="modal fade modal-load" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Edit Quote Request</h4>
            </div>
            <div class="content-loader"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<style>
    .input-data{
        text-decoration: underline;
    }
</style>
<script>
    $(function(e){
        $('.edit-btn').click(function(e){
            e.preventDefault();
            var page = $('.content-loader');
            var url = bu + 'quotation/edit/' + this.id;
            page.load(url);
            $('.modal-load').modal();
        });
    })
</script>