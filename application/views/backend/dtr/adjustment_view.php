<?php
echo form_open('','class="form-horizontal adjustment-form"');
?>
<div class="modal-body">
    <table class="table table-colored-header">
        <thead>
            <tr>
                <th style="width: 18%;">Amount</th>
                <th style="width: 15%;">CR/DR</th>
                <th>Reason</th>
            </tr>
        </thead>
        <tbody class="table-body">
            <?php
            $max_len = 5 - count($adjustment);
            $len = $max_len > 0 ? $max_len : 0;
            $total = 0;
            if(count($adjustment) > 0){
                foreach($adjustment as $k=>$v){
                    $total += $v->amount;
                    ?>
                    <tr>
                        <td>
                            <input type="text" class="form-control input-sm number required" name="amount[]" id="amount" value="<?php echo $v->amount ? $v->amount : ''?>">
                        </td>
                        <td>
                            <?php echo form_dropdown('adjustment_type_id[]',$adjustment_type, $v->adjustment_type_id,'class="form-control input-sm required" id="adjustment_type"')?>
                        </td>
                        <td>
                            <textarea class="form-control input-sm required" name="notes[]" id="note" rows="1"><?php echo $v->notes?></textarea>
                        </td>
                    </tr>
                <?php
                }
            }
            for($i=0;$i<=$len;$i++){
                ?>
                <tr>
                    <td>
                        <input type="text" class="form-control input-sm number required" name="amount[]" id="amount" value="">
                    </td>
                    <td>
                        <?php echo form_dropdown('adjustment_type_id[]',$adjustment_type, '','class="form-control input-sm required" id="adjustment_type"')?>
                    </td>
                    <td>
                        <textarea class="form-control input-sm required" name="notes[]" id="note" rows="1"></textarea>
                    </td>
                </tr>
            <?php
            }
            ?>
        </tbody>
        <tbody>
            <tr class="danger">
                <td><strong class="total-amount"><?php echo $total ? '$ '.number_format($total,2) : '$ 0.00';?></strong></td>
                <td colspan="2" style="text-align: left!important;"><strong>Total Adjustment this Pay Period</strong></td>
            </tr>
        </tbody>
    </table>
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

        $('textarea[name="notes[]"]')
            .on('keyup',function(e){
                var data = getData();
                $.post(url,data,function(data){
                });
            });

        $('select[name="adjustment_type_id[]"]').on('change',function(e){
            var data = getData();

            $.post(url,data,function(data){
            });
            $(this).parent().parent().find('textarea[name="notes[]"]').focus();
            load_pay_slip();
        });
        var calculate_amount = function(){
            var $_total = 0;
            var total_amount = $('.total-amount');
            $('input[name="amount[]"]').each(function(e){
                $_total += $(this).val() ? parseFloat($(this).val()) : 0;
                total_amount.html('');
            });
            total_amount.html('$ ' + $_total.toFixed(2));
        };

        var getData = function(){
            var data = $('.adjustment-form').serializeArray();
            data.push(
                {name:'submit',value:1},
                {name:'staff_id',value:_id},
                {name:'week_number',value:_week},
                {name:'date',value:_date}
            );

            return data;
        };

        $('input[name="amount[]"]')
            .on('keyup, keydown',function(e){
                var data = getData();

                var adjustment_type = $(this).parent().parent().find('select[name="adjustment_type_id[]"]');
                adjustment_type.val('');
                if($(this).val() != ''){
                    if($(this).val().indexOf('-') === -1){
                        adjustment_type.val(1);
                    }else{
                        adjustment_type.val(2);
                    }
                }

                $.post(url,data,function(data){
                });
                if (e.keyCode == 9 || e.keyCode == 13) {
                    e.preventDefault();
                    $(this).parent().parent().find('textarea[name="notes[]"]').focus();
                    load_pay_slip();
                }
            })
            .on('focusin, focusout',function(e){
                calculate_amount();
            });

        close.click(function(e){
            var data = getData();

            $.post(url,data,function(data){
            });

            var select = $('select[name="adjustment_type_id[]"]');
            var input = $('input[name="amount[]"]');
            var textarea = $('textarea[name="notes[]"]');

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