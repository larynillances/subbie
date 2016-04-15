<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('CurrencyConverter'))
{
    function CurrencyConverter($code,$default_currency = 'NZD'){
        $amount = urlencode(1);
        $from = urlencode($default_currency);
        $to = urlencode($code);

        $url     = "http://www.google.com/finance/converter?a=$amount&from=$from&to=$to";

        $get = file_get_contents($url);
        $get = explode("<span class=bld>",$get);
        $get = explode("</span>",$get[1]);
        $converted_amount = preg_replace("/[^0-9\.]/", null, $get[0]);

        return $converted_amount;
    }

}


if ( ! function_exists('BreakTimeDeduction'))
{
    function BreakTimeDeduction($time_in,$time_out,$is_seconds = false){
        $total_deduction = 0;
        if($time_in && $time_out){

            $afternoon = strtotime(date('g:i a',strtotime($time_out)));
            $morning = strtotime(date('g:i a',strtotime($time_in)));
            $afternoon_time = strtotime("01:30 pm");
            $morning_time = strtotime("12:00 pm");
            $is_afternoon = ($afternoon - $afternoon_time);
            $is_morning = ($morning - $morning_time);
            if($is_morning <= 0 && $is_afternoon >= 0){
                $total_deduction += $is_seconds ? (30 * 60) :0.50;
            }
        }

        return $total_deduction;
    }

}

if ( ! function_exists('maxValueInArray'))
{
    function maxValueInArray($array, $keyToSearch)
    {
        $currentMax = NULL;
        foreach($array as $arr)
        {
            foreach($arr as $key => $value)
            {
                if(is_array($value))
                {
                    foreach($value as $k => $v)
                    {
                        if ($k == $keyToSearch && ($v >= $currentMax))
                        {
                            $currentMax = $v;
                        }
                    }
                }
                else
                {
                    if ($key == $keyToSearch && ($value >= $currentMax))
                    {
                        $currentMax = $value;
                    }
                }
            }
        }

        return $currentMax;
    }

}

if ( ! function_exists('StartWeekNumber'))
{
    function StartWeekNumber($week_number,$year = 2015){
        $result = array();

        if($year == 2015){
            if($week_number > 30){
                $_start_day = 1;
                $_end_day = 7;
                $_days_count = 6;
            }else if($week_number == 30){
                $_start_day = 2;
                $_end_day = 7;
                $_days_count = 5;
            }else{
                $_start_day = 2;
                $_end_day = 8;
                $_days_count = 6;
            }

        }else if($year < 2015){
            $_start_day = 2;
            $_end_day = 8;
            $_days_count = 6;
        }else{
            $_start_day = 1;
            $_end_day = 7;
            $_days_count = 6;
        }

        $result['start_day'] = $_start_day;
        $result['end_day'] = $_end_day;
        $result['days_count'] = $_days_count;

        return $result;
    }
}

if ( ! function_exists('DateDiff'))
{
    function DateDiff($interval, $datefrom, $dateto, $using_timestamps = false) {
        /*
        $interval can be:
        yyyy - Number of full years
        q - Number of full quarters
        m - Number of full months
        y - Difference between day numbers
            (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
        d - Number of full days
        w - Number of full weekdays
        ww - Number of full weeks
        h - Number of full hours
        n - Number of full minutes
        s - Number of full seconds (default)
        */

        if (!$using_timestamps) {
            $datefrom = strtotime($datefrom, 0);
            $dateto = strtotime($dateto, 0);
        }
        $difference = $dateto - $datefrom; // Difference in seconds

        switch($interval) {

            case 'yyyy': // Number of full years
                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
                    $years_difference--;
                }
                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
                    $years_difference++;
                }
                $datediff = $years_difference;
                break;
            case "q": // Number of full quarters
                $quarters_difference = floor($difference / 8035200);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $quarters_difference--;
                $datediff = $quarters_difference;
                break;
            case "m": // Number of full months
                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $months_difference--;
                $datediff = $months_difference;
                break;
            case 'y': // Difference between day numbers
                $datediff = date("z", $dateto) - date("z", $datefrom);
                break;
            case "d": // Number of full days
                $datediff = floor($difference / 86400);
                break;
            case "w": // Number of full weekdays
                $days_difference = floor($difference / 86400);
                $weeks_difference = floor($days_difference / 7); // Complete weeks
                $first_day = date("w", $datefrom);
                $days_remainder = floor($days_difference % 7);
                $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
                if ($odd_days > 7) { // Sunday
                    $days_remainder--;
                }
                if ($odd_days > 6) { // Saturday
                    $days_remainder--;
                }
                $datediff = ($weeks_difference * 5) + $days_remainder;
                break;
            case "ww": // Number of full weeks
                $datediff = floor($difference / 604800);
                break;
            case "h": // Number of full hours
                $datediff = floor($difference / 3600);
                break;
            case "n": // Number of full minutes
                $datediff = floor($difference / 60);
                break;
            default: // Number of full seconds (default)
                $datediff = $difference;
                break;
        }
        return $datediff;
    }
}

if ( ! function_exists('DisplayArray'))
{
    function DisplayArray($ar, $color = "F00"){
        echo '<pre style="z-index:9999;color:#'.$color.'">';
        print_r($ar);
        echo '</pre><br style="clear:both;" /><br />';
    }
}

if ( ! function_exists('ArrayWalk'))
{
    function ArrayWalk($array, $append){
        $ar = array();
        if(count($array)>0){
            foreach($array as $k=>$v){
                $ar[$k] = $append . $v;
            }
        }

        return $ar;
    }
}

if(! function_exists('ErrorMessage'))
{
    function ErrorMessage($code){
        $message = '';
        switch($code){
            case 'e_data_error':
                $message = 'Failed Data Retrieval: cannot continue to the process due to failed in retrieving the data';
                break;
            case 'e_url_error':
                $message = 'Failed URL Request: your url request is invalid. Please try again';
                break;
        }
        exit($message);
    }
}

if(! function_exists('ConvertCurrency')){
    function ConvertCurrency($from_Currency, $to_Currency, $amount) {
        $amount = urlencode($amount);
        $from_Currency = urlencode($from_Currency);
        $to_Currency = urlencode($to_Currency);
        $url = "http://www.google.com/finance/converter?a=$amount&from=$from_Currency&to=$to_Currency";
        $ch = curl_init();
        $timeout = 0;
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $rawdata = curl_exec($ch);
        curl_close($ch);
        $data = explode('bld>', $rawdata);
        $data = explode($to_Currency, $data[1]);
        return round($data[0], 2);
        }
}

if(! function_exists('CalculateAge')){
    function CalculateAge($dateString =  "11/14/1991"){
        $birthDate = date('m/d/Y', strtotime($dateString));
        //explode the date to get month, day and year
        $birthDate = explode("/", $birthDate);
        //get age from date or birthdate
        $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md") ? ((date("Y")-$birthDate[2])-1):(date("Y")-$birthDate[2]));
        return $age;
    }
}

if(! function_exists('CalculateDays')){
    function CalculateDays($date1, $date2){

        $days_between = ceil(abs($date1 - $date2) / 86400);
        return $days_between;
    }
}

if(! function_exists('DisplayTimeSlot')){
    function DisplayTimeSlot($day_of_week, $time_table){

        if(isset($time_table['timeTable'])){
            $time_table = $time_table['timeTable'];
        }
        $list = '';
        foreach($day_of_week as $dayNum=>$dayName){
            $list .= '<ul>';
            foreach($time_table as $day=>$time){
                if($dayNum == $day){
                    $list .= '<li>';
                    $list .= $dayName;
                    $list .= '<ul>';
                    foreach($time as $slot){
                        $list .= '<li>';
                        $list .= $slot->start !== "any" ? date('h:i a', $slot->start) : "Anytime";
                        $list .= "-";
                        $list .= $slot->end !== "any" ? date('h:i a', $slot->end) : "Anytime";
                        $list .= '</li>';
                    }

                    $list .= '</ul>';
                    $list .= '</li>';
                }
            }
            $list .= '</ul>';
        }

        return $list;

    }

}

if(! function_exists('ArrayToObject'))
{
    function ArrayToObject($d) {
        if (is_array($d)) {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return (object) array_map(__FUNCTION__, $d);
        }
        else {
            // Return object
            return $d;
        }
    }
}

if(! function_exists('invoice_format')){
    function invoice_format($package_id, $date_issue = '0812', $invoice_data = array())
    {
        $package_code = array(
            1 => 'ST',
            2 => 'DO',
            3 => 'SR',
            4 => 'SI',
            5 => 'EX',
            6 => 'VO'
        );

        $increment = str_pad(count($invoice_data),4,'0',STR_PAD_LEFT);

        return $package_code[$package_id] .'-'.$date_issue.'-'.$increment;

    }
}

if(! function_exists('file_rearrange')){
    function file_rearrange($file_post){
        $files = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for($i = 0; $i < $file_count; $i++){
            foreach($file_keys as $keys){
                $files[$i][$keys] = $file_post[$keys][$i];
            }
        }

        return $files;
    }
}

if(! function_exists('timezone_transition')){
    function timezone_transition($orig_date, $orig_tz, $target_tz, $format = 'd M Y h:i a'){

        $original_datetime = $orig_date;
        $original_timezone = new DateTimeZone($orig_tz);

        $datetime = new DateTime($original_datetime, $original_timezone);
        $target_timezone = new DateTimeZone($target_tz);

        $datetime->setTimeZone($target_timezone);

        $triggerOn = $datetime->format($format);

        return $triggerOn;
    }
}

if(! function_exists('customTimeZoneConverter')){
    function customTimeZoneConverter($time, $source_offset, $target_offset, $format = 'h:i a'){
        $offset_diff = $target_offset - $source_offset;
        $diff_time = (3600 * $offset_diff);
        $new_time = $time + $diff_time;

        $new_date = date($format, $new_time);
        return $new_date;
    }
}

// for generating random colors - used in full calendar
/*if(! function_exists('random_color_part')){
    function random_color_part() {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }
}
if(! function_exists('random_color')){
    function random_color() {
        return random_color_part() . random_color_part() . random_color_part();
    }
}
if(! function_exists('random_color_array')){
    function random_color_array($array) {
        $thisColor = random_color();

        if(!in_array($thisColor, $array)){
            return $thisColor;
        }
        else{
            return random_color_array($array);
        }
    }
}*/

if(! function_exists('get_color')){
    function get_color($name){
        $hash = md5($name);

        $color1 = hexdec(substr($hash, 8, 2));
        $color2 = hexdec(substr($hash, 4, 2));
        $color3 = hexdec(substr($hash, 0, 2));
        if($color1 < 128) $color1 += 128;
        if($color2 < 128) $color2 += 128;
        if($color3 < 128) $color3 += 128;

        return "#" . dechex($color1) . dechex($color2) . dechex($color3);
    }
}