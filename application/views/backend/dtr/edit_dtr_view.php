<?php
echo form_open('','class="form-horizontal" role="form"');
$this_day = @$days_of_week[0];
$str_date = $thisYear . '-' . $thisMonth . '-' . date('d',strtotime($this_day));
?>
<div class="form-group">
    <label class="col-lg-1 control-label" >Date:</label>
    <div class="col-lg-2">
        <?php echo form_dropdown('month',$month,$thisMonth,'class="form-control input-sm"')?>
    </div>
    <div class="col-lg-2">
        <?php echo form_dropdown('year',$year,$thisYear,'class="form-control input-sm"')?>
    </div>
    <label class="col-lg-1 control-label" >Week:</label>
    <div class="col-sm-2">
        <?php echo form_dropdown('days',$days,$this_day,'class="form-control input-sm"')?>
    </div>
    <div class="col-lg-3">
        <input type="submit" name="submit" class="btn btn-primary" value="Save">
        <input type="submit" name="search" class="btn btn-success" value="Search">
    </div>
</div>
<div class="row">
    <div class="col-lg-7">
        <table class="table table-colored-header table-responsive">
            <?php
            //$thisDay = date('j F');
            $totalValue = 0;
            $total = 0;

            $_d = date('d',strtotime($this_day));
            $arr = array(
                $thisYear . '-12-29', $thisYear . '-12-30', $thisYear . '-12-31'
            );

            $_year = in_array($this_day, $arr) ? $thisYear + 1 : $thisYear;
            $date = mktime(0, 0, 0, $thisMonth,$_d,$_year);
            $week_number = (int)date('W', $date);
            $dt = new DateTime();
            ?>
            <thead>
            <tr>
                <th style="width: 20%;">Date</th>
                <th style="width: 80%" colspan="<?php echo count($staff)?>">Staff Name</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $totalHours = array();
            //echo $week_number.'<br/>';
            for($whatDay=2; $whatDay<=8; $whatDay++):
                $getDate =  $dt->setISODate($thisYear,$week_number,$whatDay)->format('Y-m-d');
                $day = date('Y-m-d', strtotime($getDate));
                $d = date('j',strtotime($getDate));
                $today = date('Y-m-d', strtotime($getDate)) == date('Y-m-d') ? 'today' : '';
                ?>
                <tr class="<?php echo $today;?>">
                    <td style="font-weight: bold;" rowspan="5">
                        <?php
                        echo date('l', strtotime($getDate)).'<br/>'.
                            date('d-M-Y', strtotime($getDate)).'<br/>';
                        if(count(@$working[$day]) > 0){
                            echo form_dropdown('working_type['.$day.']', $working_type, $working[$day], 'class="form-control input-sm"');
                        }else{
                            echo form_dropdown('working_type['.$day.']', $working_type, 0, 'class="form-control input-sm"');
                        }

                        ?>
                    </td>
                    <?php
                    $ref = 0;
                    if(count($staff)>0):
                        foreach($staff as $v):
                            ?>
                            <td style="background: #34386a;color: white;font-style: italic;"><?php echo $v->fname.' '.$v->lname?></td>
                            <?php
                            $ref++;
                        endforeach;
                    else:
                        ?>
                        <td>&nbsp;</td>
                    <?php
                    endif;
                    ?>
                </tr>
                <tr class="<?php echo $today;?>">
                    <?php
                    $past = '';
                    if(count($staff)>0):
                        foreach($staff as $v):
                            $thisDtr = array_key_exists($v->id, $dtr) ? $dtr[$v->id] : array();
                            $isToday = $day == date('Y-m-d');
                            $pastDay = $day < date('Y-m-d');
                            if(count($thisDtr) > 0){
                                $hasInfo = array_key_exists($day, $thisDtr);
                                if($hasInfo){
                                    $thisTime = $thisDtr[$day];
                                    $isToday = @$thisTime['time_in'] == '';
                                    $pastDay = @$thisTime['time_in'] == '';
                                }else{
                                    $isToday = false;
                                    $past =  $pastDay && !$isToday ? 'absent' : '';
                                }
                            }else{
                                $past =  $pastDay && !$isToday ? 'absent' : '';
                            }
                            ?>
                            <td style="white-space: nowrap">
                                <?php
                                $thisDtr = array_key_exists($v->id, $dtr) ? $dtr[$v->id] : array();
                                $isToday = $day == date('Y-m-d');
                                $pastDay = $day < date('Y-m-d');
                                $reason = '<a href = "#" class="reason-btn tooltip-class" data-value="'.$day.'" id="'.$v->id.'" data-toggle="tooltip" data-placement="top" title="Reason of Absent">R</a>';

                                if(count($thisDtr) > 0){
                                    $hasInfo = array_key_exists($day, $thisDtr);
                                    if($hasInfo){
                                        $thisTime = $thisDtr[$day];

                                        $isToday = @$thisTime['time_in'] == '';
                                        $pastDay = @$thisTime['time_in'] == '';
                                        ?>
                                        <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control hours"  value="<?php echo @$thisTime['time_in'];?>">
                                      <?php
                                    }else{?>
                                        <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control hours">
                                    <?php
                                    }
                                }else{?>
                                    <input type="text" name="time_in_<?php echo $v->id.'['.$d.']';?>" class="form-control hours">
                                <?php
                                }
                                ?>
                            </td>
                            <?php
                        endforeach;
                    else:
                        ?>
                        <td>&nbsp;</td>
                    <?php
                    endif;
                    ?>
                </tr>
                <tr class="<?php echo $today;?>">
                    <?php
                    if(count($staff)>0):
                        foreach($staff as $v):
                            ?>
                            <td >
                                <?php
                                $thisDtr = array_key_exists($v->id, $dtr) ? $dtr[$v->id] : array();
                                $isToday = false;
                                $dtr_id = '';
                                if(count($thisDtr) > 0){
                                    $hasInfo = array_key_exists($day, $thisDtr);
                                    if($hasInfo){
                                        $thisTime = $thisDtr[$day];
                                        $dtr_id = @$thisTime['dtr_id'];
                                        $isToday = @$thisTime['time_in'] != '' && @$thisTime['time_out'] == '';
                                        ?>
                                        <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" class="form-control hours"  value="<?php echo @$thisTime['time_out'];?>">
                                        <?php
                                    }else{?>
                                        <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" class="form-control hours">
                                    <?php
                                    }
                                }else{?>
                                    <input type="text" name="time_out_<?php echo $v->id.'['.$d.']';?>" class="form-control hours">
                                <?php
                                }
                                ?>
                            </td>
                        <?php
                        endforeach;
                    else:
                    ?>
                        <td>&nbsp;</td>
                    <?php
                    endif;
                    ?>
                </tr>
                <tr style="background:#87be90;font-weight: bold">
                    <?php
                    if(count($staff)>0):
                        foreach($staff as $v):
                            ?>
                            <td>
                                <?php
                                $thisDtr = array_key_exists($v->id, $dtr) ? $dtr[$v->id] : array();
                                $isToday = $day == date('Y-m-d');
                                $dtr_id = '';
                                if(count($thisDtr) > 0){
                                    $hasInfo = array_key_exists($day, $thisDtr);
                                    if($hasInfo){
                                        $thisTime = $thisDtr[$day];

                                        @$totalHours[$v->id] += @$thisTime['seconds'];

                                        $dtr_id = @$thisTime['dtr_id'];
                                        echo @$thisTime['hours'];
                                    }else{
                                        echo '&nbsp;';
                                    }
                                }else{
                                    echo '&nbsp;';
                                }
                                ?>
                            </td>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </tr>
                <tr>
                    <?php
                    if(count($staff)>0):
                        foreach($staff as $v):
                            ?>
                            <td>
                                <?php
                                //$thisJob = @$job_assign[$v->id];
                                $thisJob = array_key_exists($v->id, $job_assign) ? $job_assign[$v->id] : array();
                                $isToday = $day == date('Y-m-d');
                                $thisJobRef = $job;
                                if(count($thisJob) > 0){
                                    $hasInfo = array_key_exists($day, $thisJob);
                                    if($hasInfo){
                                        $thisTime = $thisJob[$day];
                                        $thisJobRef = @$thisTime['job_ref'];
                                        $isToday = $thisTime['job_ref'] == '';
                                        echo '<strong style="color:red;">'.@$thisTime['job_ref'].'</strong> <a href="#" class="edit-set-job" id="' . $v->id . '" data-value="'. $day.'"><i class="glyphicon glyphicon-pencil"></i></a>';
                                        echo '&nbsp;<a href="#" class="split-btn" id="' . $v->id . '" data-value="'. $day.'">split</a>';

                                    }else if($day <= date('Y-m-d')){
                                        echo '<strong>'.$thisJobRef.'</strong> <a href="#" class="set-job" id="' . $v->id . '" data-value="'. $day.'"><i class="glyphicon glyphicon-pencil"></i></a>';
                                        //echo '&nbsp;<a href="#">split</a>';
                                    }else{
                                        echo '&nbsp;';
                                    }
                                }else if($day <= date('Y-m-d')){
                                    echo '<strong>'.$thisJobRef.'</strong> <a href="#" class="set-job" id="' . $v->id . '" data-value="'. $day.'"><i class="glyphicon glyphicon-pencil"></i></a>';
                                    //echo '&nbsp;<a href="#">split</a>';
                                }else{
                                    echo '&nbsp;';
                                }
                                ?>
                            </td>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </tr>
                <?php
            endfor;
            ?>
            <tr>
                <td class="align-right font-bold">Total for week</td>
                <?php
                if(count($staff)>0):
                    foreach($staff as $v):
                        $total = @$totalHours[$v->id];
                        ?>
                        <td style="background: #1ccc79!important;font-weight: bold">
                            <?php
                            //$minutes = (int)($total/60);
                            $hoursValue = number_format(($total/3600),2);
                            //$minutesValue = $minutes - ($hoursValue * 60);
                            //$secondsValue = $v->hours - (($hoursValue * 3600) + ($minutesValue * 60));
                            //$hours = str_pad($hoursValue, 2, '0', STR_PAD_LEFT) . "." . str_pad($minutesValue, 2, '0', STR_PAD_LEFT);
                            echo $hoursValue;
                            ?>
                        </td>
                    <?php
                    endforeach;
                else:
                ?>
                    <td colspan="3" style="background: #1ccc79!important;font-weight: bold">&nbsp;</td>
                <?php
                endif;
                ?>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
echo form_close();


?>
<style>
    table.table tbody td{
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

</style>
<script>
    $(function(e){
        var hours = $('.hours');
        hours.numberOnly({
            wholeNumber: true,
            isForContact:true,
            hasMaxChar:true,
            maxCharLen:4
        });

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