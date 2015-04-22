<?php
echo form_open('','class="form-horizontal" role="form"');
?>
    <div class="modal-body">
        <div class="form-group">
            <label class="col-sm-4 control-label">Date</label>
            <div class="col-sm-8">
                <div class='input-group datepicker' data-date-format="DD-MM-YYYY">
                    <input type='text' class="form-control input-sm" name="date" value="<?php echo date('d-m-Y')?>"/>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-time"></span>
                    </span>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Invoice Ref.</label>
            <div class="col-sm-8">
                <?php echo form_dropdown('reference',$inv_ref_list,'','class="form-control input-sm"')?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Payment Type</label>
            <div class="col-sm-8">
                <?php echo form_dropdown('payment_type',$payment_type,'','class="form-control input-sm"')?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Amount</label>
            <div class="col-sm-8">
                <input type='text' class="form-control input-sm number" name="amount"/>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>
        <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
    </div>
<?php
echo form_close();
?>
<script>
    $(function(e){
        $('.datepicker').datetimepicker({
            pickTime: false
        });
        $('select[name=payment_type]').change(function(e){
            var amount = $('input[name=amount]');
            amount.removeAttr('disabled');
            if($(this).val() == 1){
                amount.attr('disabled','disabled');
            }
        });
        $('.submit-btn').click(function(e){
            var hasEmpty = false;
            var payment = $('select[name=payment_type]');
            var amount = $('input[name=amount]');
            if(!payment.val()){
                hasEmpty = true;
                payment.css({
                    'border' : '1px solid #ff0000'
                });
            }
            if(payment.val() == 2 && !amount.val()){
                hasEmpty = true;
                amount.css({
                    'border' : '1px solid #ff0000'
                });
            }

            if(hasEmpty){
                e.preventDefault();
            }
        });
    });
</script>