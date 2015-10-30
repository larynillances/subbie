<?php
echo form_open('','class="form-horizontal"');
unset($leave_type['']);
unset($holiday['']);
$pay = @$leave_pay[$staff_id];
?>
    <div class="modal-body">
        <div class="row">
            <div class="form-group">
                <label class="control-label col-sm-3">Date Submitted:</label>
                <div class="col-sm-3">
                    <div class='input-group date' id='date_requested' >
                        <input type='text' class="form-control input-sm" name="date_requested" readonly/>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Leave Date:</label>
                <div class="col-sm-8">
                    <div style="border: 1px solid #000000;display: table-cell;">
                        <div style="background: #000000;color: #ffffff;padding: 10px;font-weight: bold;">From:</strong></div>
                        <div style="padding: 3px 5px;">
                            <div class='input-group date' id='leave_start' >
                                <input type='text' name="leave_start" class="form-control input-sm" readonly/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            </div>
                        </div>
                    </div>
                    <div style="border: 1px solid #000000;display: table-cell;">
                        <div style="background: #000000;color: #ffffff;padding: 10px;font-weight: bold;">To:</strong></div>
                        <div style="padding: 3px 5px;">
                            <div class='input-group date' id='leave_end' >
                                <input type='text' name="leave_end" class="form-control input-sm" readonly />
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            </div>
                        </div>
                    </div>
                    <div style="border: 1px solid #000000;display: table-cell;">
                        <div style="background: #000000;color: #ffffff;padding: 10px;font-weight: bold;">Time:</strong></div>
                        <div style="padding: 3px 5px;">
                            <?php
                            echo form_dropdown('leave_range', $holiday, $day_data, 'class="leave_range form-control input-sm"');
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Leave Type:</label>
                <div class="col-sm-8">
                    <?php
                    $limit = 5;
                    $s = ceil(count($leave_type)/$limit);
                    $a = array_chunk($leave_type, $s, true);
                    if(count($a) > 0){
                        foreach($a as $i){
                            echo '<div style="display: table-cell;">';
                            foreach($i as $id=>$txt){
                                echo '<label>&nbsp;&nbsp;<input type="radio" name="type" value="' . $id . '" ' . ($leave_data == $id ? 'checked' : '') . '/>&nbsp;' . $txt . '</label>';
                            }
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Reason:</label>
                <div class="col-sm-8">
                    <textarea class="form-control input-sm text-area-class required" name="reason_request" style="min-height: 50px;"></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Ordinary Weekly Pay:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control input-sm" value="<?php echo @$pay['gross'] ? '$ '.number_format(@$pay['gross'],2) : '$ 0.00'?>" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-sm-3">Ave. Weekly Pay:</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control input-sm" value="<?php echo @$pay['annual_leave_pay'] ? '$ '.number_format(@$pay['annual_leave_pay'],2) : '$ 0.00'?>" readonly>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="submit" name="submit_request" class="btn btn-success btn-sm" id="submit-btn">Submit</button>
        <button type="button" class="btn btn-danger btn-sm" id="cancel-btn" data-dismiss="modal">Close</button>
    </div>
<?php
echo form_close();
?>

<script>
    $(function (e) {
        var leave_range = $('.leave_range');
        var leave_start = $('#leave_start');
        var leave_end = $('#leave_end');

        var thisDate = new Date();

        $('#date_requested').datetimepicker({
            format: "DD/MM/YYYY hh:mm a",
            defaultDate: moment(thisDate),
            pickTime: true
        });
        leave_start
            .datetimepicker({
                format: "DD/MM/YYYY hh:mm a",
                useCurrent: false,
                defaultDate: "<?php echo date('d/m/Y h:i a', strtotime($date_start)); ?>",
                maxDate: "<?php echo date('d/m/Y h:i a', strtotime($date_end)); ?>",
                pickTime: false
            })
            .on("dp.change",function (e) {
                var d = new Date(e.date);
                d = leaveRangeSet(d, 1);
                var date = new Date(d.getFullYear(), d.getMonth(), d.getDate() - 1, d.getHours(), d.getMinutes(), d.getSeconds());

                leave_start.data("DateTimePicker").setValue(moment(d));
                leave_end.data("DateTimePicker").setMinDate(moment(date));
                var end = leaveRangeSet(d, 2);
                leave_end.data("DateTimePicker").setValue(moment(end));
            });
        leave_end
            .datetimepicker({
                format: "DD/MM/YYYY hh:mm a",
                useCurrent: false,
                defaultDate: "<?php echo date('d/m/Y h:i a', strtotime($date_end)); ?>",
                minDate: "<?php echo date('d/m/Y h:i a', strtotime($date_start)); ?>",
                pickTime: false
            })
            .on("dp.change",function (e) {
                var d = new Date(e.date);
                d = leaveRangeSet(d, 2);

                leave_end.data("DateTimePicker").setValue(moment(d));
                leave_start.data("DateTimePicker").setMaxDate(e.date);
            });
        leave_range.change(function(e){
            if(leave_start.data('date')){
                var s = new Date(leave_start.data("DateTimePicker").getDate());
                s = leaveRangeSet(s, 1);
                leave_start.data("DateTimePicker").setValue(moment(s));
            }
            if(leave_end.data('date')) {
                var ee = new Date(leave_end.data("DateTimePicker").getDate());
                ee = leaveRangeSet(ee, 2);
                leave_end.data("DateTimePicker").setValue(moment(ee));
            }
        });

        $('#submit-btn').click(function(e){
            var hasEmpty = false;
            $('.required').each(function(e){
                if(!$(this).val()){
                    hasEmpty = true;
                    $(this).css({
                        border: '1px solid #F00'
                    });
                }
                else{
                    $(this).css({
                        border: '1px solid #CCC'
                    });
                }
            });

            if(hasEmpty){
                e.preventDefault();
            }
        });

        function leaveRangeSet(d, t){
            var range = leave_range.val();
            switch (range){
                case "2":
                    d.setHours(t == 1 ? 8 : 12);
                    break;
                case "3":
                    d.setHours(t == 1 ? 12 : 17);
                    break;
                default:
                    d.setHours(t == 1 ? 8 : 17);
            }
            d.setMinutes(0);
            return d;
        }
    });
</script>