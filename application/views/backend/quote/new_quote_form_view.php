<div class="row">
    <table style="width: 100%;">
        <tr style="vertical-align: top;">
            <td>
                <?php echo form_dropdown('client_id',$client,'','class="form-control" style="width:30%"')?><br/>
                <textarea style="resize: none;height: 35%;width: 30%;" class="form-control"></textarea>
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
            <th style="width: 20%;">Quote No.: <?php echo 'SQ'.str_pad(31,5,'0',STR_PAD_LEFT)?></th>
        </tr>
        </thead>
    </table><br/><br/>
    <div class="row">
        <div class="col-lg-12" style="font-size: 14px;">
            <p>
                Thank you for the opportunity to quote for the painting works for the Interior/Exterior at
                <strong class="input-data"><input type="text" name="address" class="address"></strong>.
                We submit our quotation for the above contract as per plans specifications provided for the sum
                of $ <strong class="input-data"><input type="text" name="nett"></strong> plus GST $ <strong class="input-data"><input type="text" name="gst"></strong>.<br/><br/><br/>

                Note:<br/>
                Please note the following items:<br/><br/>
                Scaffolding/and/or/scissors lift provided by Main Contractor.<br/>
                <strong class="input-data"><input type="text" name="main_contractor"></strong><br/>
                This quotation holds good for 90 days from the above date.<br/>
                <strong class="input-data"><input type="text" name="quotation"></strong><br/><br/>
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
                We accept the quotation from the Subbie Solutions Ltd for the painting works at <strong class="input-data"><input type="text" name="street" class="address"></strong>.
                For the sum of $ <strong class="input-data"><input type="text" name="quotation"></strong> plus GST $ <strong class="input-data"><input type="text" name="quotation"></strong>.<br/><br/>

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
        <a href="<?php echo base_url().'clientList'?>" class="btn btn-primary">Back</a>
        <a href="#" class="btn btn-primary">Send</a>
        <a href="#" class="btn btn-primary">Archive</a>
    </div>
</div>
<style>
    .input-data{
        text-decoration: underline;
    }
    .address{
        width: 30%;
    }
    input[type=text]{
        margin-bottom: 5px;
    }
</style>