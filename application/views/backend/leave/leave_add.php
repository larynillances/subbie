<style>
    table tr th{
        background: #ffffff!important;
    }
    .leaveForm{
        border-collapse: collapse;
        font-size: 12px;
        width: 100%;
    }
    .leaveForm tr td{
        padding: 3px 5px;
    }
    .leaveForm tr td:first-child{
        font-weight: bold;
        white-space: nowrap;
        text-align: right;
    }
</style>

<?php
echo form_open('');
    ?>
    <table class="leaveForm">
        <tr>
            <td style="width: 100px;white-space: nowrap;">Project:</td>
            <td>
                <div class="col-sm-3">
                    <?php
                    echo form_dropdown('project_id', $project, '', 'class="project form-control input-sm"');
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 100px;white-space: nowrap;">Employee Name:</td>
            <td>
                <div class="col-sm-3 userArea" data-default="<?php echo $staffDefault; ?>"></div>
            </td>
        </tr>
        <tr>
            <td>Date Submitted:</td>
            <td>
                <div class="col-sm-3">
                    <div class='input-group date' id='date_requested'>
                        <input type='text' class="form-control required input-sm" name="date_requested" readonly/>
                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr style="vertical-align: top;">
            <td>Leave Date:</td>
            <td>
                <div class="col-sm-7">
                    <div style="border: 1px solid #000000;display: table-cell;width: 300px;">
                        <div style="background: #000000;color: #ffffff;padding: 10px;font-weight: bold;">From:</strong></div>
                        <div style="padding: 3px 5px;">
                            <div class='input-group date' id='leave_start'>
                                <input type='text' name="leave_start" class="form-control input-sm required" readonly/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            </div>
                        </div>
                    </div>
                    <div style="border: 1px solid #000000;display: table-cell;width: 300px;">
                        <div style="background: #000000;color: #ffffff;padding: 10px;font-weight: bold;">To:</strong></div>
                        <div style="padding: 3px 5px;">
                            <div class='input-group date' id='leave_end'>
                                <input type='text' name="leave_end" class="form-control input-sm required" readonly/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                            </div>
                        </div>
                    </div>
                    <div style="border: 1px solid #000000;display: table-cell;width: 300px;">
                        <div style="background: #000000;color: #ffffff;padding: 10px;font-weight: bold;">Time:</strong></div>
                        <div style="padding: 3px 5px;">
                            <?php
                            echo form_dropdown('leave_range', $leave_range, 1, 'class="leave_range form-control input-sm"');
                            ?>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr style="vertical-align: top;">
            <td>Kind of Leave:</td>
            <td>
                <div class="col-sm-7">
                    <?php
                    $limit = 5;
                    $s = ceil(count($type)/$limit);
                    $a = array_chunk($type, $s, true);
                    if(count($a) > 0){
                        foreach($a as $i){
                            echo '<div style="display: table-cell;">';
                            foreach($i as $id=>$txt){
                                echo '<label style="white-space: nowrap!important;">&nbsp;&nbsp;<input type="radio" name="type" value="' . $id . '" />&nbsp;' . $txt . '</label>';
                            }
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left;">
                <div class="col-sm-10">
                    <div style="border: 1px solid #000000;">
                        <div style="background: #000000;color: #ffffff;padding: 10px;font-weight: bold;">State the reason for request/application</strong></div>
                        <textarea name="reason_request" class="form-control input-sm required" style="width: 100%;height: 100px;resize: none;"></textarea>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="2"><br /></td>
        </tr>
        <?php
        if(in_array($account_type, array(1,2,4))) {
            ?>
            <tr>
                <td>Decision:</td>
                <td>
                    <div class="col-sm-5">
                        <?php
                        if (count($decision) > 0) {
                            echo '<div style="display: table-row;">';
                            foreach ($decision as $id => $txt) {
                                echo '<div style="display: table-cell;">';
                                echo '<label>&nbsp;&nbsp;<input type="radio" name="decision" value="' . $id . '"/>&nbsp;' . $txt . '</label>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: left;">
                    <div class="col-sm-10">
                        <div style="border: 1px solid #000000;">
                            <div style="background: #000000;color: #ffffff;padding: 10px;font-weight: bold;">Reason for
                                Decision</strong></div>
                            <textarea name="reason_decision" class="form-control input-sm" style="width: 100%;height: 100px;resize: none;"></textarea>
                        </div>
                    </div>
                </td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td colspan="2" style="text-align: right;">
                <div class="col-sm-10">
                    <input type="submit" name="submit" class="btn btn-primary btn-sm submitBtn" value="Request" />
                    <a href="<?php echo base_url() . "staffLeave"; ?>" class="btn btn-sm btn-danger">Close</a>
                </div>
            </td>
        </tr>
    </table>
    <?php
echo form_close();
?><br/>
<script>
    $(function (e) {
        var fDp, sDp;

        fDp = $('.project');
        sDp = $('.sDp');
        var $sDp = <?php echo count($staff) > 0 ? json_encode($staff) : '[]'; ?>;
        fDp.selectCountry({
            cityName: 'user_id',
            cityId: 'sDp',
            cityClass: 'sDp',
            city: $sDp,
            appendWhere: $('.userArea'),
            callBack: function(e){
                sDp = $('.sDp');
            }
        });

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

        $('.submitBtn').click(function(e){
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