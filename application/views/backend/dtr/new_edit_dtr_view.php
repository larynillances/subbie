<div class="container-fluid">
    <?php
    echo form_open('','class="form-horizontal" role="form"');
    $this_day = @$days_of_week[0];
    $str_date = $thisYear . '-' . $thisMonth . '-' . date('d',strtotime($this_day));
    $commit_btn_class = @$pay_period->week_num ?
        (@$pay_period->is_locked ? 'disabled="disabled"' : '') : 'disabled="disabled"';
    $is_locked_dtr = @$pay_period->is_locked ? 'is-locked-dtr' : '';

    $account = array(1,2,4);
    $is_locked_dtr = in_array($account_type,$account) ? (@$pay_period->is_locked ? 'is-locked-dtr' : '') : (@$pay_period->is_submitted ? 'is-locked-dtr' : '');
    ?>
    <div class="form-group form-class">
        <label class="col-sm-1 control-label" >Date:</label>
        <div class="col-sm-2">
            <?php echo form_dropdown('month',$month,$thisMonth,'class="form-control input-sm select month-dp"')?>
        </div>
        <div class="col-sm-1">
            <?php echo form_dropdown('year',$year,$thisYear,'class="form-control input-sm select year-dp"')?>
        </div>
        <label class="col-sm-1 control-label" >Week:</label>
        <div class="col-sm-1 week-display">
            <?php echo form_dropdown('week',$week,$thisWeek,'class="form-control input-sm"')?>
        </div>
        <div class="col-sm-6">
            <input type="submit" name="submit" class="btn btn-success btn-sm" value="Save" style="display: none">
            <input type="submit" name="search" class="btn btn-success btn-sm" value="Go">
            <a href="<?php echo base_url('timeSheetDefault')?>" class="btn btn-primary btn-sm">
                <i class="glyphicon glyphicon-arrow-left"></i> Back</a>
            <span class="msg-str" style="display: none;">
                <span class="alert-danger alert" style="padding: 6px 4px;">Saved data.</span>
            </span>
            <?php
            if(count($staff) > 0 && in_array($account_type,$account)){
                ?>
                <span class="pull-right" style="margin-right: 15px;">
                <a href="<?php echo base_url().'payPeriodSummaryReport?print=1&week=' . $thisWeek . '&month=' . $thisMonth . '&year=' . $thisYear?>" class="btn btn-sm btn-primary preview-btn" name="preview" target="_blank" <?php echo $commit_btn_class;?> >Preview</a>
                <button class="btn btn-sm btn-danger commit-btn" name="commit"  <?php echo $commit_btn_class;?>>Commit</button>
            </span>
            <?php
            }else{
                ?>
                <input type="button" name="submit" class="btn btn-primary btn-sm btn-submit-hours pull-right" style="margin-right: 15px;" value="Submit">
            <?php
            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <table class="table table-colored-header table-responsive table-fixed-header">
                <?php
                $totalValue = 0;
                $total = 0;

                $_d = date('d',strtotime($this_day));
                $arr = array(
                    $thisYear . '-12-29', $thisYear . '-12-30', $thisYear . '-12-31'
                );

                $_year = in_array($this_day, $arr) ? $thisYear + 1 : $thisYear;
                $date = mktime(0, 0, 0, $thisMonth,$_d,$_year);
                $week_number = $thisWeek;
                $dt = new DateTime();
                ?>
                <thead class="header">
                <tr>
                    <th rowspan="2" style="width: 15%;">Date</th>
                    <th rowspan="2" >Staff Name</th>
                    <th rowspan="2" style="width: 10%;">Time In</th>
                    <th rowspan="2" style="width: 10%;">Time Out</th>
                    <th rowspan="2" style="width: 12%;">Holiday</th>
                    <th rowspan="2" style="width: 10%;">Sick Leave</th>
                    <th colspan="3" style="width: 25%;">Total</th>
                </tr>
                <tr>
                    <th>Worked<br/>Hours</th>
                    <th>Holiday</th>
                    <th>Sick Leave</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $totalHours = array();
                $totalHolidayHours = array();
                $hoursValue = 0;
                $holidayHours = 0;
                $sickLeaveHours = 0;
                $week_start = StartWeekNumber($week_number,$thisYear);
                $_start_day = $week_start['start_day'];
                $_end_day = $week_start['end_day'];

                for($whatDay=$_start_day; $whatDay<=$_end_day; $whatDay++):
                    $getDate =  $dt->setISODate($thisYear,$week_number,$whatDay)->format('Y-m-d');
                    $day = date('Y-m-d', strtotime($getDate));
                    $d = date('j',strtotime($getDate));
                    $today = date('Y-m-d', strtotime($getDate)) == date('Y-m-d') ? 'today' : '';
                    $this_month = date('m', strtotime($getDate)) == $thisMonth ? '' : 'not-this-month';
                    $ref = 0;
                    $_week = new DateTime($getDate);

                    if(count($staff)>0):
                        ?>
                        <tr class="<?php echo $today.' '.$this_month.' '.$is_locked_dtr;?>" style="border-bottom: 2px solid #0000ff">
                            <td style="vertical-align: middle;font-weight: bold;" rowspan="<?php echo count($staff) + 1?>">
                                <?php
                                echo date('l', strtotime($getDate)).'<br/>'.
                                    date('d-M-Y', strtotime($getDate)).'<br/>'.
                                    '[Week '.$_week->format('W').']<br/>';
                                ?>
                            </td>
                        </tr>
                        <?php
                        foreach($staff as $v):
                            $thisDtr = array_key_exists($v->id, $dtr) ? $dtr[$v->id] : array();
                            $isToday = $day == date('Y-m-d');
                            $pastDay = $day < date('Y-m-d');
                            $reason = '<a href = "#" class="reason-btn tooltip-class" data-value="'.$day.'" id="'.$v->id.'" data-toggle="tooltip" data-placement="top" title="Reason of Absent">R</a>';
                            $getValue = @$thisDtr[$day];
                            $holidayHours += @$getValue['holiday_hours'];
                            $sickLeaveHours += @$getValue['sick_hours'];
                            $totalHolidayHours[$v->id][] = array(
                                'holiday' => @$getValue['holiday_hours'],
                                'sick' => @$getValue['sick_hours']
                            );

                            $style = $ref == (count($staff) - 1) ? 'style="border-bottom: 2px solid #0000ff!important"' : '';
                            ?>
                            <tr class="<?php echo $today.' '.$this_month.' '.$is_locked_dtr;?>" <?php echo $style;?>>
                                <td style="background: #34386a;color: white;font-style: italic;vertical-align: middle;width: 20%;">
                                    <?php
                                    echo $v->fname.' '.$v->lname;
                                    echo $v->rate_cost ? '<span style="color: #ffff00;float: right;font-size: 11px;">($'.$v->rate_cost.')</span>' : '';
                                    ?>
                                </td>
                                <td style="white-space: nowrap;">
                                    <?php
                                    if(count($thisDtr) > 0){
                                        $hasInfo = array_key_exists($day, $thisDtr);

                                        if($hasInfo){
                                            $thisTime = $thisDtr[$day];
                                            $isToday = @$thisTime['time_in'] == '';
                                            $pastDay = @$thisTime['time_in'] == '';
                                            $style = @floatval($thisTime['time_out']) > 0 &&
                                            (@floatval($thisTime['time_in']) == 0 || $thisTime['time_in'] == '')? 'style="border:1px solid red;background:pink;"' : '';
                                            ?>
                                            <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours tooltip-class" title="Valid time format HHmm eg.(0100)"  value="<?php echo @$thisTime['time_in'];?>" placeholder="0000" <?php echo $style;?> >
                                        <?php
                                        }else{?>
                                            <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
                                        <?php
                                        }
                                    }else{?>
                                        <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if(count($thisDtr) > 0){
                                        $hasInfo = array_key_exists($day, $thisDtr);
                                        if($hasInfo){
                                            $thisTime = $thisDtr[$day];
                                            $dtr_id = @$thisTime['dtr_id'];
                                            $isToday = @$thisTime['time_in'] != '' && @$thisTime['time_out'] == '';
                                            $style = @floatval($thisTime['time_in']) > 0 &&
                                            (@floatval($thisTime['time_out']) == 0 || $thisTime['time_out'] == '')? 'style="border:1px solid red;background:pink;"' : '';
                                            ?>
                                            <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours tooltip-class" title="Valid time format HHmm eg.(0100)" value="<?php echo @$thisTime['time_out'];?>" placeholder="0000" <?php echo $style;?> >
                                        <?php
                                        }else{?>
                                            <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
                                        <?php
                                        }
                                    }else{?>
                                        <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" id="<?php echo $v->id?>" data-value="<?php echo $d;?>" class="form-control input-sm data-input hours tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
                                    <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php echo form_dropdown('holiday_type_id['.$v->id.'][' . $day . ']',$holiday,@$getValue['holiday_type_id'],'class="form-control input-sm data-dp"')?>
                                </td>
                                <td><?php echo form_dropdown('sick_leave_type_id['.$v->id.'][' . $day . ']',$holiday,@$getValue['sick_leave_type_id'],'class="form-control input-sm data-dp"')?></td>
                                <td style="background:#87be90;font-weight: bold;vertical-align: middle">
                                    <?php
                                    $thisDtr = array_key_exists($v->id, $dtr) ? $dtr[$v->id] : array();
                                    $isToday = $day == date('Y-m-d');
                                    $dtr_id = '';
                                    if(count($thisDtr) > 0){
                                        $hasInfo = array_key_exists($day, $thisDtr);
                                        if($hasInfo){
                                            $thisTime = $thisDtr[$day];

                                            $break_deduction = BreakTimeDeduction(@$thisTime['time_in'],@$thisTime['time_out']);
                                            $break_deduction_seconds = BreakTimeDeduction(@$thisTime['time_in'],@$thisTime['time_out'],true);

                                            $valid_time = @floatval($thisTime['time_in']) > 0 && @floatval($thisTime['time_out']) > 0;

                                            @$totalHours[$v->id] += $valid_time ? (@$thisTime['seconds'] - $break_deduction_seconds) : 0;

                                            $dtr_id = @$thisTime['dtr_id'];
                                            echo @$thisTime['hours'] > 0 ? number_format(@$thisTime['hours'] - $break_deduction,2) : '&nbsp;';
                                            echo @$thisTime['hours'] > 0 ? '&nbsp;<a href="#" style="color: red;float: right;" class="tooltip-class" title="'.($break_deduction_seconds/60).' minutes Meal Break deducted.">?</a>' : '';
                                        }else{
                                            echo '&nbsp;';
                                        }
                                    }else{
                                        echo '&nbsp;';
                                    }
                                    ?>
                                </td>
                                <td style="background:#87be90;vertical-align: middle">
                                    <strong><?php echo @$getValue['holiday_hours'];?></strong>
                                </td>
                                <td style="background:#87be90;vertical-align: middle">
                                    <strong><?php echo @$getValue['sick_hours'];?></strong>
                                </td>
                            </tr>
                            <?php
                            $ref++;
                        endforeach;
                    else:
                        ?>
                        <td style="background: #a9a9a9">&nbsp;</td>
                        <?php
                        if($whatDay == 7){
                            ?>
                            <td style="background: #a9a9a9">&nbsp;</td>
                            <td style="background: #a9a9a9">&nbsp;</td>
                        <?php
                        }
                    endif;
                    ?>
                <?php
                endfor;
                ?>
                <tr class="danger">
                    <td colspan="9" style="text-align: center;text-transform: uppercase"><strong>Total Weekly Hours per Employee</strong></td>
                </tr>
                <?php
                $hoursValue = 0;
                if(count($staff)>0):
                    foreach($staff as $v):
                        $total = @$totalHours[$v->id];
                        $hours = $total/3600;
                        $holiday_total = 0;
                        $sick_total = 0;
                        if(count($totalHolidayHours[$v->id]) > 0){
                            foreach($totalHolidayHours[$v->id] as $data){
                                $holiday_total += $data['holiday'];
                                $sick_total += $data['sick'];
                            }
                        }
                        ?>
                        <tr class="info">
                            <td colspan="6" style="text-align: right;"><strong><?php echo $v->fname.' '.$v->lname?></strong></td>
                            <td><strong><?php echo number_format($hours,2);?></strong></td>
                            <td><strong><?php echo number_format($holiday_total,2);?></strong></td>
                            <td><strong><?php echo number_format($sick_total,2);?></strong></td>
                        </tr>
                        <?php
                        $hoursValue += ($total/3600);
                    endforeach;
                endif;
                ?>

                <tr class="danger">
                    <td colspan="6" style="text-align: right;text-transform: uppercase"><strong>Total Hours</strong></td>
                    <td><strong><?php echo number_format($hoursValue,2);?></strong></td>
                    <td><strong><?php echo number_format($holidayHours,2);?></strong></td>
                    <td><strong><?php echo number_format($sickLeaveHours,2);?></strong></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php
    echo form_close();
    ?>
</div>
<style>
    .not-this-month{
        background: #8b8b8b !important;
    }
    .form-class div{
        padding: 2px;
    }
    table.table thead tr th{
        border: 1px solid #000000;
        vertical-align: middle;
    }
    table.table tbody tr td{
        border: 1px solid #000000;
    }
    table.table tbody .today td{
        background: #7cdae5;
    }
    table.table tbody .past-day td{
        background: #56e5b4;
    }
    table.table tbody tr .absent{
        background: #e5a6a6;
        color: #000000;
    }
    ::-webkit-input-placeholder {
        color: #c3c3c3 !important;
    }

    :-moz-placeholder { /* Firefox 18- */
        color: #c3c3c3!important;;
    }

    ::-moz-placeholder {  /* Firefox 19+ */
        color: #c3c3c3!important;;
    }

    :-ms-input-placeholder {
        color: #c3c3c3!important;;
    }

</style>
<script src="<?php echo base_url();?>plugins/fixed-header/jquery.stickytableheaders.js"></script>
<script>
    $('.table-fixed-header').stickyTableHeaders({marginTop: 51});
    $(function(e){
        var hours = $('.hours');
        var week_ = <?php echo $thisWeek;?>;
        var month_ = <?php echo $thisMonth;?>;
        var year_ = <?php echo $thisYear;?>;

        hours.numberOnly({
            wholeNumber: true,
            isForContact:true,
            hasMaxChar:true,
            maxCharLen:4
        });

        $('.preview-btn').click(function(e){
            e.preventDefault();
            var myWindow = window.open(
                this.href,
                'Pay Period Summary Report'
            );
            $.post(bu + 'generateStaffPaySlip/' + week_ + '/' + month_ + '/' + year_ +'?generate=1',
                {
                    generate: 1
                },
                function(data){
                    if(data){

                        $(myWindow).load(function(){
                            location.reload();
                        });
                    }
                }
            );
        });

        $('.btn-submit-hours').click(function(e){
            e.preventDefault();
            var ele =
                '<div class="modal-body">' +
                    '<div class="row">' +
                        '<div class="col-sm-12">' +
                        'Are you sure you have entered all <strong>Employee Hourly Details</strong> accurately ? ' +
                        'Once these <strong>Hours</strong> are submitted to the <strong>Wages Clerk</strong> you will not be able ' +
                        'to alter or add to them any further yourself. However, you may contact your ' +
                        '<strong>Wages Clerk</strong> during the <strong>Monday</strong> of each week if you subsequently do find a ' +
                        'mistake after submitting these Hours' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-success yesBtn-confirm-submit" data-dismiss="modal">Yes</button>' +
                    '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                '</div>';
            $(this).modifiedModal({
                html:ele,
                title: 'Submit Daily Hours'
            });

            $('.yesBtn-confirm-submit').click(function(){
                var data_ = $('.form-horizontal').serializeArray();
                data_.push({name:'hours_submit',value:1});
                var msg_str = $('.msg-str');
                msg_str.css({'display':'none'});
                $.post(bu + 'timeSheetEdit',
                    data_,
                    function(data){
                        location.reload();
                    }
                );
            });
        });

        $('.commit-btn').click(function(e){
            e.preventDefault();
            var ele =
                '<div class="modal-body">' +
                    '<div class="row">' +
                        '<div class="col-sm-12">' +
                        'Please confirm you are ready to commit these <strong>Hours Worked</strong> for the <strong>Pay Period</strong> shown. ' +
                        'Once these Hours are committed the Pay Period is <strong>Locked</strong>, permanently, and any changes must then ' +
                        'be applied to a following Pay Period. If you <strong>Cancel</strong>, you may continue to edit any of the data on ' +
                        'this page until you are satisfied it is correct and may be paid out on.' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-success yesBtn-confirm" data-dismiss="modal">Yes</button>' +
                    '<button type="button" class="btn btn-default" data-dismiss="modal">No</button>' +
                '</div>';
            $(this).modifiedModal({
                html:ele,
                title: 'Commit Pay Period'
            });

            $('.yesBtn-confirm').click(function(){
                $(this).newForm.addLoadingForm();
                var has_return = 0;
                $.post(bu + 'timeSheetEdit',{commit:1},
                     function(data){
                         $.post(bu + 'payPeriodSummaryReport?print=1&week=' + week_ + '&month=' + month_ + '&year=' + year_,
                             {submit:1},
                             function(data){
                                 $.post(bu + 'timeSheetEdit',
                                     {
                                         send_mail:1
                                     },
                                     function(data){
                                         console.log(data);
                                         has_return = 1;
                                         $(this).newForm.removeLoadingForm();
                                         location.replace(bu + 'timeSheetEdit');
                                     }
                                 );
                             }
                         );
                     }
                 );
            });
        });
        $('.data-dp').change(function(){
            var data = $('.form-horizontal').serializeArray();
            data.push({name:'submit',value:1});
            var msg_str = $('.msg-str');
            msg_str.css({'display':'none'});
            $.post(bu + 'timeSheetEdit',
                data,
                function(data){
                    console.log(data);
                    msg_str.css({'display':'inline'});
                }
            );
        });

        var not_this_month = function (){
            var has_class = $('.is-locked-dtr');
            has_class.find('select').attr('disabled','disabled');
            has_class.find('input').attr('disabled','disabled');
        };

        not_this_month();

        $('.reason-btn').click(function(e){
            e.preventDefault();
            var url = bu + 'absentReason/' + this.id + '/' + $(this).attr('data-value');
            $('.sm-title').html('Reason for absent');
            $('.sm-load-page').load(url);
            $('.sm-modal').modal();
        });
        $('.set-job').click(function(e){
            e.preventDefault();
            var url = bu + 'setJobAssign/' + this.id + '/' + $(this).attr('data-value') + '/editing/add';
            $('.my-title').html('Set Staff Job');
            $('.load-page').load(url);
            $('.my-modal').modal();
        });
        $('.split-btn').click(function(e){
            e.preventDefault();
            var date = $(this).attr('data-value');
            var url = bu + 'assignJob/split/' + this.id + '/' + date;
            $('.my-title').html('Split Time');
            $('.load-page').load(url);
            $('.my-modal').modal();
        });
        $('.edit-set-job').click(function(e){
            e.preventDefault();
            var url = bu + 'setJobAssign/' + this.id + '/' + $(this).attr('data-value') + '/editing/edit';
            $('.my-title').html('Edit Staff Job');
            $('.load-page').load(url);
            $('.my-modal').modal();
        });
    });
</script>