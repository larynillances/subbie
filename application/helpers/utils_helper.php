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