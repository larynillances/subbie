<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('getPaymentStartDate'))
{
    function getPaymentStartDate($year,$end = "",$month = 'April', $day = 'tuesday',$start = 2){

        $month_day = $year.'-04-01';
        $next_day = first_day_of_month($year,$month,$day);
        $next_day = date('Y-m-d',strtotime('-1 day '.$next_day));
        $date = new DateTime($month_day);
        $week = $date->format("W");

        $end_year = $year + 1;
        $end_month_day = $end_year.'-03-31';//$this->first_day_of_month($end_year,$month,$day);
        if($end){
            $end_month_day = strtotime($end) < strtotime($end_month_day) ? $end : $end_month_day;
        }
        //$this_month = date('m',strtotime($end_month_day));
        //$this_day = date('d',strtotime($end_month_day));
        $whatDay = date('N',strtotime($month_day));
        $year_week = array();

        if($year == 2015){

            $start_week =  new DatePeriod(
                new DateTime("$year-W$week-$whatDay"),
                new DateInterval('P1W'),
                new DateTime("$next_day T23:59:59Z")
            );
            foreach ($start_week as $week => $monday) {
                if($monday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$monday->format('Y-m-d')] = $monday->format('W');
                }

            }

            $_date = new DateTime($next_day);
            $_week = $_date->format("W");

            $weeksPeriod = new DatePeriod(
                new DateTime("$year-W$_week-$start"),
                new DateInterval('P1W'),
                new DateTime("2015-07-26T23:59:59Z")
            );
            foreach ($weeksPeriod as $week => $monday) {
                if($monday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$monday->format('Y-m-d')] = $monday->format('W');
                }
            }
            $start = 1;
            $weeksPeriod = new DatePeriod(
                new DateTime("$year-W31-$start"),
                new DateInterval('P1W'),
                new DateTime("$end_month_day T23:59:59Z")
            );
            foreach ($weeksPeriod as $week => $monday) {
                if($monday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$monday->format('Y-m-d')] = $monday->format('W');
                }
            }
        }else if($year > 2015){
            $start = 1;
            $start_week =  new DatePeriod(
                new DateTime("$year-W$week-$whatDay"),
                new DateInterval('P1W'),
                new DateTime("$next_day T23:59:59Z")
            );
            foreach ($start_week as $week => $monday) {
                if($monday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$monday->format('Y-m-d')] = $monday->format('W');
                }
            }

            $_date = new DateTime($next_day);
            $_week = $_date->format("W");

            $weeksPeriod = new DatePeriod(
                new DateTime("$year-W$_week-$start"),
                new DateInterval('P1W'),
                new DateTime("2015-07-26T23:59:59Z")
            );

            foreach ($weeksPeriod as $week => $monday) {
                if($monday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$monday->format('Y-m-d')] = $monday->format('W');
                }
            }
        }else{
            $weeksPeriod = new DatePeriod(
                new DateTime("$year-W$week-$start"),
                new DateInterval('P1W'),
                new DateTime("$end_year-03-31T23:59:59Z")
            );
            $year_week = array();
            foreach ($weeksPeriod as $week => $monday) {
                if($monday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$monday->format('Y-m-d')] = $monday->format('W');
                }
            }
        }

        return $year_week;
    }
}

if(!function_exists('first_day_of_month')){
    function first_day_of_month($year,$month = 'April',$day="Tuesday"){
        $_day = $year > 2015 ? 'Monday' : $day;
        $day = new DateTime(sprintf("First $_day of $month %s", $year));
        return $day->format('Y-m-d');
    }
}

if(!function_exists('last_day_of_month')){
    function last_day_of_month($year,$month = 'March',$day="Tuesday"){
        $_day = $year > 2015 ? 'Monday' : $day;
        $day = new DateTime(sprintf("Last $_day of $month %s", $year));
        return $day->format('Y-m-d');
    }
}

if(!function_exists('getWeekDays')){
    function getWeekDays($m,$d,$y,$std = 2){
        $arr = array(
            $y . '-12-29', $y . '-12-30', $y . '-12-31'
        );
        $_day = "$y-$m-$d";
        $_year = in_array($_day, $arr) ? $y + 1 : $y;
        $date = mktime(0, 0, 0, $m,$d,$_year);
        $week = (int)date('W', $date);
        $dt = new DateTime();
        $this->data['days_of_week'] = array();
        $ref = 0;
        $date_ = new DateTime($date);
        $start_ = StartWeekNumber($date_->format('W'),$y);

        for($whatDay=$start_['start_day']; $whatDay<=$start_['end_day']; $whatDay++){
            $getDate =  $dt->setISODate($y, $week , $whatDay)->format('Y-m-d');
            $this->data['days_of_week'][$ref] = $getDate;
            $ref++;
        }

        return $this->data['days_of_week'];
    }
}

if(!function_exists('getNumberOfWeeks')){
    function getNumberOfWeeks($year, $month){
        $days = getFirstNextLastDay($year,$month);
        $this->data['days'] = array();
        $thisDays = array();
        if(count($days) > 0){
            foreach($days as $v){
                $thisDays[$v] = getWeeks($v,date('l',strtotime($v)));
            }
        }
        $this->data['days'] = $thisDays;
        return $this->data['days'];
    }
}

if(!function_exists('getDaysInWeek')){
    function getDaysInWeek($year = '',$week_number = ''){

        $days = array();
        $year = $year ? $year : date('Y');
        $week_number = $week_number ? $week_number : date('W');

        $week_start = StartWeekNumber($week_number,$year);
        $_start_day = $week_start['start_day'];
        $_end_day = $week_start['end_day'];

        $dt = new DateTime();

        for($day=$_start_day; $day<=$_end_day; $day++)
        {

            $getDate =  $dt->setISODate($year,$week_number,$day)->format('Y-m-d');

            $date = new DateTime($getDate);
            $days[$date->format('Y-m-d')] = $date->format('l');
        }

        return $days;
    }
}

if(!function_exists('getWeeksNumberInMonth')){
    function getWeeksNumberInMonth($year, $month){
        $days = getFirstNextLastDay($year,$month);
        $thisDays = array();
        if(count($days) > 0){
            foreach($days as $key=>$v){
                $week_end = $key == 30 && $year == 2015 ? date('Y-m-d',strtotime('+5 days '.$v)) : date('Y-m-d',strtotime('+6 days '.$v));
                if(date('m',strtotime($week_end)) == $month){

                    $thisDays[$key] = $key;
                }
                else{
                    if($month == 12){
                        $thisDays[$key] = $key;
                    }
                }
            }
        }
        ksort($thisDays);
        $days_array = $thisDays;
        return $days_array;
    }
}

if(!function_exists('getWeekDateInMonth')){
    function getWeekDateInMonth($year, $month){
        $days = getFirstNextLastDay($year,$month);
        $thisDays = array();
        if(count($days) > 0){
            foreach($days as $key=>$v){
                $week_end = $key == 30 && $year == 2015 ? date('Y-m-d',strtotime('+5 days '.$v)) : date('Y-m-d',strtotime('+6 days '.$v));
                if(date('m',strtotime($week_end)) == $month){

                    $thisDays[$key] = $v;
                }
                else{
                    if($month == 12){
                        $thisDays[$key] = $v;
                    }
                }
            }
        }
        $days_array = $thisDays;
        return $days_array;
    }
}

if(!function_exists('getWeeks')){
    function getWeeks($date, $rollover = 'tuesday')
    {
        $cut = substr($date, 0, 8);
        $daylen = 86400;

        $timestamp = strtotime($date);
        $first = strtotime($cut . "00");
        $elapsed = ($timestamp - $first) / $daylen;

        $i = 1;
        $weeks = 1;

        for($i; $i<=$elapsed; $i++)
        {
            $dayfind = $cut . (strlen($i) < 2 ? '0' . $i : $i);
            $daytimestamp = strtotime($dayfind);

            $day = strtolower(date("l", $daytimestamp));

            if($day == strtolower($rollover))  $weeks ++;
        }

        return $weeks;
    }
}

if(!function_exists('getWeekNumberOfDateInYear')){
    function getWeekNumberOfDateInYear($date){
        $date = date('Y-m-d',strtotime($date));
        $_date = new DateTime($date);
        $week = $_date->format("W");

        return $week;
    }
}

if(!function_exists('getYear')){
    function getYear($cutoff = 2010){
        // current year
        $now = date('Y');
        $year = array();

        // build years menu
        for ($y=$now; $y>=$cutoff; $y--) {
            $year[$y] = $y;
        }

        return $year;
    }
}

if(!function_exists('getMonth')){
    function getMonth(){
        $month = array();

        for ($m=1; $m<=12; $m++) {
            $date = '2014-'.$m.'-01';
            $month_str = str_pad($m,2,'0',STR_PAD_LEFT);
            $month[$month_str] = date('F', strtotime($date));
        }
        return $month;
    }
}

if(!function_exists('getWeekInYear')){
    function getWeekInYear($end_year,$start = 2){
        $year_week = array();

        if($end_year == 2015){
            $first_year_week_period = new DatePeriod(
                new DateTime("$end_year-W01-$start"),
                new DateInterval('P1W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );

            $second_year_week_period = new DatePeriod(
                new DateTime("2015-W31-1"),
                new DateInterval('P1W'),
                new DateTime("2015-12-31T23:59:59Z")
            );
            foreach ($first_year_week_period as $week => $tuesday) {
                $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
            }
            foreach ($second_year_week_period as $week => $tuesday) {
                $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
            }
        }else if($end_year < 2015){
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$end_year-W01-$start"),
                new DateInterval('P1W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
            }
        }else{
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$end_year-W01-1"),
                new DateInterval('P1W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
            }
        }

        return $year_week;
    }
}

if(!function_exists('getWeekBetweenDates')){
    function getWeekBetweenDates($start,$end,$day_start = 'tuesday'){
        $year = date('Y',strtotime($start));
        $end_year = date('Y',strtotime($end));
        $year_week = array();

        if($year == 2015 || $end_year == 2015){
            $_start_date = new DateTime($start);
            $_start_week = $_start_date->format('W');
            $_day = $_start_week > 30 ? 'monday' : $day_start;
            $start = date('Y-m-d',strtotime('-1 day '.$start));
            $start = date('Y-m-d',strtotime('next '.$_day.' '.$start));

            $_end_date = new DateTime($end);
            $_end_week = $_end_date->format('W');
            $_day = $_end_week > 30 ? 'monday' : $day_start;
            $end = date('Y-m-d',strtotime('+1 day '.$end));
            $end = date('Y-m-d',strtotime('last '.$_day.' '.$end));

            $first_year_week_period = new DatePeriod(
                new DateTime("$start"),
                new DateInterval('P1W'),
                new DateTime($end."T23:59:59Z")
            );

            foreach ($first_year_week_period as $week => $day) {
                $year_week[$day->format('W-Y')] = $day->format('Y-m-d');
            }

            if($_start_week <= 30 &&  $_end_week >= 30){
                $second_year_week_period = new DatePeriod(
                    new DateTime("$year-W31-1"),
                    new DateInterval('P1W'),
                    new DateTime($end."T23:59:59Z")
                );

                foreach ($second_year_week_period as $week => $day) {
                    $year_week[$day->format('W-Y')] = $day->format('Y-m-d');
                }
            }

        }
        else if($year < 2015){
            $start = date('Y-m-d',strtotime('-1 day '.$start));
            $start = date('Y-m-d',strtotime('next '.$day_start.' '.$start));

            $end = date('Y-m-d',strtotime('+1 day '.$end));
            $end = date('Y-m-d',strtotime('last '.$day_start.' '.$end));

            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$start"),
                new DateInterval('P1W'),
                new DateTime($end."T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $day) {
                $year_week[$day->format('W-Y')] = $day->format('Y-m-d');
            }
        }
        else{

            $day_start = 'monday';
            $start = date('Y-m-d',strtotime('-1 day '.$start));
            $start = date('Y-m-d',strtotime('next '.$day_start.' '.$start));

            $end = date('Y-m-d',strtotime('+1 day '.$end));
            $end = date('Y-m-d',strtotime('last '.$day_start.' '.$end));

            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$start"),
                new DateInterval('P1W'),
                new DateTime($end."T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $day) {
                $year_week[$day->format('W-Y')] = $day->format('Y-m-d');
            }
        }

        return $year_week;
    }
}

if(!function_exists('getWeekInYearBetweenDates')){
    function getWeekInYearBetweenDates($end_year,$start_year = 2014,$start = 2,$start_week = 1, $interval = 1){
        $year_week = array();
        $start_week = str_pad($start_week,2,'0',STR_PAD_LEFT);
        if($end_year == 2015){
            $first_year_week_period = new DatePeriod(
                new DateTime("$start_year-W$start_week-$start"),
                new DateInterval('P'.$interval.'W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );

            $second_year_week_period = new DatePeriod(
                new DateTime("$start_year-W31-1"),
                new DateInterval('P'.$interval.'W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($first_year_week_period as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
                }
            }
            foreach ($second_year_week_period as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
                }
            }
        }else if($end_year < 2015){
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$start_year-W$start_week-$start"),
                new DateInterval('P'.$interval.'W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
                }
            }
            $second_year_week_period = new DatePeriod(
                new DateTime("$start_year-W31-1"),
                new DateInterval('P'.$interval.'W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($second_year_week_period as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
                }
            }
        }else{
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$start_year-W$start_week-1"),
                new DateInterval('P'.$interval.'W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
                }
            }
        }

        return $year_week;
    }
}

if(!function_exists('getYearNumWeek')){
    function getYearNumWeek($year,$start = 2){
        $year_week = array();
        if($year == 2015){
            $first_year_week_period = new DatePeriod(
                new DateTime("$year-W01-$start"),
                new DateInterval('P1W'),
                new DateTime("$year-12-31T23:59:59Z")
            );

            $second_year_week_period = new DatePeriod(
                new DateTime("$year-W31-1"),
                new DateInterval('P1W'),
                new DateTime("$year-12-31T23:59:59Z")
            );
            foreach ($first_year_week_period as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('Y-m-d')] = $tuesday->format('Y-m-d');
                }
            }
            foreach ($second_year_week_period as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('Y-m-d')] = $tuesday->format('Y-m-d');
                }
            }
        }else if($year < 2015){
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$year-W01-$start"),
                new DateInterval('P1W'),
                new DateTime("$year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('Y-m-d')] = $tuesday->format('Y-m-d');
                }
            }
        }else{
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$year-W01-1"),
                new DateInterval('P1W'),
                new DateTime("$year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('Y-m-d')] = $tuesday->format('Y-m-d');
                }
            }
        }

        return $year_week;
    }
}

if(!function_exists('getFirstNextLastDay')){
    function getFirstNextLastDay($y, $m, $week = '')
    {
        $start_date = $y.'-'.$m.'-01';
        $start_day = date('l',strtotime($start_date));

        $day = strtotime($start_date) > strtotime('2015-07-26') ? 'monday' : 'tuesday';
        $begin = new DateTime("first $day of $y-$m");
        $begin = $begin->modify('-1 week');

        $end = new DateTime("last $day of $y-$m");
        $end = $end->modify( '+1 day' );

        $interval = DateInterval::createFromDateString('next '.$day);
        $date_range = new DatePeriod($begin, $interval ,$end);
        $date = array();
        $year = date('Y');
        $week_number = date('W');
        $days = array();
        for($day_=1; $day_<=7; $day_++)
        {
            $days[strtolower(date('l',strtotime($year."W".$week_number.$day_)))] = $day_;
        }
        if(strtolower($start_day) != $day){

            if($days[strtolower($start_day)] > $days[$day]){

                $date_ = new DateTime($start_date);
                $week_num = $date_->format('W');
                $next_day = date('Y-m-d',strtotime('last '. $day .' '.$start_date));
                $date[$week_num] = $next_day;
            }
        }
        if(count($date_range) > 0){
            foreach($date_range as $dv){
                $this_date = $dv->format('Y-m-d');
                $week_num = $dv->format('W');
                //$month = $dv->format('m');

                $week_end = $week_num == 30 && $year == 2015 ? date('Y-m-d',strtotime('+5 days '.$this_date)) : date('Y-m-d',strtotime('+6 days '.$this_date));
                if(date('m',strtotime($week_end)) == date('m',strtotime($week_end))){
                    $date[$week_num] = $this_date;
                }
                $date[$week_num] = $this_date;
            }
        }
        $data = array();
        if($week){
            @$data[$week] = $date[$week];
        }
        return $week ? $data : $date;
    }
}

if(!function_exists('createDateRangeArray')){
    function createDateRangeArray($strDateFrom, $strDateTo)
    {
        // takes two dates formatted as YYYY-MM-DD and creates an
        // inclusive array of the dates between the from and to dates.

        // could test validity of dates here but I'm already doing
        // that in the main script

        $aryRange = array();

        $iDateFrom = mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
        $iDateTo = mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

        if ($iDateTo>=$iDateFrom)
        {
            array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
            while ($iDateFrom<$iDateTo)
            {
                $iDateFrom+=86400; // add 24 hours
                array_push($aryRange,date('Y-m-d',$iDateFrom));
            }
        }
        return $aryRange;
    }
}

if(!function_exists('payPeriodDropdown')){
    function payPeriodDropdown(){
        $data = array(
            date('Ym') => 'Current Month'
        );
        $num = array(1,2,3,4,6,12);

        foreach($num as $val){
            $str = date('Ym',strtotime('-' . $val . ' month'));
            $data[$str] = 'Last ' . ($val != 1 ? $val .' ' : '') . 'Month' . ($val > 1 ? 's' : '');
        }

        return $data;
    }
}
