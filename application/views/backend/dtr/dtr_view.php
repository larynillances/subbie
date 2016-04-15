<?php
$thisday = date('j F');
$year = date('Y');
$month = date('m');
$d = date('d');
$date = mktime(0, 0, 0, $month,$d,$year);
$week = (int)date('W', $date);
$week_number = $week;
$totalValue = 0;
$total = 0;
$dateValue = date('j F');
$dt = new DateTime;
?>
<div class="row">
    <div class="col-lg-6">
        <table class="table table-colored-header table-responsive">
        <thead>
        <tr>
            <th style="width: 30%;">Date</th>
            <th style="width: 70%" colspan="<?php echo count($staff)?>">Staff Name</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $totalHours = array();
        for($whatDay=2; $whatDay<=8; $whatDay++):
            $getDate =  $dt->setISODate(date('Y'), $week_number , $whatDay)->format('Y-m-d');
            $day = date('Y-m-d', strtotime($getDate));
            $today = date('Y-m-d', strtotime($getDate)) == date('Y-m-d') ? 'today' : '';
            ?>
            <tr class="<?php echo $today;?>">
                <td style="font-weight: bold;" rowspan="5">
                    <?php echo date('l', strtotime($getDate)).'<br/>'.
                        date('d-M-Y', strtotime($getDate));?>
                </td>
                <?php
                $ref = 0;
                if(count($staff)>0):
                    foreach($staff as $v):
                        ?>
                        <td style="background: #34386a;color: white"><?php echo $v->fname.' '.$v->lname?></td>
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
                        <td class="<?php echo $past;?>" style="white-space: nowrap">
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
                                    echo @$thisTime['time_in'];
                                }else{
                                    $thisAbsent = array_key_exists($v->id, $absent) ? $absent[$v->id] : array();
                                    if(count($thisAbsent) > 0){
                                        $hasAbsent = array_key_exists($day, $thisAbsent);
                                        if($hasAbsent){
                                            $thisTime = $thisAbsent[$day];
                                            echo @$thisTime;
                                        }else{
                                            echo $pastDay ? $reason.' | Absent!' : '&nbsp;';
                                        }
                                    }else{
                                        echo $pastDay ? $reason.' | Absent!' : '&nbsp;';
                                    }
                                }
                            }else{
                                $thisAbsent = array_key_exists($v->id, $absent) ? $absent[$v->id] : array();
                                if(count($thisAbsent) > 0){
                                    $hasAbsent = array_key_exists($day, $thisAbsent);
                                    if($hasAbsent){
                                        $thisTime = $thisAbsent[$day];
                                        echo @$thisTime;
                                    }else{
                                        echo $pastDay ? $reason.' | Absent!' : '&nbsp;';
                                    }
                                }else{
                                    echo $pastDay ? $reason.' | Absent!' : '&nbsp;';
                                }
                            }

                            if($isToday){
                                echo '<a href = "'. base_url() .'timeSheetLog/in/' . $v->id . '" class="btn-flat">In</a>';
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
                        <td class="<?php echo $past;?>">
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
                                    echo @$thisTime['time_out'];
                                }else{
                                    echo '&nbsp;';
                                }
                            }else{
                                echo '&nbsp;';
                            }

                            if($isToday){
                                if($day == date('Y-m-d')){
                                    echo '<a href = "'. base_url() .'timeSheetLog/out/' . $v->id . '/'. $dtr_id .'" class="btn-flat">Out</a>';
                                }
                                else{
                                    echo 'Not out!';
                                }
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
                        <td class="<?php echo $past;?>">
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
                else:
                    ?>
                    <td>&nbsp;</td>
                <?php
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
                            $thisJob = array_key_exists($v->id, $job_assign) ? $job_assign[$v->id] : array();
                            $isToday = $day == date('Y-m-d');
                            if(count($thisJob) > 0){
                                $hasInfo = array_key_exists($day, $thisJob);
                                if($hasInfo){
                                    $thisTime = $thisJob[$day];
                                    $isToday = $thisTime == '';
                                    echo '<strong>'.@$thisTime.'</strong><br/>';
                                    echo '<br/>';
                                }else{
                                    echo '&nbsp;';
                                }
                            }else{
                                echo '&nbsp;';
                            }

                            if($isToday){
                                echo '<a href="#" class="set-job btn-flat" id="' . $v->id . '" data-value="'. $day.'">Set Job</a>';
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
                        $minutes = (int)($total/60);
                        $hoursValue = (int)($minutes/60);
                        $minutesValue = $minutes - ($hoursValue * 60);
                        //$secondsValue = $v->hours - (($hoursValue * 3600) + ($minutesValue * 60));
                        $hours = str_pad($hoursValue, 2, '0', STR_PAD_LEFT) . "." . str_pad($minutesValue, 2, '0', STR_PAD_LEFT);
                        echo $hours;
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
        $('.reason-btn').click(function(e){
            e.preventDefault();
            var url = bu + 'absentReason/' + this.id + '/' + $(this).attr('data-value');
            $('.sm-title').html('Reason for absent');
            $('.sm-load-page').load(url);
            $('.sm-modal').modal();
        });
        $('.set-job').click(function(e){
            e.preventDefault();
            var url = bu + 'setJobAssign/' + this.id + '/' + $(this).attr('data-value') + '/main/add';
            $('.sm-title').html('Set Staff Job');
            $('.sm-load-page').load(url);
            $('.sm-modal').modal();
        });
    });
</script>