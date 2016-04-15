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
            $total_credit = 0;
            $total_debit = 0;
            if(count($adjustment) > 0){
                foreach($adjustment as $k=>$v){
                    if($v->adjustment_type_id == 1){
                        $total_debit += $v->amount;
                    }else{
                        $total_credit += str_replace('-','',$v->amount);
                    }
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
            $total = $total_credit - $total_debit;
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

        $('.number').numeric();

        $('textarea[name="notes[]"]')
            .on('keyup',function(e){
                var data = getData();
                $.post(url,data,function(data){
                });
            });

        $('select[name="adjustment_type_id[]"]').on('change',function(e){
            var input = $(this).parent().parent().find('input[name="amount[]"]');
            var input_data = input.val();
            if($(this).val() == 2){
                //input.val('');
                input_data.replace('-','');
                if(input.val().indexOf('-') === -1){
                    input.val('-' + input_data);
                }
            }
            else{
                input.val(input_data.replace('-',''));
            }
            calculate_amount();
            var data = getData();

            $.post(url,data,function(data){
            });
            $(this).parent().parent().find('textarea[name="notes[]"]').focus();
            load_pay_slip();
        });
        var calculate_amount = function(){
            var $_total = 0;
            var total_credit = 0;
            var total_debit = 0;
            var total_amount = $('.total-amount');
            $('input[name="amount[]"]').each(function(e){
                var adjustment_type = $(this).parent().parent().find('select[name="adjustment_type_id[]"]');
                var adjust_val = $(this).val().replace('-','');
                if(adjustment_type.val() == 1){
                    total_debit += adjust_val ? parseFloat(adjust_val) : 0;
                }else{
                    total_credit += adjust_val ? parseFloat(adjust_val) : 0;
                }
            });
            $_total = total_credit - total_debit;
            var total_ = $_total.toString().replace('-','');
            var str = $_total > 0 ? 'CR' : 'DR';
            total_ = parseFloat(total_);
            total_amount.html('$ ' + total_.toFixed(2) + str);
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

                if($(this).val() != ''){
                    if($(this).val().indexOf('-') === -1){
                        adjustment_type.val(1);
                    }
                    else{
                        adjustment_type.val(2);
                    }
                }
                else{
                    adjustment_type.val('');
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
                    var _select = $(this).parent().parent().find('select[name="adjustment_type_id[]"]');
                    var _input = $(this).parent().parent().find('input[name="amount[]"]');
                    var _textarea = $(this).parent().parent().find('textarea[name="notes[]"]');
                    var _text = _textarea.val().replace(/\s/g,'');

                    if(_select.val() && (!_input.val() || !_textarea.val())){
                        _input.css({border:'1px solid red'});
                        _textarea.css({border:'1px solid red'});
                    }
                    else if(_input.val() && (!_select.val() || !_textarea.val())){
                        _select.css({border:'1px solid red'});
                        _textarea.css({border:'1px solid red'});
                    }
                    else if(_textarea.val() && (!_select.val() || !_input.val())){
                        _select.css({border:'1px solid red'});
                        _input.css({border:'1px solid red'});
                    }
                    else if(_textarea.val() && _text.length < 10){
                        _textarea.css({border:'1px solid red'});
                    }
                    else{
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