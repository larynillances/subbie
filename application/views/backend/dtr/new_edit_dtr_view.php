<div class="container-fluid">
    <?php
    echo form_open('','class="form-horizontal form-open" role="form"');
    $this_day = @$days_of_week[0];
    $str_date = $thisYear . '-' . $thisMonth . '-' . date('d',strtotime($this_day));
    $preview_btn_class = @$pay_period->week_num ?
        (@$pay_period->is_locked ? 'disabled="disabled"' : '') : 'disabled="disabled"';

    $commit_btn_class = @$pay_period->week_num ?
        (@$pay_period->is_locked ? 'disabled="disabled"' : (@$pay_period->is_preview ? '' : 'disabled="disabled"')) : 'disabled="disabled"';

    $is_locked_dtr = @$pay_period->is_locked ? 'is-locked-dtr' : '';
    $selected_day_type = $this->session->userdata('day_type_selected');
    $leave_type_selected = $this->session->userdata('leave_type_selected');
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
                <a href="<?php echo base_url().'payPeriodSummaryReport?print=1&week=' . $thisWeek . '&month=' . $thisMonth . '&year=' . $thisYear?>" class="btn btn-sm btn-primary preview-btn" name="preview" target="_blank" <?php echo $preview_btn_class;?> >Preview</a>
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
                    <th rowspan="2" style="width: 12%;">Leave</th>
                    <th rowspan="2" style="width: 12%;">Day</th>
                    <th colspan="2" style="width: 25%;">Total</th>
                </tr>
                <tr>
                    <th>Worked<br/>Hours</th>
                    <th style="width: 15%;">Leave<br/>Hours</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $totalHours = array();
                $totalWorkHours = array();
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
                    $week_end = $_week->format('W') != 30 ? date('Y-m-d',strtotime('+6 days '.$getDate)) : date('Y-m-d',strtotime('+5 days '.$getDate));
                    $type = 0;
                    $range_type = 0;

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
                            $staff_leave = @$leave_data_approved[$v->id][$getDate];
                            $pending = @$leave_data_pending[$v->id][$getDate];
                            $holidayHours += @$staff_leave->decision ? @$staff_leave->leave_in_hours : 0;
                            $sickLeaveHours += @$getValue['sick_hours'];
                            $totalHolidayHours[$v->id][] = array(
                                'holiday' =>  @$staff_leave->decision ? @$staff_leave->leave_in_hours : 0,
                                'sick' => @$getValue['sick_hours']
                            );

                            $__leave_type = @$leave_type_selected[$v->id][$day];
                            $__day_type = @$selected_day_type[$v->id][$day];
                            $type = @$pending->type ? @$pending->type : @$leave_type_selected[$v->id][$day];
                            $range_type = @$pending->range_type ? @$pending->range_type : @$selected_day_type[$v->id][$day];

                            $disable_input = @$pending->range_type != 1 ? '' : 'disabled-input';

                            $style = $ref == (count($staff) - 1) ? 'style="border-bottom: 2px solid #0000ff!important"' : '';
                            $has_pending_request = @$pending->leave_in_hours ? 'has-pending' : '';
                            ?>
                            <tr class="<?php echo $today.' '.$this_month.' '.$is_locked_dtr.' '.$has_pending_request.' '.$disable_input;?>" <?php echo $style;?>>
                                <td style="background: #34386a;color: white;font-style: italic;vertical-align: middle;width: 20%;">
                                    <?php
                                    echo $v->fname.' '.$v->lname;
                                    echo $v->rate_cost ? '<span style="color: #ffff00;float: right;font-size: 11px;">($'.$v->rate_cost.')</span>' : '';
                                    ?>
                                </td>
                                <?php
                                if(count($staff_leave) > 0){

                                    if($staff_leave->range_type != 1){
                                        ?>
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
                                                    <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours time_in tooltip-class" title="Valid time format HHmm eg.(0100)"  value="<?php echo count($staff_leave) == 0 ? @$thisTime['time_in'] : '';?>" placeholder="0000" <?php echo $style;?> >
                                                <?php
                                                }else{?>
                                                    <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours time_in tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
                                                <?php
                                                }
                                            }else{?>
                                                <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours time_in tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
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
                                                    <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours time_out tooltip-class" title="Valid time format HHmm eg.(0100)" value="<?php echo count($staff_leave) == 0 ? @$thisTime['time_out'] : '';?>" placeholder="0000" <?php echo $style;?> >
                                                <?php
                                                }else{?>
                                                    <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours time_out tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
                                                <?php
                                                }
                                            }else{?>
                                                <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" id="<?php echo $v->id?>" data-value="<?php echo $d;?>" class="form-control input-sm data-input hours time_out tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
                                            <?php
                                            }
                                            ?>
                                        </td>
                                        <td colspan="2">
                                            <strong><?php echo 'Staff was on '.$staff_leave->leave_type;?></strong>
                                        </td>
                                        <?php
                                    }
                                    else{
                                        ?>
                                        <td colspan="4">
                                            <strong><?php echo 'Staff was on '.$staff_leave->leave_type;?></strong>
                                        </td>
                                        <?php
                                    }
                                    ?>
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

                                                @$totalHours[$v->id] += count(@$staff_leave) == 0 && $valid_time ? (@$thisTime['seconds'] - $break_deduction_seconds) : 0;

                                                $dtr_id = @$thisTime['dtr_id'];
                                                echo count($staff_leave) == 0 && @$thisTime['hours'] > 0 ? number_format(@$thisTime['hours'] - $break_deduction,2) : '&nbsp;';
                                                echo count($staff_leave) == 0 && @$thisTime['hours'] > 0 ? '&nbsp;<a href="#" style="color: red;float: right;" class="tooltip-class" title="'.($break_deduction_seconds/60).' minutes Meal Break deducted.">?</a>' : '';
                                            }else{
                                                echo '&nbsp;';
                                            }
                                        }else{
                                            echo '&nbsp;';
                                        }
                                        ?>
                                    </td>
                                    <td style="background:#87be90;vertical-align: middle">
                                        <strong>
                                            <?php

                                            echo $staff_leave->leave_in_hours && $staff_leave->decision ? number_format($staff_leave->leave_in_hours,2) : '&nbsp;';
                                            ?>
                                        </strong>
                                    </td>

                                <?php
                                }
                                else{
                                    ?>
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
                                                <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours time_in tooltip-class" title="Valid time format HHmm eg.(0100)"  value="<?php echo @$thisTime['time_in'];?>" placeholder="0000" <?php echo $style;?> >
                                            <?php
                                            }else{?>
                                                <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours time_in tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
                                            <?php
                                            }
                                        }else{?>
                                            <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input hours time_in tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
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
                                                <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input time_out hours tooltip-class" title="Valid time format HHmm eg.(0100)" value="<?php echo @$thisTime['time_out'];?>" placeholder="0000" <?php echo $style;?> >
                                            <?php
                                            }else{?>
                                                <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" class="form-control input-sm data-input time_out hours tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
                                            <?php
                                            }
                                        }else{?>
                                            <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" id="<?php echo $v->id?>" data-value="<?php echo $d;?>" class="form-control input-sm data-input hours time_out tooltip-class" title="Valid time format HHmm eg.(0100)" placeholder="0000">
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo form_dropdown('leave_type_id['.$v->id.'][' . $day . ']',$leave_type,$type,'id="pending_'.@$pending->id.'" data-default="'.$type.'" data-date="'.$getDate.'" data-staff="'.$v->id.'" class="form-control input-sm data-dp leave-type '.$has_pending_request.'" data-value="'.$v->fname.' '.$v->lname.'"')
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        echo form_dropdown('day_type_id['.$v->id.'][' . $day . ']',$holiday,$range_type,'id="pending_'.@$pending->id.'" data-default="'.$range_type.'" data-date="'.$getDate.'" data-staff="'.$v->id.'" class="form-control input-sm data-dp day-type '.$has_pending_request.'" data-value="'.$v->fname.' '.$v->lname.'"');
                                        ?>
                                    </td>
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
                                                echo @$thisTime['hours'] > 0 ? '&nbsp;<a href="#" style="color: red;float: right;margin-left:3px;" class="tooltip-class" title="'.($break_deduction_seconds/60).' minutes Meal Break deducted.">?</a>' : '';
                                            }else{
                                                echo '&nbsp;';
                                            }
                                        }else{
                                            echo '&nbsp;';
                                        }
                                        echo @$pending->leave_in_hours && !@$pending->decision ? '&nbsp;<a href="#" style="color: red;float: right;margin-left:3px;" class="tooltip-class has-pending-request" id="pending_' . @$pending->id . '" data-date="'.$getDate.'" data-staff="'.$v->id.'" data-value="'. $v->fname . ' ' . $v->lname .'" title="Leave Request still Pending Approval."><i class="glyphicon glyphicon-asterisk"></i></a>' : '';
                                        echo '&nbsp;';
                                        echo '<strong class="incomplete-request" ' . ($__leave_type && $__day_type && !@$pending->leave_in_hours ? '' : 'style="display: none;"') . '><a href="#" style="color: red;float: right;margin-left:3px;" class="tooltip-class has-incomplete-request" id="incomplete_' . @$pending->id . '" data-date="'.$getDate.'" data-staff="'.$v->id.'" data-value="'. $v->fname . ' ' . $v->lname .'" title="Incomplete Leave Request">#</strong>';
                                        ?>
                                    </td>
                                    <td style="background:#87be90;vertical-align: middle">
                                        <strong><?php echo @$pending->leave_in_hours && @$pending->decision ? number_format(@$pending->leave_in_hours,2) : '';?></strong>
                                    </td>
                                    <!--<td style="background:#87be90;vertical-align: middle">
                                        <strong><?php /*echo @$getValue['sick_hours'];*/?></strong>
                                    </td>-->
                                    <?php
                                }
                                ?>
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
                        <?php
                        }
                    endif;
                    ?>
                <?php
                endfor;
                ?>
                <tr class="danger">
                    <td colspan="8" style="text-align: center;text-transform: uppercase"><strong>Total Weekly Hours per Employee</strong></td>
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
                            }
                        }
                        ?>
                        <tr class="info">
                            <td colspan="6" style="text-align: right;"><strong><?php echo $v->fname.' '.$v->lname?></strong></td>
                            <td><strong><?php echo number_format($hours,2);?></strong></td>
                            <td><strong><?php echo number_format($holiday_total,2);?></strong></td>
                            <!--<td><strong><?php /*echo number_format($sick_total,2);*/?></strong></td>-->
                        </tr>
                        <?php
                        $hoursValue +=  ($total/3600);
                    endforeach;
                endif;
                ?>

                <tr class="danger">
                    <td colspan="6" style="text-align: right;text-transform: uppercase"><strong>Total Hours</strong></td>
                    <td><strong><?php echo number_format($hoursValue,2);?></strong></td>
                    <td><strong><?php echo number_format($holidayHours,2);?></strong></td>
                    <!--<td><strong><?php /*echo number_format($sickLeaveHours,2);*/?></strong></td>-->
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
        var r_id = <?php echo isset($_GET['r_id']) ? $_GET['r_id'] : 0;?>;

        hours
            .numberOnly({
                wholeNumber: true,
                isForContact:true,
                hasMaxChar:true,
                maxCharLen:4
            });

        hours.on('keyup, keydown', function(e){
            var _this = $(this);
            _this.removeAttr('style');
            var day_type = $(this).parent().parent().find('.day-type').val();
            day_type = day_type ? day_type : 0;
            var option = validateTime(day_type,_this.val());
            var tooltip_msg = _this.hasClass('time_in') ? 'Minimum time allowed is 1200.' : 'Maximum time allowed is 1300.';
            if (e.keyCode == 9 &&  (_this.val().length < 4 || (parseInt(day_type) && !option))) {  //tab pressed
                e.preventDefault(); // stops its action
                _this
                    .css({
                        border: '1px solid #ff0000',
                        background: '#e5a6a6'
                    })
                    .attr({
                        'data-original-title' : _this.val().length < 4 ? 'Please input a valid time.' : tooltip_msg
                    })
                    .tooltip('show');
            }else{
                /*if(day_type){
                    var data = $('.form-open').serializeArray();

                    data.push({name:'submit',value:1});
                    var msg_str = $('.msg-str');
                    msg_str.css({'display':'none'});
                    $.post(bu + 'timeSheetEdit',
                        data,
                        function(data){
                            if(data){
                                msg_str.css({'display':'inline'});
                            }
                        }
                    );
                }*/
            }
        });

        function validateTime(day, time){
            var valid = false;

            time = parseInt(time);
            day = parseInt(day);

            if(day && day != 1){
                switch (day){
                    case 2:
                        valid = time >= 1200;
                        break;
                    case 3:
                        valid = time <= 1300;
                        break;
                    default:
                        break;
                }
            }
            return valid;
        }

        if(r_id){
            $('html,body,table').animate({
                scrollTop: $("#pending_" + r_id).offset().top - 351
            }, 2000);
        }
        $('.has-pending-request').click(function(){
            var staff_name = $(this).data('value');
            var str = this.id;
            var id = str.split('_');
            var ele =
                '<div class="modal-body">' +
                    '<div class="row">' +
                        '<div class="col-sm-12">' +
                        'Would you like to open this Leave Application to review and/or approve it?' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-success btn-sm" id="yes-btn" data-dismiss="modal">Ok</button>' +
                    '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>' +
                '</div>';
            $(this).modifiedModal({
                html:ele,
                title: 'Pending Leave for ' + staff_name
            });

            $('#yes-btn').click(function(){
                location.replace(bu + 'staffLeaveEdit/' + id[1] + '?dtr=1');
            });
        });

        $('.data-dp').change(function(e){
            var leave_type = $(this).parent().parent().find('.leave-type');
            var time_out = $(this).parent().parent().find('.time_out');
            var time_in = $(this).parent().parent().find('.time_in');
            var hours_ = $(this).parent().parent().find('.hours');
            var day_type = $(this).parent().parent().find('.day-type');
            var staff_name = $(this).data('value');
            var str = this.id;
            var id = str.split('_');
            var df_val = $(this).data('default');
            var this_ = $(this);

            $(this).removeClass('has-incomplete');
            if($(this).hasClass('has-pending')){
                if(leave_type.val() && day_type.val()){
                    var ele =
                        '<div class="modal-body">' +
                            '<div class="row">' +
                                '<div class="col-sm-12">' +
                                    'You are selecting a single day for Leave that is already part of a whole Day or series of Days on a Leave application.' +
                                    ' Do you want to change or approve that Leave?' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-success btn-sm" id="yes-btn" data-dismiss="modal">Yes</button>' +
                            '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">No</button>' +
                        '</div>';
                    $(this).modifiedModal({
                        html:ele,
                        title: 'Pending Leave for ' + staff_name
                    });

                    $('#yes-btn').click(function(){
                        location.replace(bu + 'staffLeaveEdit/' + id[1] + '?dtr=1');
                    });

                    this_.val(df_val);
                }
            }
            else{
                if(leave_type.val() && day_type.val()){
                    var date = $(this).data('date');
                    var staff_id = $(this).data('staff');
                    var data = $('.form-open').serializeArray();

                    $(this).modifiedModal({
                        url: bu + 'timeSheetEdit/' + staff_id + '/' + date + '?req=1',
                        data: data,
                        type: 'large',
                        title: 'Request Leave for ' + staff_name
                    });
                    $(this).addClass('has-incomplete');

                    if(hours_.val()){
                        data.push({name:'submit',value:1});
                        var msg_str = $('.msg-str');
                        msg_str.css({'display':'none'});
                        $.post(bu + 'timeSheetEdit',
                            data,
                            function(data){
                                if(data){
                                    msg_str.css({'display':'inline'});
                                }
                            }
                        );
                    }
                }

                hours_
                    .removeAttr('style disabled');

                if(day_type.val()){

                    if(day_type.val() == 2 && !validateTime(day_type.val() ,time_in.val())){
                        time_in
                            .css({
                                border: '1px solid #ff0000',
                                background: '#e5a6a6'
                            })
                            .attr('data-original-title','Minimum time allowed is 1200.')
                            .tooltip('show');
                        time_out
                            .attr('data-original-title','Valid time format HHmm eg.(0100).')
                            .tooltip('hide');
                    }
                    else if(day_type.val() == 3 && !validateTime(day_type.val() ,time_out.val())){
                        time_out
                            .css({
                                border: '1px solid #ff0000',
                                background: '#e5a6a6'
                            })
                            .attr('data-original-title','Maximum time allowed is 1300.')
                            .tooltip('show');
                        time_in
                            .attr('data-original-title','Valid time format HHmm eg.(0100).')
                            .tooltip('hide');
                    }
                    else if(day_type.val() == 1){
                        hours_
                            .attr('disabled','disabled')
                            .val()
                        ;
                    }
                }
            }
        });

        $('.has-incomplete-request').click(function(e){
            e.preventDefault();
            var date = $(this).data('date');
            var staff_id = $(this).data('staff');
            var data = $('.form-open').serializeArray();
            var staff_name = $(this).data('value');
            var dp = $(this).parent().parent().parent().find('.data-dp');
            var has_incomplete = $(this);
            var ele =
                '<div class="modal-body">' +
                    '<div class="row">' +
                        '<div class="col-sm-12">' +
                        'Would you like to continue and finish this incomplete Leave Request?' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="modal-footer">' +
                    '<button type="button" class="btn btn-success btn-sm continue-request-btn" data-dismiss="modal">Continue</button>' +
                    '<button type="button" class="btn btn-danger btn-sm cancel-request-btn" data-dismiss="modal">Cancel</button>' +
                    '<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>' +
                '</div>';
            $(this).modifiedModal({
                html:ele,
                title: 'Incomplete Leave Request'
            });

            $('.continue-request-btn').click(function(){
                $(this).modifiedModal({
                    url: bu + 'timeSheetEdit/' + staff_id + '/' + date + '?req=1',
                    data: data,
                    type: 'large',
                    title: 'Request Leave for ' + staff_name
                });
            });

            $('.cancel-request-btn').click(function(){
                dp.val('');
                has_incomplete.css({display:'none'});
                hours
                    .attr('data-original-title','Valid time format HHmm eg.(0100).')
                    .tooltip('hide');
                $.post(bu + 'timeSheetEdit/' + staff_id + '/' + date + '?req=1',{request:1}, function (data) {

                });
            });
        });

        $('.preview-btn').click(function(e){
            e.preventDefault();
            var myWindow = window.open(
                this.href,
                'Pay Period Summary Report'
            );

            $.ajax(
                {
                    url: bu + 'generateStaffPaySlip/' + week_ + '/' + month_ + '/' + year_ + '?g=1',
                    dataType: "html"
                })
                .done(function( data ) {
                    if(data){
                        location.reload();
                    }
                })
                .fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                });
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
                var data_ = $('.form-open').serializeArray();
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

        $('#cancel-btn,.close').live('click',function(e){
            $('.has-incomplete').each(function(e){
                $(this).parent().parent().find('.incomplete-request').css({display:'inline'});
            });
        });

        var locked_dtr = function (){
            var has_class = $('.is-locked-dtr');
            has_class.find('select').attr('disabled','disabled');
            has_class.find('input').attr('disabled','disabled');
        };

        locked_dtr();

        var not_this_pay = function (){
            var has_class = $('.disabled-input');
            has_class.find('input').attr('disabled','disabled');
        };

        not_this_pay();

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