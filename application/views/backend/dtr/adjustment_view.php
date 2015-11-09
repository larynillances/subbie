<?php
echo form_open('','class="form-horizontal adjustment-form"');
?>
<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-1" for="amount">Amount:</label>
        <div class="col-sm-3">
            <input type="text" class="form-control input-sm number required" name="amount" id="amount" value="<?php echo @$adjustment->amount?>">
        </div>
        <label class="control-label col-sm-1" for="adjustment_type">Adjustment:</label>
        <div class="col-sm-2">
            <?php echo form_dropdown('adjustment_type_id',$adjustment_type, @$adjustment->adjustment_type_id,'class="form-control input-sm required" id="adjustment_type"')?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-1 control-label" for="note">Notes:</label>
        <div class="col-sm-6">
            <textarea class="form-control input-sm required" name="notes" id="note"><?php echo @$adjustment->notes?></textarea>
        </div>
    </div>

    <fieldset>
        <legend>Payslip</legend>
        <div class="row">
            <div class="pay-slip-loader"></div>
        </div>
    </fieldset>
</div>
<div class="modal-footer">
    <button type="button" class="btn-danger btn-sm btn close-btn" data-dismiss="modal">Close</button>
</div>
<?php
echo form_close();
?>
<style>
    #note{
        min-height: 150px;
    }
</style>
<script>
    $(function(e){
        var _id = '<?php echo $id?>';
        var _date = '<?php echo $date?>';
        var _week = '<?php echo $week?>';
        var close = $('.close,.close-btn');
        var url = bu + 'payAdjustment/' + parseInt(_id);

        var load_pay_slip = function(){
            $(this).newForm.addLoadingForm();
            $('.pay-slip-loader')
                .html('')
                .load(bu + 'printPaySlip/' + parseInt(_id) + '/' + _date + '?view=1&dtr=1',function(data){
                    $(this).newForm.removeLoadingForm();
                });
        };

        load_pay_slip();

        $('.number').numberOnly({
            isForContact: true,
            isPercentage: true
        });

        $('textarea[name="notes"]')
            .on('keyup',function(e){
                var data = $('.adjustment-form').serializeArray();
                data.push(
                    {name:'submit',value:1},
                    {name:'staff_id',value:_id},
                    {name:'week_number',value:_week},
                    {name:'date',value:_date}
                );

                $.post(url,data,function(data){
                });
            });

        $('select[name="adjustment_type_id"]').on('change',function(e){
            var data = $('.adjustment-form').serializeArray();
            data.push(
                {name:'submit',value:1},
                {name:'staff_id',value:_id},
                {name:'week_number',value:_week},
                {name:'date',value:_date}
            );

            $.post(url,data,function(data){
            });
        });

        $('input[name="amount"]').on('keyup, keydown',function(e){
            var data = $('.adjustment-form').serializeArray();
            data.push(
                {name:'submit',value:1},
                {name:'staff_id',value:_id},
                {name:'week_number',value:_week},
                {name:'date',value:_date}
            );

            var adjustment_type = $('#adjustment_type');
            adjustment_type.val('');
            if($(this).val()){
                if($(this).val().indexOf('-') === -1){
                    adjustment_type.val(2);
                }else{
                    adjustment_type.val(1);
                }
            }

            $.post(url,data,function(data){
            });

            if (e.keyCode == 9 || e.keyCode == 13) {
                e.preventDefault();
                $('textarea[name="notes"]').focus();
                load_pay_slip();
            }
        });

        close.click(function(e){
            var select = $('select[name="adjustment_type_id"]');
            var input = $('input[name="amount"]');
            var textarea = $('textarea[name="notes"]');

            var text = textarea.val().replace(/\s/g,'');
            $(this).attr('data-dismiss','modal');
            if(select.val() || input.val() || textarea.val()){
                $('.required').each(function(e){
                    if(!$(this).val()){
                        $(this).css({border:'1px solid red'});
                    }else{
                        $(this).removeAttr('style');
                    }
                });

                if(select.val() && (!input.val() || !textarea.val())){
                    $(this).removeAttr('data-dismiss');
                }
                else if(input.val() && (!select.val() || !textarea.val())){
                    $(this).removeAttr('data-dismiss');
                }
                else if(textarea.val() && (!select.val() || !input.val())){
                    $(this).removeAttr('data-dismiss');
                }
                else if(textarea.val() && text.length < 10){
                    $(this).removeAttr('data-dismiss');
                }
                else{
                    $(this).attr('data-dismiss','modal');
                }
            }
        });
    });
</script>