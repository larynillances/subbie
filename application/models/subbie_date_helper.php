<?php

class Subbie_Date_Helper extends CI_Model{
    var $holidays = array();
    const oneday = 86400; // 86400 seconds = 1 Day
    const weekend = 172800; // 172800 seconds = 2 Days

    function __construct(){
        // Call the Model constructor
        parent::__construct();

        if ($this->db->table_exists('tbl_holiday')){
            $query = $this->db->get('tbl_holiday');

            if($query->num_rows() > 0){
                foreach ($query->result() as $row){
                    $dateStart = "";
                    $dateEnd = "";
                    switch($row->type){
                        case 1:
                            $dateStart = date('Y') . "-" . date('m-d', strtotime($row->date));
                            break;
                        case 2:
                            $dateStart = $row->date;
                            $dateEnd = $row->date_to ? $row->date_to : "";
                            break;
                    }

                    if($dateEnd){
                        $dateBetween = createDateRangeArray($dateStart, $dateEnd);
                        if(count($dateBetween)>0){
                            foreach($dateBetween as $v){
                                if(!in_array($v, $this->holidays)){
                                    $this->holidays[] = $v;
                                }
                            }
                        }

                    }
                    else{
                        $this->holidays[] = $dateStart;
                    }
                }
            }
        }
    }

    function returnHoliday($year = array()){
        $h = $this->holidays;
        if(count($year) > 0){
            if(count($h) > 0){
                foreach($h as $k=>$v){
                    $y = date('Y', strtotime($v));
                    if(!in_array($y, $year)){
                        unset($h[$k]);
                    }
                }
            }
        }

        return $h;
    }

    function addBusinessDays($start_date, $business_days, $has_saturday = 0) {
        // If $start_date is on the weekend, start on following Monday
        if (date('N', $start_date) == 6 && $has_saturday == 0) { // If start date is on Saturday
            $new_start_date = $start_date + self::weekend;
        }
        elseif (date('N', $start_date) == 7) { // If start date is on Sunday
            $new_start_date = $start_date + self::oneday;
        }
        else {
            $new_start_date = $start_date;
        }

        // Add business days to the start date
        $due_date = $new_start_date + $business_days * self::oneday;

        $working_business_days = $has_saturday == 1 ? 6 : 5;
        $weekend_days = $has_saturday == 1 ? self::oneday : self::weekend;
        $until_day = $has_saturday == 1 ? 7 : 6;

        // For every 5 business days, add 2 more for the weekend
        $extra_days_for_weekend = floor($business_days / $working_business_days) * $weekend_days;
        $due_date += $extra_days_for_weekend;

        // If remainder of business days causes due date to land on or after the weekend
        if (($business_days % $working_business_days) + date('N', $new_start_date) >= $until_day) {
            $due_date += $weekend_days; // Add 2 days to compensate for the weekend
        }

        if(count($this->holidays) > 0){
            foreach($this->holidays as $holiday){
                $time_stamp = strtotime($holiday);

                // If the holiday falls between the start date and end date
                // and is on a weekday
                // Or if $new_start_date is on a holiday
                if (($start_date <= $time_stamp && $time_stamp <= $due_date && date("N", $time_stamp) < $until_day) ||
                    date("Y-m-d", $new_start_date) == $holiday) {
                    $due_date += self::oneday;

                    if (date('N', $due_date) >= $until_day) { // If due date on Saturday or Sunday
                        $due_date += $weekend_days;
                    }
                }
            }
        }

        return $due_date;
    }

    function getWorkingDays($start_date, $end_date, $holidays = array()){
        if(count($holidays)==0){
            $holidays = $this->holidays;
        }

        $start_ts = strtotime($start_date);
        $end_ts = strtotime($end_date);
        foreach ($holidays as & $holiday) {
            $holiday = strtotime($holiday);
        }
        $working_days = 0;
        $tmp_ts = $start_ts;
        while ($tmp_ts <= $end_ts) {
            $tmp_day = date('D', $tmp_ts);
            if (!($tmp_day == 'Sun') && !($tmp_day == 'Sat') && !in_array($tmp_ts, $holidays)) {
            //if (!($tmp_day == 'Sun') && !in_array($tmp_ts, $holidays)) {
                $working_days++;
            }
            $tmp_ts = strtotime('+1 day', $tmp_ts);
        }
        return $working_days;
    }


    function date_difference($date1timestamp, $date2timestamp) {
        $all = round(($date1timestamp - $date2timestamp) / 60);
        $d = floor ($all / 1440);
        $h = floor (($all - $d * 1440) / 60);
        $m = $all - ($d * 1440) - ($h * 60);

        //Since you need just hours and mins
        return (Object)array(
            'hours' => $h>9 ? $h : str_pad($h, 2, '0', STR_PAD_LEFT),
            'minutes' => $m>9 ? $m : str_pad($m, 2, '0', STR_PAD_LEFT)
        );
    }

    function getDayIntervalMonth($strDateFrom, $strDateTo, $options = array()){
        $default = array(
            'interval' => "next sunday",
            'format' => "Y-m-d"
        );

        $option = count($options) > 0 ? array_merge($default, $options) : $default;
        $option = (Object)$option;

        $p = new DatePeriod(
            $strDateFrom,
            DateInterval::createFromDateString($option->interval),
            $strDateTo
        );

        $period = array();
        foreach($p as $dt){
            $period[] = $dt->format($option->format);
        }

        return $period;
    }
}