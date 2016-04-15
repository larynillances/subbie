<?php
echo form_open('','class="topup-form"')
?>
<div class="modal-body">
    <table class="table table-colored-header">
        <thead>
        <tr>
            <th style="width: 18%;">Hours</th>
            <th>Reason</th>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php
        $max_len = 1 - count($topup_hours);
        $len = $max_len > 0 ? $max_len : 0;
        $total = 0;
        if(count($topup_hours) > 0){
            foreach($topup_hours as $k=>$v){
                $total += floatval($v->topup_hours);
                ?>
                <tr>
                    <td>
                        <input type="text" class="form-control input-sm top_hours number required" name="topup_hours[]" id="amount" value="<?php echo $v->topup_hours ? $v->topup_hours : ''?>">
                    </td>
                    <td>
                        <textarea class="form-control input-sm required notes" name="notes[]" id="note" rows="1"><?php echo $v->notes?></textarea>
                    </td>
                </tr>
            <?php
            }
        }else{
            ?>
            <tr>
                <td>
                    <input type="text" class="form-control input-sm top_hours number" name="topup_hours[]" id="amount" value="">
                </td>
                <td>
                    <textarea class="form-control input-sm notes" name="notes[]" id="note" rows="1"></textarea>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
        <tbody>
        <tr class="danger">
            <td><strong class="total-amount"><?php echo $total ? number_format($total,2) : '0.00';?></strong></td>
            <td colspan="2" style="text-align: left!important;"><strong>Total Topup Hours this Pay Period</strong></td>
        </tr>
        </tbody>
    </table>
    <fieldset>
        <legend>Pay Period Summary Report</legend>
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
        var _year = '<?php echo date('Y',strtotime($date))?>';
        var _month = '<?php echo date('m',strtotime($date))?>';
        var _week = '<?php echo $week?>';
        var close = $('.close,.close-btn');
        var url = bu + 'topUpHours/' + parseInt(_id);

        var getData = function(){
            var data = $('.topup-form').serializeArray();
            data.push(
                {name:'submit',value:1},
                {name:'staff_id',value:_id},
                {name:'week_number',value:_week},
                {name:'date',value:_date}
            );

            return data;
        };

        var calculate_amount = function(){
            var $_total = 0;
            var total_amount = $('.total-amount');
            $('input[name="topup_hours[]"]').each(function(e){
                $_total += $(this).val() ? parseFloat($(this).val()) : 0;
            });
            total_amount.html($_total.toFixed(2));
        };

        var load_pay_period_summary = function(){
            $(this).newForm.addLoadingForm();
            $('.pay-slip-loader')
                .html('')
                .load(bu + 'payPeriodSummaryReportReview/' + parseInt(_id) + '?year=' + _year + '&month=' + _month + '&week=' + _week,function(data){
                    $(this).newForm.removeLoadingForm();
                });
        };

        load_pay_period_summary();

        $('.number').numberOnly({
            isForContact: true,
            isPercentage: true
        });

        $('.notes')
            .on('keyup',function(e){
                var data = getData();
                $.post(url,data,function(data){
                });
            });

        $('input[name="topup_hours[]"]')
            .on('keyup, keydown',function(e){
                var data = getData();
                $.post(url,data,function(data){
                });
                if (e.keyCode == 9 || e.keyCode == 13) {
                    e.preventDefault();
                    $(this).parent().parent().find('.notes').focus();
                    load_pay_period_summary();
                }
            })
            .on('focusin, focusout', function(e){
                calculate_amount();
            });

        close.click(function(e){
            e.preventDefault();
            var data = getData();

            $.post(url,data,function(data){
            });

            var input = $('input[name="topup_hours[]"]');
            var textarea = $('.notes');
            var _this = $(this);

            $(this).attr('data-dismiss','modal');
            if(input.val() || textarea.val()){
                $('.required').each(function(e){
                    var _input = $(this).parent().parent().find('input[name="topup_hours[]"]');
                    var _textarea = $(this).parent().parent().find('.notes');
                    var _text = _textarea.val().replace(/\s/g,'');

                    if(_input.val() && !_textarea.val()){
                        _this.removeAttr('data-dismiss');
                        _textarea.css({border:'1px solid red'});
                    }
                    else if(_textarea.val() && !_input.val()){
                        _this.removeAttr('data-dismiss');
                        _input.css({border:'1px solid red'});
                    }
                    else if(_text.length < 10){
                        _this.removeAttr('data-dismiss');
                        _textarea.css({border:'1px solid red'});
                    }
                    else{
                        //console.log(_text.length);
                        _this.attr('data-dismiss','modal');
                        $(this).removeAttr('style');
                    }
                });
            }
        });
    });
</script>