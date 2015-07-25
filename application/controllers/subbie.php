<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property My_Model $my_model Optional description
 */
class Subbie extends CI_Controller{

    var $data;

    function __construct(){
        parent::__construct();
        date_default_timezone_set('Pacific/Auckland');
        $this->getUserInfo();
    }

    function index(){
        if($this->session->userdata('is_logged_in') === true){
            redirect('trackingLog');
        }

        $this->load->view('login_view',$this->data);
    }

    function validate(){
        if(isset($_POST['login'])){
            $fields = array('email','username');
            $user = $this->my_model->getInfo('tbl_user',$_POST['email'],$fields);
            $data = array(
                'is_logged_in' => false
            );
            if(count($user)>0){
                foreach($user as $v){
                    $password = $this->encrypt->decode($v->password);
                    if($_POST['password'] == $password){
                        $data = array(
                            'is_logged_in' => true,
                            'user_id' => $v->id,
                            'account_type' => $v->account_type
                        );
                    }else{
                        $this->session->set_flashdata(array(
                            'error_msg'=>'<div class="bg-danger" style="padding: 10px;color: #ff0000;margin-bottom: 10px;">
                            Error! Email/Password did not match.
                            </div>'
                        ));
                        redirect('');
                    }
                }
            }else{
                $this->session->set_flashdata(array(
                    'error_msg'=>'<div class="bg-danger" style="padding: 10px;color: #ff0000;margin-bottom: 10px;">
                    Error! Email/Password did not match.
                    </div>'
                ));
                redirect('');
            }

            if($data['is_logged_in']){
                $this->session->set_userdata($data);
                switch($data['account_type']){
                    case 1:
                        redirect('timeSheetEdit');
                        break;
                    case 2:
                        redirect('trackingLog');
                        break;
                    default:
                        redirect('trackingLog');
                        break;
                }
                //redirect('trackingLog');
            }
        }
    }

    function error404(){
        $this->data['page_load'] = 'error_404_view';
        $this->load->view('main_view',$this->data);
    }

    function logout(){
        $this->session->set_userdata(array('is_logged_in' => false));
        $this->session->sess_destroy();

        redirect('');
    }

    function getUserInfo($has_return = false){
        $page_link = $this->uri->segment(1);

        $this->my_model->setLastId('name');
        $page_name = $this->my_model->getInfo('tbl_page_title',$page_link,'link');

        $this->my_model->setLastId('invoice_info');
        $this->data['invoice_info'] = $this->my_model->getInfo('tbl_invoice_info');

        $this->my_model->setLastId('terms_trade');
        $this->data['terms_trade'] = $this->my_model->getInfo('tbl_invoice_info');

        //$this->displayarray($this->data['header_links']);exit;
        $this->data['page_name'] = $page_name != '' ? $page_name : 'Error 404';

        $this->my_model->setLastId('earnings');
        $this->data['earnings'] = $this->my_model->getInfo('tbl_tax',2,'wage_type_id');

        $this->my_model->setLastId('m_paye');
        $this->data['m_paye'] = $this->my_model->getInfo('tbl_tax',2,'wage_type_id');
        $this->data['info_array'] = $this->my_model->getInfo('tbl_invoice_info');

        $this->data['account_type'] = $this->session->userdata('account_type');

        $this->my_model->setShift();
        $this->data['user'] = (Object)$this->my_model->getInfo('tbl_user',$this->session->userdata('user_id'));


        $this->data['notification'] = $this->getInvoiceNotification();
        $this->data['count_msg'] = count($this->data['notification']);

        $this->data['notification_dp'] = array(
            '1' => 'New',
            '2' => 'Open'
        );

        $this->my_model->setShift();
        $this->data['user_data'] = (Object)$this->my_model->getInfo('tbl_user',3);

        if($has_return){
            return $this->data;
        }
    }

    function getSubPages($parent_id, $parent_per_ids){
        $sub_pages = array();

        if($parent_id && count($parent_per_ids)>0){
            $whatField = array('parent_id', 'id');
            $whatVal = array($parent_id, $parent_per_ids);
            $pages = $this->my_model->getinfo('tbl_pages', $whatVal, $whatField);

            if(count($pages)>0){
                $this_link = "";
                foreach($pages as $k=>$v){
                    $this->my_model->setLastId("link");
                    $this_link = $this->my_model->getinfo('tbl_page_title', $v->page_title_id);

                    $whatToSearch = $v->is_parent ? $v->page : $this_link;
                    if($v->page_title_wildcard){
                        $whatToSearch .= "/" . $v->page_title_wildcard;
                    }

                    if(!$this->isSubLinkExist($whatToSearch, $this->data['header_links'])){
                        if($v->is_parent){
                            $sub_pages[$v->page] = $this->getSubPages($v->id, $parent_per_ids);
                        }else{
                            if($this_link){
                                $sub_pages[$whatToSearch] = $v->page;
                            }else{
                                $sub_pages[] = $v->page;
                            }
                        }
                    }
                }
            }
        }

        return $sub_pages;
    }

    function isSubLinkExist($whatLink, $this_array){
        $itExist = false;

        if($whatLink){
            if(count($this_array)>0){
                foreach($this_array as $k=>$v){
                    if(is_array($v)){
                        $itExist = $this->isSubLinkExist($whatLink, $v);
                    }

                    $itExist = $itExist ? $itExist : $k == $whatLink;
                    if($itExist){
                        break;
                    }
                }
            }
        }

        return $itExist;
    }

    function getWorkingHours($date,$id,$working = 1,$action = 'weekly',$start_date = 2){
        $totalHours = array();
        $hours_gain = array();
        $year = date('Y',strtotime($date));
        $month = date('m',strtotime($date));
        $d = date('d',strtotime($date));
        $date = mktime(0, 0, 0, $month,$d,$year);
        $week = (int)date('W', $date);
        $week_number = $week;
        $dt = new DateTime;
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $this->my_model->setSelectFields(array(
            'TIMESTAMPDIFF(SECOND, time_in, time_out) as hours',
            'time_in','time_out','staff_id','date',
            'id as dtr_id','working_type_id'
        ));

        $dtr = $this->my_model->getinfo('tbl_login_sheet', $id,'staff_id');
        if(count($dtr) >0){
            foreach($dtr as $dv){
                $hours_gain[$dv->staff_id][$dv->working_type_id][$dv->date] = $dv->hours;
            }
        }

        switch($action){
            case 'weekly':
                for($whatDay=$start_date; $whatDay<=8; $whatDay++){
                    $getDate =  $dt->setISODate($year, $week_number , $whatDay)->format('Y-m-d');
                    $day = date('Y-m-d', strtotime($getDate));

                    $thisDtr = array_key_exists($id, $hours_gain) ? @$hours_gain[$id][$working] : array();
                    if(count($thisDtr) > 0){
                        $hasInfo = array_key_exists($day, $thisDtr);

                        if($hasInfo){
                            $thisTime = $thisDtr[$day];
                            @$totalHours[$id] += @$thisTime;
                        }
                    }
                }
                break;
            default:
                for($whatDay=1; $whatDay<=$num; $whatDay++){
                    $whatDate = $year.'-'.$month.'-'.$whatDay;
                    $thisDate = date('Y-m-d',strtotime($whatDate));
                    $thisDtr = array_key_exists($id, $hours_gain) ? $hours_gain[$id][$working] : array();
                    if(count($thisDtr) > 0){
                        $hasInfo = array_key_exists($thisDate, $thisDtr);
                        if($hasInfo){
                            $thisTime = $thisDtr[$thisDate];
                            @$totalHours[$id] += @$thisTime;
                        }
                    }
                }
                break;
        }


        //$minutes = (int)(@$totalHours[$id]/60);
        $hoursValue = number_format((@$totalHours[$id]/3600),2);
        //$minutesValue = $minutes - ($hoursValue * 60);
        //$secondsValue = $v->hours - (($hoursValue * 3600) + ($minutesValue * 60));
        //$hours = str_pad($hoursValue, 2, '0', STR_PAD_LEFT) . "." . str_pad($minutesValue, 2, '0', STR_PAD_LEFT);
        return $hoursValue;
    }

    function getTotalHours($date,$id,$action = 'weekly',$start_date = 2){
        $totalHours = array();
        $hours_gain = array();
        $year = date('Y',strtotime($date));
        $month = date('m',strtotime($date));
        $d = date('d',strtotime($date));
        $date = mktime(0, 0, 0, $month,$d,$year);
        $week = (int)date('W', $date);
        $week_number = $week;
        $dt = new DateTime;
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $this->my_model->setSelectFields(array(
            'TIMESTAMPDIFF(SECOND, time_in, time_out) as hours',
            'time_in','time_out','staff_id','date',
            'id as dtr_id','working_type_id'
        ));

        $dtr = $this->my_model->getinfo('tbl_login_sheet', $id,'staff_id');
        if(count($dtr) >0){
            foreach($dtr as $dv){
                $hours_gain[$dv->staff_id][$dv->date] = $dv->hours;
            }
        }

        switch($action){
            case 'weekly':
                for($whatDay=$start_date; $whatDay<=8; $whatDay++){
                    $getDate =  $dt->setISODate($year, $week_number , $whatDay)->format('Y-m-d');
                    $day = date('Y-m-d', strtotime($getDate));

                    $thisDtr = array_key_exists($id, $hours_gain) ? $hours_gain[$id] : array();
                    if(count($thisDtr) > 0){
                        $hasInfo = array_key_exists($day, $thisDtr);

                        if($hasInfo){
                            $thisTime = $thisDtr[$day];
                            @$totalHours[$id] += @$thisTime;
                        }
                    }
                }
                break;
            default:
                for($whatDay=1; $whatDay<=$num; $whatDay++){
                    //$whatDate = $year.'-'.$month.'-'.$whatDay;
                    $date = mktime(0, 0, 0, $month,$whatDay,$year);
                    $thisDate = date('Y-m-d',$date);
                    $thisDtr = array_key_exists($id, $hours_gain) ? $hours_gain[$id] : array();
                    if(count($thisDtr) > 0){
                        $hasInfo = array_key_exists($thisDate, $thisDtr);
                        if($hasInfo){
                            $thisTime = $thisDtr[$thisDate];
                            @$totalHours[$id] += @$thisTime;
                        }
                    }
                }
                break;
        }

        //$minutes = (int)(@$totalHours[$id]/60);
        $hoursValue = number_format((@$totalHours[$id]/3600),2);
        //$minutesValue = $minutes - ($hoursValue * 60);
        //$secondsValue = $v->hours - (($hoursValue * 3600) + ($minutesValue * 60));
        //$hours = str_pad($hoursValue, 2, '0', STR_PAD_LEFT) . "." . str_pad($minutesValue, 2, '0', STR_PAD_LEFT);
        return $hoursValue;
    }

    function getWeekInYear($end_year,$start = 2){

        $endYearWeeksPeriod = new DatePeriod(
            new DateTime("$end_year-W01-$start"),
            new DateInterval('P1W'),
            new DateTime("$end_year-12-31T23:59:59Z")
        );

        $year_week = array();
        $ref = 1;
        foreach ($endYearWeeksPeriod as $week => $tuesday) {
            $year_week[$ref] = $tuesday->format('Y-m-d');
            $ref++;
        }

        return $year_week;
    }

    function getWeekBetweenDates($start,$end,$day_start = 'tuesday'){
        $start = date('Y-m-d',strtotime('-1 day '.$start));
        $start = date('Y-m-d',strtotime('next '.$day_start.' '.$start));

        $end = date('Y-m-d',strtotime('+1 day '.$end));
        $end = date('Y-m-d',strtotime('last '.$day_start.' '.$end));

        $endYearWeeksPeriod = new DatePeriod(
            new DateTime("$start"),
            new DateInterval('P1W'),
            new DateTime($end."T23:59:59Z")
        );

        $year_week = array();
        $ref = 1;
        foreach ($endYearWeeksPeriod as $week => $day) {
            $year_week[$ref] = $day->format('Y-m-d');
            $ref++;
        }

        return $year_week;
    }

    function getWeekInYearBetweenDates($end_year,$start_year = 2014,$start = 2){
        $endYearWeeksPeriod = new DatePeriod(
            new DateTime("$start_year-W01-$start"),
            new DateInterval('P1W'),
            new DateTime("$end_year-12-31T23:59:59Z")
        );

        $year_week = array();
        $ref = 1;
        foreach ($endYearWeeksPeriod as $week => $tuesday) {
            $year_week[$ref] = $tuesday->format('Y-m-d');
            $ref++;
        }

        return $year_week;
    }

    function getYearNumWeek($year,$start = 2){

        $weeksPeriod = new DatePeriod(
            new DateTime("$year-W01-$start"),
            new DateInterval('P1W'),
            new DateTime("$year-12-31T23:59:59Z")
        );
        $year_week = array();
        foreach ($weeksPeriod as $week => $monday) {
            $year_week[$monday->format('Y-m-d')] = $monday->format('W');
        }
        //$this->displayarray($this->data['year_week']);
        return $year_week;
    }

    function getFirstNextLastDay($y, $m, $day = 'tuesday')
    {
        $begin = new DateTime("first $day of $y-$m");
        $end = new DateTime("last $day of $y-$m");
        $end = $end->modify( '+1 day' );

        $interval = DateInterval::createFromDateString('next '.$day);
        $daterange = new DatePeriod($begin, $interval ,$end);

        $date = array();

        if(count($daterange) > 0){
            foreach($daterange as $dv){
                $this_date = $dv->format('Y-m-d');
                //$date[] = date('Y-m-d',strtotime('+6 days '.$this_date));
                $date[] = $this_date;
            }
        }

        return $date;
    }

    function getPaymentStartDate($year,$month = 'April',$day = 'tuesday',$start = 2){

        $month_day = $this->first_day_of_month($year,$month,$day);

        $date = new DateTime($month_day);
        $week = $date->format("W");

        $end_year = $year + 1;
        $end_month_day = $this->first_day_of_month($end_year,$month,$day);
        $this_month = date('m',strtotime($end_month_day));
        $this_day = date('d',strtotime('-2 days '.$end_month_day));

        $weeksPeriod = new DatePeriod(
            new DateTime("$year-W$week-$start"),
            new DateInterval('P1W'),
            new DateTime("$end_year-$this_month-$this_day T23:59:59Z")
        );
        $year_week = array();
        foreach ($weeksPeriod as $week => $monday) {
            $year_week[$monday->format('Y-m-d')] = $monday->format('W');
        }

        return $year_week;
    }

    function first_day_of_month($year,$month = 'April',$day="Tuesday"){
        $day = new DateTime(sprintf("First $day of $month %s", $year));
        return $day->format('Y-m-d');
    }

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
        for($whatDay=$std; $whatDay<=8; $whatDay++){
            $getDate =  $dt->setISODate($y, $week , $whatDay)->format('Y-m-d');
            $this->data['days_of_week'][$ref] = $getDate;
            $ref++;
        }

        return $this->data['days_of_week'];
    }

    function getNumberOfWeeks($year, $month){
        $days = $this->getFirstNextLastDay($year,$month);
        $this->data['days'] = array();
        $thisDays = array();
        if(count($days) > 0){
            foreach($days as $v){
                $thisDays[$v] = $this->getWeeks($v,date('l',strtotime($v)));
            }
        }
        $this->data['days'] = $thisDays;
        return $this->data['days'];
    }

    function get_date($month, $year, $week, $day, $direction) {
        if($direction > 0)
            $startday = 1;
        else
            $startday = date('t', mktime(0, 0, 0, $month, 1, $year));

        $start = mktime(0, 0, 0, $month, $startday, $year);
        $weekday = date('N', $start);

        if($direction * $day >= $direction * $weekday)
            $offset = -$direction * 7;
        else
            $offset = 0;

        $offset += $direction * ($week * 7) + ($day - $weekday);
        return mktime(0, 0, 0, $month, $startday + $offset, $year);
    }

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

    function getWeekNumberOfDateInYear($date){
        $date = date('Y-m-d',strtotime($date));
        $_date = new DateTime($date);
        $week = $_date->format("W");

        return $week;
    }

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

    function getMonth(){
        $month = array();

        for ($m=1; $m<=12; $m++) {
            $date = '2014-'.$m.'-01';
            $month_str = str_pad($m,2,'0',STR_PAD_LEFT);
            $month[$month_str] = date('F', strtotime($date));
        }
        return $month;
    }

    function getOverAllWageTotalPay($date,$id = ''){

        $this_year = date('Y',strtotime($date));
        $first_tuesday_in_month = $this->first_day_of_month($this_year);
        $year = strtotime($first_tuesday_in_month) > strtotime($date) ? date('Y',strtotime('-1 year'.$date)) : date('Y',strtotime($date));

        $earnings = floatval($this->data['earnings']);
        $m_paye = floatval($this->data['m_paye']);

        $this->getYearTotalBalance($this_year);

        $this->my_model->setJoin(array(
            'table' => array('tbl_rate','tbl_currency','tbl_deductions','tbl_wage_type','tbl_kiwi'),
            'join_field' => array('id','id','staff_id','id','id'),
            'source_field' => array('tbl_staff.rate','tbl_staff.currency','tbl_staff.id','tbl_staff.wage_type','tbl_staff.kiwi_id'),
            'type' => 'left'
        ));
        $deductions = $this->arrayWalk(array(
            'flight_deduct','flight_debt',
            'visa_deduct','visa_debt',
            'accommodation','transport'
        ),'tbl_deductions.');
        $staff = $this->arrayWalk(array(
            'tax_number','installment','balance',
            'nz_account','account_two'
        ),'tbl_staff.');

        $fields = array_merge($deductions,$staff);
        $fields[] = 'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name';
        $fields[] = 'tbl_rate.rate_cost';
        $fields[] = 'tbl_staff.id as employee';
        $fields[] = 'tbl_currency.symbols';
        $fields[] = 'tbl_currency.currency_code';
        $fields[] = 'tbl_wage_type.type as wage_type';
        $fields[] = 'tbl_kiwi.kiwi';

        $whatVal = false;
        $whatFld = 'tbl_staff.is_unemployed';

        if($id){
            $whatVal = $id;
            $whatFld = 'tbl_staff.id';
        }

        $this->my_model->setSelectFields($fields);
        $staff_list = $this->my_model->getinfo('tbl_staff',$whatVal,$whatFld);

        $set_wage_date = $this->getPaymentStartDate($year);
        $wage_total_data = array();

        if(count($set_wage_date) > 0){
            foreach($set_wage_date as $key=>$dv){
                if(count($staff_list) > 0){
                    foreach($staff_list as $ev){
                        $rate = $this->getStaffRate($ev->employee,$key);
                        $ev->start_use = '';
                        if(count($rate) > 0){
                            foreach($rate as $val){
                                $ev->rate_name = $val->rate_name;
                                $ev->rate_cost = $val->rate;
                                $ev->start_use = $val->start_use;
                            }
                        }


                        $ev->tax = 0;
                        $hours = $ev->wage_type != 1 ? $this->getTotalHours($key,$ev->employee) : 1;
                        $ev->gross = $ev->rate_cost * $hours;
                        $ev->gross_ = $ev->gross != 0 ? floatval(number_format($ev->gross,2,'.','')):'0.00';
                        $ev->gross = $ev->gross != 0 ? floatval(number_format($ev->gross,0,'.','')):'0.00';
                        $ev->kiwi_ = 0;
                        $kiwi = $ev->kiwi ? 'kiwi_saver_'.$ev->kiwi : '';

                        if($ev->wage_type == 1){
                            $whatVal = 'earnings ="'.$ev->gross.'" AND start_date <= "'.$key.'" AND wage_type_id = "'.$ev->wage_type.'"';
                            $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                            if(count($tax)>0){
                                foreach($tax as $tv){
                                    $ev->tax = $tv->m_paye;
                                    $ev->kiwi_ = $kiwi ? $tv->$kiwi : 0;
                                }
                            }
                        }else{
                            if($ev->gross > $earnings){
                                $ev->tax = (($ev->gross - $earnings) * 0.33) + $m_paye;
                            }else{
                                $whatVal = 'earnings ="'.$ev->gross.'" AND start_date <= "'.$key.'" AND wage_type_id = "'.$ev->wage_type.'"';
                                $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                                if(count($tax)>0){
                                    foreach($tax as $tv){

                                        $ev->tax = $tv->m_paye;
                                        $ev->kiwi_ = $kiwi ? $tv->$kiwi : 0;
                                    }
                                }
                            }
                        }

                        $ev->flight_debt = @$this->data['total_bal'][$key][$ev->employee]['flight_debt'];
                        $ev->flight_deduct = $ev->flight_debt > 0 ?
                            ($ev->flight_debt <= $ev->flight_deduct ? $ev->flight_debt : $ev->flight_deduct) : 0;

                        $ev->visa_debt = @$this->data['total_bal'][$key][$ev->employee]['visa_debt'];
                        $ev->visa_deduct = $ev->visa_debt > 0 ?
                            ($ev->visa_debt <= $ev->visa_deduct ? $ev->visa_debt : $ev->visa_deduct) : 0;

                        $ev->balance = @$this->data['total_bal'][$key][$ev->employee]['balance'];
                        $ev->installment = $ev->balance > 0 ?
                            ($ev->balance <= $ev->installment ? $ev->balance : $ev->installment) : 0;

                        $ev->recruit = $ev->visa_deduct ? $ev->gross * 0.03 : 0;
                        $ev->admin = $ev->visa_deduct ? $ev->gross * 0.01 : 0;
                        $ev->nett = $ev->gross - ($ev->kiwi_ + $ev->tax + $ev->flight_deduct + $ev->visa_deduct + $ev->accommodation + $ev->transport + $ev->recruit + $ev->admin);

                        $ev->distribution = $ev->nett - $ev->installment;
                        $ev->account_one = $ev->distribution - ($ev->nz_account + $ev->account_two);

                        if($ev->gross > 0){
                            @$ev->total_distribution +=  floatval($ev->distribution);
                            @$ev->total_account_one +=  $ev->account_one > 0 ? floatval($ev->account_one) : 0;
                            @$ev->total_account_two +=  floatval($ev->account_two);
                            @$ev->total_nz_account +=  floatval($ev->nz_account);

                            $wage_total_data[$ev->employee][$key] = array(
                                'distribution' => $ev->total_distribution,
                                'account_one' => $ev->total_account_one,
                                'account_two' => $ev->total_account_two,
                                'nz_account' => $ev->total_nz_account
                            );
                        }
                    }
                }
            }
        }

        return $wage_total_data;
    }

    function getOrderData($id,$order_id = ''){
        $this->my_model->setLastId('id');
        $order_num = (int)$this->my_model->getInfo('tbl_order_send');

        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_registration',
                'tbl_supplier',
                'tbl_product_list',
                'tbl_client'
            ),
            'join_field' => array('id','id','id','id'),
            'source_field' => array(
                'tbl_order_book.job_id',
                'tbl_order_book.supplier_id',
                'tbl_order_book.product_id',
                'tbl_registration.client_id'
            ),
            'type' => 'left'
        ));

        $fields = $this->arrayWalk(array('product_name','price'),'tbl_product_list.');
        $fields[] = 'tbl_registration.address';
        $fields[] = 'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref';
        $fields[] = 'tbl_supplier.supplier_name';
        $fields[] = 'tbl_order_book.job_id';
        $fields[] = 'tbl_supplier.address';
        $fields[] = 'tbl_order_book.quantity';
        $fields[] = 'tbl_order_book.id';
        $fields[] = 'tbl_order_book.product_id';
        $fields[] = 'tbl_order_book.supplier_id';

        $this->my_model->setSelectFields($fields);

        $whatVal = array($id,true);
        $whatFld = array('tbl_order_book.supplier_id','tbl_order_book.is_order !=');
        $config = $this->my_model->model_config;

        $order_num = $order_num != 0 ? $order_num + 1 : 1;
        $this->data['order_num'] = 'SO'.str_pad($order_num,5,'0',STR_PAD_LEFT);
        if($order_id){
            $whatVal = array($id,$order_id);
            $whatFld = array('tbl_order_book.supplier_id','tbl_order_book.order_ref');
            $this->data['order_num'] = $order_id;
        }
        $this->my_model->model_config = $config;
        $this->data['order_list'] = $this->my_model->getInfo('tbl_order_book',$whatVal,$whatFld);

        $this->data['supplier'] = $this->my_model->getInfo('tbl_supplier',$id);
        $this->data['job_num'] = '';
    }

    function getWageData($year,$month,$type = 'weekly'){
        //automate wage
        $earnings = $this->data['earnings'];
        $m_paye = $this->data['m_paye'];

        $this->getYearTotalBalance($year);
        $this->my_model->setJoin(array(
            'table' => array('tbl_rate','tbl_currency','tbl_deductions','tbl_wage_type','tbl_kiwi'),
            'join_field' => array('id','id','staff_id','id','id'),
            'source_field' => array('tbl_staff.rate','tbl_staff.currency','tbl_staff.id','tbl_staff.wage_type','tbl_staff.kiwi_id'),
            'type' => 'left'
        ));
        $deductions = $this->arrayWalk(array(
            'flight_deduct','flight_debt',
            'visa_deduct','visa_debt',
            'accommodation','transport'
        ),'tbl_deductions.');
        $staff = $this->arrayWalk(array(
            'tax_number','installment','balance',
            'nz_account','nz_account as nz_account_','account_two'
        ),'tbl_staff.');

        $fields = array_merge($deductions,$staff);
        $fields[] = 'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name';
        $fields[] = 'tbl_rate.rate_cost';
        $fields[] = 'tbl_staff.id as employee';
        $fields[] = 'tbl_currency.symbols';
        $fields[] = 'tbl_currency.currency_code';
        $fields[] = 'tbl_wage_type.type as wage_type';
        $fields[] = 'tbl_kiwi.kiwi';

        $this->my_model->setSelectFields($fields);
        $staff_list = $this->my_model->getinfo('tbl_staff',true,'tbl_staff.is_unemployed !=');
        switch($type){
            case 'weekly':
                $this->data['balance'] = array();
                $this->data['wage_data'] = array();

                $this->data['date'] = $this->getFirstNextLastDay($year,$month,'tuesday');
                $this->data['wage_total_data'] = array();
                $phpCurrency = CurrencyConverter('PHP');
                if(count($this->data['date']) > 0){
                    foreach($this->data['date'] as $dv){
                        if(count($staff_list) > 0){
                            foreach($staff_list as $ev){
                                $rate = $this->getStaffRate($ev->employee,$dv);
                                $ev->start_use = '';
                                if(count($rate) > 0){
                                    foreach($rate as $val){
                                        $ev->rate_name = $val->rate_name;
                                        $ev->rate_cost = $val->rate;
                                        $ev->start_use = $val->start_use;
                                    }
                                }

                                $hourly_rate = $this->getStaffHourlyRate($ev->employee);
                                $ev->hourly_rate = 0;
                                if(count($hourly_rate) > 0){
                                    foreach($hourly_rate as $val){
                                        $ev->hourly_rate = $val->hourly_rate;
                                    }
                                }

                                $this->data['balance'][$ev->employee] = array(
                                    'balance' => $ev->balance,
                                    'flight_debt' => $ev->flight_debt,
                                    'visa_debt' => $ev->visa_debt
                                );
                                $code = $ev->currency_code != 'NZD' ? $ev->currency_code : 'PHP';
                                $symbols = $ev->currency_code != 'NZD' ? $ev->symbols : '₱';

                                $converted_amount = $ev->currency_code != 'NZD' ? 1 : $phpCurrency;

                                $ev->tax = 0;
                                $hours = $ev->wage_type != 1 ? $this->getTotalHours($dv,$ev->employee) : 1;
                                $ev->gross = $ev->rate_cost * $hours;
                                $ev->gross_ = number_format($ev->gross,2,'.','');
                                $ev->gross = $ev->gross != 0 ? number_format($ev->gross,0,'.',''):'0.00';
                                $ev->kiwi_ = 0;
                                $kiwi = $ev->kiwi ? 'kiwi_saver_'.$ev->kiwi : '';

                                $ev->nz_account = $ev->nz_account_ ? $ev->nz_account_ + ($hours * $ev->hourly_rate) : 0;

                                if($ev->wage_type == 1){
                                    $whatVal = 'earnings ="'.$ev->gross.'" AND start_date <= "'.$dv.'" AND wage_type_id = "'.$ev->wage_type.'"';
                                    $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                                    if(count($tax)>0){
                                        foreach($tax as $tv){
                                            $ev->tax = $tv->m_paye;
                                            $ev->kiwi_ = $kiwi ? $tv->$kiwi : 0;
                                        }
                                    }
                                }else{
                                    if($ev->gross > $earnings){
                                        $ev->tax = ((floatval($ev->gross) - $earnings) * 0.33) + $m_paye;
                                    }else{
                                        $whatVal = 'earnings ="'.$ev->gross.'" AND start_date <= "'.$dv.'" AND wage_type_id = "'.$ev->wage_type.'"';
                                        $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                                        if(count($tax)>0){
                                            foreach($tax as $tv){

                                                $ev->tax = $tv->m_paye;
                                                $ev->kiwi_ = $kiwi ? $tv->$kiwi : 0;
                                            }
                                        }
                                    }
                                }

                                $ev->flight_debt = @$this->data['total_bal'][$dv][$ev->employee]['flight_debt'];
                                $ev->flight_deduct = $ev->flight_debt > 0 ?
                                    ($ev->flight_debt <= $ev->flight_deduct ? $ev->flight_debt : $ev->flight_deduct) : 0;

                                $ev->visa_debt = @$this->data['total_bal'][$dv][$ev->employee]['visa_debt'];
                                $ev->visa_deduct = $ev->visa_debt > 0 ?
                                    ($ev->visa_debt <= $ev->visa_deduct ? $ev->visa_debt : $ev->visa_deduct) : 0;

                                $ev->balance = @$this->data['total_bal'][$dv][$ev->employee]['balance'];
                                $ev->installment = $ev->balance > 0 ?
                                    ($ev->balance <= $ev->installment ? $ev->balance : $ev->installment) : 0;

                                $ev->recruit = $ev->visa_deduct ? $ev->gross * 0.03 : 0;
                                $ev->admin = $ev->visa_deduct ? $ev->gross * 0.01 : 0;

                                $ev->nett = $ev->gross_ - ($ev->kiwi_ + $ev->tax + $ev->flight_deduct + $ev->visa_deduct + $ev->accommodation + $ev->transport + $ev->recruit + $ev->admin);

                                $ev->distribution = $ev->nett - $ev->installment;
                                $ev->account_one = $ev->distribution - ($ev->nz_account + $ev->account_two);

                                $this->data['wage_data'][$dv][] = array(
                                    'id' => $ev->employee,
                                    'name' => $ev->name,
                                    'tax_number' => $ev->tax_number,
                                    'gross' => '$'.$ev->gross_,
                                    'rate_cost' => $ev->rate_cost,
                                    'hours' => $ev->wage_type != 1 ? $hours : 40,
                                    'tax' => $ev->tax != 0 ? '$'.number_format($ev->tax,2) : '',
                                    'flight' => $ev->flight_deduct,
                                    'visa' => $ev->visa_deduct,
                                    'accommodation' => $ev->accommodation != '' ? '$'.$ev->accommodation: '',
                                    'transport' => $ev->transport != '' ? '$'.$ev->transport : '',
                                    'deduction' => $ev->installment != '' || $ev->installment != 0 ? '$'.$ev->installment : '',
                                    'distribution' => $ev->distribution,
                                    'date' => $dv,
                                    'nett' => '$'.number_format($ev->nett,2,'.',''),
                                    'recruit' => $ev->visa_deduct ? '$'.number_format($ev->recruit,2,'.','') : '',
                                    'admin' => $ev->visa_deduct ? '$'.number_format($ev->admin,2,'.','') : '',
                                    'currency' => $code,
                                    'account_two' => $ev->account_two,
                                    'account_one' => $ev->flight_deduct ? $ev->account_one : 0,
                                    'nz_account' => $ev->nz_account,
                                    'rate_value' => $converted_amount,
                                    'week' => $this->getWeeks($dv),
                                    'start_use' => $ev->start_use,
                                    'wage_type' => $ev->wage_type,
                                    'symbols' => $symbols,
                                    'installment' => $ev->installment,
                                    'kiwi' => $ev->kiwi_
                                );
                                $this->data['last_week'] = $this->getWeeks(date('Y-m-t',strtotime('-1 week '.$dv)));
                                $this->data['start_week'] = $this->getWeekNumberOfDateInYear($ev->start_use);
                            }
                        }
                    }
                }
                break;
            case 'monthly':
                $this->data['monthly_pay'] = array();
                $date = $this->getFirstNextLastDay($year,$month,'tuesday');
                $this->data['staff'] = $this->my_model->getinfo('tbl_staff',true,'tbl_staff.is_unemployed !=');

                $phpCurrency = CurrencyConverter('PHP');
                if(count($staff_list)>0){
                    foreach($staff_list as $mv){
                        if(count($date) >0){
                            foreach($date as $dv){
                                $monthly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->staff_id,'monthly') : 1;
                                $weekly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->staff_id) : 1;

                                if($monthly_hours != 0){
                                    $rate = $this->getStaffRate($mv->staff_id,$dv);
                                    $mv->start_use = '';
                                    if(count($rate) > 0){
                                        foreach($rate as $val){
                                            $mv->rate_name = $val->rate_name;
                                            $mv->rate_cost = $val->rate;
                                            $mv->start_use = $val->start_use;
                                        }
                                    }

                                    $hourly_rate = $this->getStaffHourlyRate($mv->staff_id);
                                    $mv->hourly_rate = 0;
                                    if(count($hourly_rate) > 0){
                                        foreach($hourly_rate as $val){
                                            $mv->hourly_rate = $val->hourly_rate;
                                        }
                                    }

                                    $mv->hours = $monthly_hours;
                                    $mv->gross_ = number_format($mv->hours * $mv->rate_cost,2,'.','');
                                    $mv->gross = number_format($mv->hours * $mv->rate_cost,0,'.','');
                                    $mv->weekly_gross = number_format($weekly_hours * $mv->rate_cost,0,'.','');

                                    $mv->nz_account = $mv->nz_account ? ($mv->nz_account + ($mv->hours * $mv->hourly_rate)) : 0;

                                    $mv->flight_debt = @$this->data['total_bal'][$dv][$mv->staff_id]['flight_debt'];
                                    $mv->flight = $mv->flight_debt > 0 ?
                                        ($mv->flight_debt <= $mv->flight ? $mv->flight_debt : $mv->flight) : 0;

                                    $mv->visa_debt = @$this->data['total_bal'][$dv][$mv->staff_id]['visa_debt'];
                                    $mv->visa = $mv->visa_debt > 0 ?
                                        ($mv->visa_debt <= $mv->visa ? $mv->visa_debt : $mv->visa) : 0;

                                    $mv->balance = @$this->data['total_bal'][$dv][$mv->staff_id]['balance'];
                                    $mv->installment = $mv->balance > 0 ?
                                        ($mv->balance <= $mv->installment ? $mv->balance : $mv->installment) : 0;

                                    $mv->recruit = $mv->visa ? $mv->gross * 0.03 : 0;
                                    $mv->admin = $mv->visa ? $mv->gross * 0.01 : 0;

                                    $code = $mv->currency_code != 'NZD' ? $mv->currency_code : 'PHP';
                                    $symbols = $mv->currency_code != 'NZD' ? $mv->symbols : '₱';

                                    $converted_amount = $mv->currency_code != 'NZD' ? 1 : $phpCurrency;
                                    if(!$mv->gross){
                                        $mv->tax = 0;
                                        $mv->kiwi_ = 0;
                                    }else{
                                        $mv->tax = 0;
                                        $mv->m_paye = 0;
                                        $mv->me_paye = 0;
                                        $mv->kiwi_ = 0;
                                        $kiwi = $mv->kiwi ? 'kiwi_saver_'.$mv->kiwi : '';
                                        if($mv->wage_type == 1){
                                            $whatVal = 'earnings ="'.$mv->gross.'" AND start_date <= "'.$dv.'" AND wage_type_id = "'.$mv->wage_type.'"';
                                            $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                                            if(count($tax)>0){
                                                foreach($tax as $tv){
                                                    $mv->tax = $tv->m_paye;
                                                    $mv->m_paye = $tv->m_paye;
                                                    $mv->me_paye = $tv->me_paye;
                                                    $mv->kiwi_ = $kiwi ? $tv->$kiwi : 0;
                                                }
                                            }
                                        }else{
                                            if($mv->gross > $earnings){
                                                $mv->tax = ((floatval($mv->gross) - $earnings) * 0.33) + $m_paye;
                                            }else{
                                                $whatVal = 'earnings ="'.$mv->gross.'" AND start_date <= "'.$dv.'" AND wage_type_id = "'.$mv->wage_type.'"';
                                                $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                                                if(count($tax)>0){
                                                    foreach($tax as $tv){
                                                        $mv->tax = $tv->m_paye;
                                                        $mv->m_paye = $tv->m_paye;
                                                        $mv->me_paye = $tv->me_paye;
                                                        $mv->kiwi_ = $kiwi ? $tv->$kiwi : 0;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    @$mv->tax_total += $mv->tax;
                                    @$mv->total_install += $mv->installment;
                                    @$mv->total_accom += $mv->accommodation;
                                    @$mv->total_trans += $mv->transport;
                                    @$mv->total_visa += $mv->visa;
                                    @$mv->total_flight += $mv->flight;
                                    @$mv->total_account_two += $mv->account_two;
                                    @$mv->total_nz_account += $mv->nz_account;
                                    @$mv->total_kiwi += $mv->kiwi_;

                                    $this->data['monthly_pay'][$mv->staff_id] = array(
                                        'staff_id' => $mv->staff_id,
                                        'symbols' => $symbols,
                                        'flight' => $mv->total_flight,
                                        'rate_cost' => $mv->rate_cost,
                                        'visa' => $mv->total_visa,
                                        'accommodation' => $mv->total_accom,
                                        'transport' => $mv->total_trans,
                                        'balance' => $mv->balance,
                                        'installment' => $mv->total_install,
                                        'currency_code' => $mv->currency_code,
                                        'account_two' => $mv->total_account_two,
                                        'nz_account' => $mv->total_nz_account,
                                        'tax' => $mv->tax,
                                        'hours' => $mv->wage_type != 1 ? $mv->hours : 40,
                                        'gross' => $mv->gross_,
                                        'recruit' => $mv->recruit,
                                        'admin' => $mv->admin,
                                        'total_tax' => $mv->tax_total,
                                        'weekly_gross' => $mv->weekly_gross,
                                        'm_paye' => $mv->m_paye,
                                        'me_paye' => $mv->me_paye,
                                        'kiwi' => $mv->total_kiwi,
                                        'rate_value' => $converted_amount
                                    );
                                }
                            }
                        }
                    }
                }
                break;
            default:
                break;
        }
    }

    function getPaySlipData($id,$date){
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_rate',
                'tbl_currency',
                'tbl_deductions',
                'tbl_wage_type',
                'tbl_kiwi'
            ),
            'join_field' => array(
                'id',
                'id',
                'staff_id',
                'id',
                'id'
            ),
            'source_field' => array(
                'tbl_staff.rate',
                'tbl_staff.currency',
                'tbl_staff.id',
                'tbl_staff.wage_type',
                'tbl_staff.kiwi_id'
            ),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array(
            'tbl_staff.id',
            'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name',
            'tbl_staff.tax_number','tbl_staff.company',
            'tbl_currency.symbols',
            'tbl_deductions.flight_deduct as flight',
            'tbl_rate.rate_cost',
            'tbl_deductions.flight_debt',
            'tbl_deductions.visa_debt',
            'tbl_deductions.visa_deduct as visa','tbl_deductions.accommodation',
            'tbl_deductions.transport',
            'tbl_staff.balance','tbl_staff.installment',
            'tbl_currency.currency_code','tbl_staff.account_two',
            'tbl_staff.nz_account',
            'tbl_staff.position',
            'tbl_wage_type.type as wage_type',
            'tbl_kiwi.kiwi',
            'tbl_staff.email',
            'IF(tbl_currency.symbols = "₱","Php",tbl_currency.symbols) as symbols'
        ),false);

        $data['staff'] = $this->my_model->getinfo('tbl_staff',$id,'tbl_staff.id');

        $earnings = floatval($this->data['earnings']);
        $m_paye = floatval($this->data['m_paye']);

        $this->getYearTotalBalance(date('Y',strtotime($date)));
        $data['staff_name'] = '';
        $data['has_email'] = 0;
        $phpCurrency = CurrencyConverter('PHP');
        if(count($data['staff'])>0){
            foreach($data['staff'] as $v){
                $data['staff_name'] = $v->name;
                $data['has_email'] = $v->email ? 1 : 0;
                $v->hours = $v->wage_type != 1 ? $this->getTotalHours($date,$v->id) : 1;
                $v->working_hours = $v->wage_type != 1 ? $this->getWorkingHours($date,$v->id) : 1;
                $v->non_working_hours = $v->wage_type != 1 ? $this->getWorkingHours($date,$v->id,2) : 1;
                $v->code = $v->currency_code != 'NZD' ? $v->currency_code : 'PHP';
                $v->currency_symbols = $v->symbols;
                $v->symbols = $v->currency_code != 'NZD' ? $v->symbols : 'Php';

                $rate = $this->getStaffRate($v->id,$date);
                if(count($rate) > 0){
                    foreach($rate as $val){
                        $v->rate_name = $val->rate_name;
                        $v->rate_cost = $val->rate;
                    }
                }

                $hourly_rate = $this->getStaffHourlyRate($v->id);
                $v->hourly_rate = 0;
                if(count($hourly_rate) > 0){
                    foreach($hourly_rate as $val){
                        $v->hourly_rate = $val->hourly_rate;
                    }
                }

                $converted_amount = $v->currency_code != 'NZD' ? 1 : $phpCurrency;
                $v->converted_amount = $converted_amount;
                $v->nz_account = $v->nz_account ? ($v->nz_account + ($v->hours * $v->hourly_rate)) : 0;
                $v->gross = $v->rate_cost * $v->hours;
                $v->gross_ = $v->gross != 0 ? number_format($v->gross,2,'.',''):'0.00';
                $v->gross = $v->gross != 0 ? number_format($v->gross,0,'.',''):'0.00';

                $v->flight_debt = @$this->data['total_bal'][$date][$v->id]['flight_debt'];
                $v->flight = $v->flight_debt > 0 ? $v->flight : 0;

                $v->visa_debt = @$this->data['total_bal'][$date][$v->id]['visa_debt'];
                $v->visa = $v->visa_debt > 0 ? $v->visa : 0;

                $v->balance = @$this->data['total_bal'][$date][$v->id]['balance'];
                $v->installment = $v->balance > 0 ? $v->installment : 0;

                if(!$v->gross){
                    $v->tax = 0;
                    $v->kiwi_ = 0;
                }else{
                    $v->tax = 0;
                    $v->kiwi_ = 0;
                    $kiwi = $v->kiwi ? 'kiwi_saver_'.$v->kiwi : '';
                    if($v->wage_type == 1){
                        $whatVal = 'earnings ="'.$v->gross.'" AND start_date <= "'.$date.'" AND wage_type_id = "'.$v->wage_type.'"';
                        $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                        if(count($tax)>0){
                            foreach($tax as $tv){
                                $v->tax = $tv->m_paye;
                                $v->kiwi_ = $kiwi ? $tv->$kiwi : 0;
                            }
                        }
                    }else{
                        if($v->gross > $earnings){
                            $v->tax = ((floatval($v->gross) - $earnings) * 0.33) + $m_paye;
                        }else{
                            $whatVal = 'earnings ="'.$v->gross.'" AND start_date <= "'.$date.'" AND wage_type_id = "'.$v->wage_type.'"';
                            $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                            if(count($tax)>0){
                                foreach($tax as $tv){
                                    $v->tax = $tv->m_paye;
                                    $v->kiwi_ = $kiwi ? $tv->$kiwi : 0;
                                }
                            }
                        }
                    }
                }


                @$v->tax_total += $v->tax;
                @$v->total_install += $v->installment;
                @$v->total_flight += $v->flight;
                @$v->total_visa += $v->visa;
                @$v->total_accom += $v->accommodation;
                @$v->total_trans += $v->transport;
                @$v->total_account_two += $v->account_two;
                @$v->total_nz_account += $v->nz_account;
                @$v->total_kiwi += $v->kiwi_;

                $v->recruit = $v->visa ? $v->gross * 0.03 : 0;
                $v->admin = $v->visa ? $v->gross * 0.01 : 0;
                $v->net = $v->gross - ($v->kiwi_ + $v->tax + $v->flight + $v->recruit + $v->admin + $v->visa + $v->accommodation + $v->transport);
                $v->total = $v->total_kiwi + $v->tax_total + $v->total_install + $v->recruit + $v->admin + $v->total_flight + $v->total_visa + $v->total_accom + $v->total_trans;
                $v->distribution = $v->net - $v->installment;
                $v->account_one = $v->distribution - ($v->nz_account + $v->account_two);

                $v->account_one_ = $v->account_one > 0 ? $v->symbols.number_format($v->account_one * $converted_amount,2,'.',','):$v->symbols.' 0.00';
                $v->account_two_ = $v->symbols.number_format($v->total_account_two * $converted_amount,2,'.',',');
                $v->account_one = '$'.number_format($v->account_one,2,'.',',');

                $v->net = number_format($v->net,2,'.',',');
                $v->total = number_format($v->total,2,'.',',');
                $v->flight_debt = $v->flight_debt != '' ? $v->flight_debt :  '&nbsp;';
                $v->visa_debt = $v->visa_debt != '' ? $v->visa_debt :  '&nbsp;';
                $v->total_visa = $v->total_visa != 0 ? number_format($v->total_visa,2,'.',',') :  '&nbsp;';
                $v->total_trans = $v->total_trans != 0 ? number_format($v->total_trans,2,'.',',') : '&nbsp;';
                $v->total_install = $v->total_install != 0 ? number_format($v->total_install,2,'.',',') : '&nbsp;';
                $v->admin = number_format($v->admin,2,'.',',');
                $v->recruit = number_format($v->recruit,2,'.',',');
                $v->total_flight = $v->total_flight != 0 ? number_format($v->total_flight,2,'.',',') : '&nbsp;';
                $v->tax = $v->tax_total != 0 ? $v->tax_total : '&nbsp;';
                $v->star_balance = $v->balance != '' ? number_format($v->balance,2,'.',',') : '';
                $v->rem_balance = $v->balance - $v->total_install;
            }
        }

        return $data;
    }

    function getEmployerData($year,$month){

        $date = $this->getFirstNextLastDay($year,$month,'tuesday');
        $this->data['monthly_pay'] = array();
        $this->data['over_all_pay'] = array();
        $earnings = $this->data['earnings'];
        $m_paye = $this->data['m_paye'];
        $date_ = $year.'-'.$month.'-01';
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_rate',
                'tbl_tax_codes',
                'tbl_wage_type',
                'tbl_staff_rate'
            ),
            'join_field' => array('id','id','id','staff_id'),
            'source_field' => array(
                'tbl_staff.rate',
                'tbl_staff.tax_code_id',
                'tbl_staff.wage_type',
                'tbl_staff.id'
            ),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array(
            'tbl_staff.id',
            'tbl_staff.fname',
            'tbl_staff.lname',
            'tbl_rate.rate_cost',
            'IF(tbl_tax_codes.tax_code != "" , tbl_tax_codes.tax_code, "") as tax_code',
            'tbl_staff.ird_num',
            'tbl_wage_type.type as wage_type',
            'IF(tbl_staff.date_employed != "0000-00-00" ,DATE_FORMAT(tbl_staff.date_employed,"%d-%m-%Y"),"") as date_employed'
        ),false);
        $this->my_model->setGroupBy('id');
        $whatVal = 'tbl_staff_rate.start_use <= "'.$date_.'" AND tbl_staff.is_unemployed !=1';
        $this->data['staff'] = $this->my_model->getinfo('tbl_staff',$whatVal,'');
        if(count($date) >0){
            foreach($date as $dv){
                if(count($this->data['staff'])>0){
                    foreach($this->data['staff'] as $mv){
                        $monthly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->id) : 1;
                        $rate = $this->getStaffRate($mv->id,$dv);

                        if(count($rate) > 0){
                            foreach($rate as $val){
                                $mv->rate_name = $val->rate_name;
                                $mv->rate_cost = $val->rate;
                            }
                        }

                        if($monthly_hours != 0){
                            $mv->hours = $monthly_hours;
                            $mv->gross_ = number_format($mv->hours * $mv->rate_cost,2,'.','');
                            $mv->gross = number_format($mv->hours * $mv->rate_cost,0,'.','');
                            $mv->total_hours += $mv->hours;
                            $mv->total_gross += floatval($mv->gross);
                            if(!$mv->gross){
                                $mv->tax = 0;
                            }else{
                                $mv->tax = 0;
                                $mv->m_paye = 0;
                                $mv->me_paye = 0;
                                if($mv->gross > $earnings){
                                    $mv->tax = ((floatval($mv->gross) - $earnings) * 0.33) + $m_paye;
                                }else{
                                    $whatVal = 'earnings ="'.$mv->gross.'" AND start_date <= "'.$dv.'"';
                                    $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                                    if(count($tax)>0){
                                        foreach($tax as $tv){
                                            $mv->tax = $tv->m_paye;
                                            $mv->m_paye = $tv->m_paye;
                                            $mv->me_paye = $tv->me_paye;
                                        }
                                    }
                                }
                            }

                            $mv->total_tax += $mv->tax;
                            $this->data['monthly_pay'][$mv->id] = array(
                                'id' => $mv->id,
                                'rate_cost' => $mv->rate_cost,
                                'tax' => $mv->total_tax,
                                'hours' => $mv->total_hours,
                                'gross' => $mv->total_gross,
                                'm_paye' => $mv->m_paye,
                                'me_paye' => $mv->me_paye
                            );
                        }
                    }
                }
            }
        }
    }

    function getYearTotalBalance($year,$type = 'weekly',$set_range = false,$start = '',$end = ''){
        $year_week = $set_range ? $this->getWeekBetweenDates($start,$end) : $this->getWeekInYearBetweenDates($year);

        $this->my_model->setJoin(array(
            'table' => array('tbl_deductions','tbl_wage_type'),
            'join_field' => array('staff_id','id'),
            'source_field' => array('tbl_staff.id','tbl_staff.wage_type'),
            'type' => 'left'
        ));
        $deductions = $this->arrayWalk(array(
            'flight_deduct','flight_debt',
            'visa_deduct','visa_debt',
            'accommodation','transport'
        ),'tbl_deductions.');
        $staff = $this->arrayWalk(array(
            'id','tax_number','installment','balance',
            'nz_account','account_two','date_employed'
        ),'tbl_staff.');

        $fields = array_merge($deductions,$staff);
        $fields[] = 'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name';
        $fields[] = 'tbl_wage_type.type as wage_type';

        $this->my_model->setSelectFields($fields);
        $staff_list = $this->my_model->getinfo('tbl_staff',true,'tbl_staff.is_unemployed !=');

        $this->data['total_bal'] = array();

        if(count($year_week)>0){
            foreach($year_week as $y){
                if(count($staff_list) > 0){
                    foreach($staff_list as $ev){
                        $hours = $ev->wage_type != 1 ? $this->getTotalHours($y,$ev->id) : 1;
                        if($hours != 0){
                            $ev->balance -= $ev->installment;
                            $ev->visa_debt -= $ev->visa_deduct;
                            $ev->flight_debt -= $ev->flight_deduct;
                            switch($type){
                                case 'weekly':
                                    $this->data['total_bal'][$y][$ev->id] = array(
                                        'visa_debt' => $ev->visa_debt > 0 ? $ev->visa_debt : '',
                                        'flight_debt' => $ev->flight_debt > 0 ? $ev->flight_debt : '',
                                        'balance' => $ev->balance > 0 ? $ev->balance : ''
                                    );
                                    break;
                                case 'monthly':
                                    $this->data['total_bal'][date('Y-m',strtotime($y))][$ev->id] = array(
                                        'visa_debt' => $ev->visa_debt > 0 ? $ev->visa_debt : '',
                                        'flight_debt' => $ev->flight_debt > 0 ? $ev->flight_debt : '',
                                        'balance' => $ev->balance > 0 ? $ev->balance : ''
                                    );
                                    break;
                                default:
                                    break;
                            }
                        }
                    }
                }
            }
        }

    }

    function getStaffRate($id,$date = ''){
        $whatVal = $id;
        $whatFld = 'staff_id';

        if($date){
            $whatVal = 'start_use <= "' . $date . '" AND staff_id = "' . $id . '"';
            $whatFld = '';
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_rate'),
            'join_field' => array('id'),
            'source_field' => array('tbl_staff_rate.rate_id'),
            'type' => 'left'
        ));
        $fields = $this->arrayWalk($this->my_model->getFields('tbl_staff_rate',array('id')),'tbl_staff_rate.');
        $fields[] = 'tbl_rate.rate_cost';
        $fields[] = 'tbl_rate.rate_name';
        $this->my_model->setSelectFields($fields);
        $rate = $this->my_model->getInfo('tbl_staff_rate',$whatVal,$whatFld);

        return $rate;
    }

    function getStaffHourlyRate($id,$date = ''){
        $whatVal = $id;
        $whatFld = 'staff_id';

        if($date){
            $whatVal = 'date_used <= "' . $date . '" AND staff_id = "' . $id . '"';
            $whatFld = '';
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_hourly_nz_rate'),
            'join_field' => array('id'),
            'source_field' => array('tbl_staff_nz_rate.hourly_nz_rate_id'),
            'type' => 'left'
        ));
        $fields = $this->arrayWalk($this->my_model->getFields('tbl_staff_nz_rate',array('id')),'tbl_staff_nz_rate.');
        $fields[] = 'tbl_hourly_nz_rate.hourly_rate';
        $this->my_model->setSelectFields($fields);
        $rate = $this->my_model->getInfo('tbl_staff_nz_rate',$whatVal,$whatFld);

        return $rate;
    }

    function arrayWalk($array, $append, $type = 'front', $as = ''){
        $ar = array();
        if(count($array)>0){
            foreach($array as $k=>$v){
                switch($type){
                    case 'back':
                        $ar[$k] = $v . $append;
                        break;
                    case 'as':
                        $ar[$k] = $append . $v . ' as ' . $as . $v;
                        break;
                    default:
                        $ar[$k] = $append . $v;
                }
            }
        }

        return $ar;
    }

    function arrayHtmlEncode($data, $isObject = true) {
        if (is_array($data)) {
            return array_map(array($this,'arrayHtmlEncode'), $data);
        }

        if (is_object($data)) {
            $tmp = clone $data; // avoid modifying original object
            foreach ($data as $k => $var){
                $tmp->{$k} = $this->arrayHtmlEncode($var, $isObject);
            }

            $tmp = $isObject ? $tmp : (array)$tmp;

            return $tmp;
        }

        return htmlentities($data, ENT_QUOTES, "UTF-8");
    }

    function arrayHtmlDecode($data, $isObject = true) {
        if (is_array($data)) {
            return array_map(array($this,'arrayHtmlDecode'), $data);
        }

        if (is_object($data)) {
            $tmp = clone $data; // avoid modifying original object
            foreach ( $data as $k => $var ){
                $tmp->{$k} = $this->arrayHtmlDecode($var, $isObject);
            }

            $tmp = $isObject ? $tmp : (array)$tmp;

            return $tmp;
        }

        return html_entity_decode($data, ENT_QUOTES, "UTF-8");
    }

    function arraySort (&$array, $key, $isAsc = true){
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii=>$va) {
            $sorter[$ii] = $va->$key;
        }

        if($isAsc){
            uasort($sorter, array($this,'arraySortCompareAsc'));
            //asort($sorter);
        }else{
            uasort($sorter, array($this,'arraySortCompareDesc'));
            //arsort($sorter);
        }

        foreach ($sorter as $ii=>$va) {
            $ret[] = $array[$ii];
        }

        $array = $ret;
    }

    function arraySortCompareAsc($a, $b){
        return $a == $b ? 0 : ($a < $b ? -1 : 1);
    }

    function arraySortCompareDesc($a, $b){
        return $a == $b ? 0 : ($a > $b ? -1 : 1);
    }

    function sendMail($message, $options = array()){
        //NOTE:
        /*
        If using XAMPP as your localhost things to do:
            In php.ini file
            1. smtp = 'ssl://stmp.googlemail.com'
            2. smtp_port= '465'
            3. enable/add extension=php_openssl.dll (the most import that thing to send email via localhost)
        */

        $default = array(
            'to' => 'dummymailthedraftingzone@gmail.com',
            'to_alias' => '',
            'from' => 'donotreply@theestimator.co.nz',
            'name' => 'TEC Administrator',
            'subject' => 'Notification from The Estimator Ltd',
            'cc' => '',
            'cc_alias' => '',
            'bcc' => '',
            'bcc_alias' => '',
            'url' => '',
            'disposition' => 'attachment',
            'file_names' => '',
            'debug_type' => 0,
            'debug_return' => 0,
            'debug' => false
        );

        $option = count($options) > 0 ? array_merge($default, $options) : $default;
        $option = (Object)$option;

        $this->load->library('email');

        //region compatible sitehost only if not sending to own DOMAIN Emails
        /*//
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
        //*/
        //endregion

        //region localhost SMTP
        /*//
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        $config['smtp_port'] = 465;
        $config['mailtype'] = 'html';
        $config['smtp_user'] = 'dummymailthedraftingzone@gmail.com';
        $config['smtp_pass'] = 'dummypassword';
        //*/
        //endregion

        //region sitehost SMTP
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://mx1.sitehost.co.nz';
        $config['smtp_port'] = 465;
        $config['mailtype'] = 'html';
        $config['smtp_user'] = 'donotreply@theestimator.co.nz';
        $config['smtp_pass'] = 'apple1';
        //endregion

        $this->email->set_newline("\r\n");
        $this->email->initialize($config);

        $this->email->clear(TRUE);

        $this->email->from($option->from, $option->name);
        $this->email->_to_alias_array = $option->to_alias;
        $this->email->to($option->to);
        $this->email->_cc_alias_array = $option->cc_alias;
        $this->email->cc($option->cc);
        $this->email->_bcc_alias_array = $option->bcc_alias;
        $this->email->bcc($option->bcc);

        $this->email->subject($option->subject);
        $this->email->message($message);

        if($option->url){
            $this->sendEmailAddAttachment($option->url, $option->disposition, $option->file_names);
        }

        $isSuccess = 0;
        if($this->email->send()){
            $isSuccess = 1;
        }

        if($option->debug){
            $resultString = "";
            switch($option->debug_type){
                case 2:
                    $resultString = (Object)array(
                        'type' => $isSuccess,
                        'debug' => $this->email->print_debugger()
                    );
                    break;
                case 1:
                    $resultString = $this->email->print_debugger();
                    break;
                default:
                    $resultString = $isSuccess;
            }

            switch($option->debug_return){
                case 1:
                    echo $resultString;
                    break;
                default:
                    return $resultString;
            }
        }
    }

    function sendEmailAddAttachment($url, $disposition = "", $file_names = NULL){
        if(is_array($url) || is_object($url)){
            foreach($url as $key=>$file){
                $this->sendEmailAddAttachment(
                    $file,
                    array_key_exists($key, $disposition) ? $disposition[$key] : "attachment",
                    (is_array($url) || is_object($url)) ? (array_key_exists($key, $file_names) ? $file_names[$key] : NULL) : $file_names
                );
            }
        }

        if(@is_dir($url)){
            if ($prints = opendir($url)) {
                while (false !== ($file = readdir($prints))) {
                    if($file !== "." && $file !== ".."){
                        $this->email->attach(
                            $url . $file,
                            is_string($disposition) ? $disposition : 'attachment',
                            is_string($file_names) ? $file_names : NULL
                        );
                    }
                }
            }
        }
        if(@is_file($url)){
            $this->email->attach(
                $url,
                is_string($disposition) ? $disposition : 'attachment',
                is_string($file_names) ? $file_names : NULL
            );
        }
    }

    public function updateNotification(){
        if($this->session->userdata('is_logged_in') == false){
            redirect(''.'?p=login');
        }

        if(isset($_GET['is_view'])){
            $type = $this->uri->segment(2) ? $this->uri->segment(2) : '';

            $this->data['notification'] = $this->getInvoiceNotification($type);
            $this->data['count_msg'] = count($this->data['notification']);

            $this->load->view('backend/notification/notification_content_view',$this->data);
        }else if(isset($_GET['is_json'])){
            ini_set("memory_limit","512M");
            set_time_limit(90000);
            header("Content-type: application/json");
            echo json_encode($this->data['count_msg']);
        }else{
            if(isset($_GET['open'])){
                $id = $this->uri->segment(2);

                if(!$id){
                    exit;
                }

                $post = array(
                    'is_open' => true
                );
                $this->my_model->update('tbl_invoice',$post,$id);

                echo $id;
            }else{
                $id = $this->uri->segment(2);

                if(!$id){
                    exit;
                }

                $post = array(
                    'is_new' => false
                );
                $this->my_model->update('tbl_invoice',$post,$id);

                echo $id;
            }
        }

    }

    function getInvoiceNotification($type = ''){
        $this->my_model->setJoin(array(
            'table' => array('tbl_client','tbl_registration','tbl_quotation'),
            'join_field' => array('id','id','job_id'),
            'source_field' => array('tbl_invoice.client_id','tbl_invoice.job_id','tbl_registration.id'),
            'type' => 'left'
        ));
        $fields = $this->arrayWalk(
            array(
                'id','your_ref','job_id','meter','date','job_name','is_archive','is_open'
            ),
            'tbl_invoice.'
        );
        $fields[] = 'tbl_quotation.price';
        $fields[] = 'IF(tbl_invoice.job_id != 0 ,
                            CONCAT(tbl_client.client_code,LPAD(tbl_invoice.job_id, 5,"0")),
                            CONCAT(tbl_client.client_code,LPAD(tbl_invoice.id, 5,"0"),"-I")
                        )
                    as job_ref';
        $fields[] = 'tbl_registration.address';
        $fields[] = 'tbl_invoice.inv_ref';
        $fields[] = 'tbl_registration.job_name as reg_job_name';
        $fields[] = 'tbl_client.client_code';
        $fields[] = 'tbl_client.id as client_id';

        $this->my_model->setSelectFields($fields);

        $whatVal = array(true,false);
        $whatFld = array('tbl_invoice.is_new','tbl_invoice.is_archive');

        if($type){
            switch($type){
                case 1:
                    $whatVal = array(true,false,false);
                    $whatFld = array('tbl_invoice.is_new','tbl_invoice.is_open','tbl_invoice.is_archive');
                    break;
                case 2:
                    $whatVal = array(true,true,false);
                    $whatFld = array('tbl_invoice.is_new','tbl_invoice.is_open','tbl_invoice.is_archive');
                    break;
                default:
                    break;
            }
        }

        $invoice = $this->my_model->getInfo('tbl_invoice',$whatVal,$whatFld);

        return $invoice;
    }

    function getSickLeave($id,$date,$default = 5){
        $date = date('Y-m-d',strtotime($date));
        $this->my_model->setSelectFields(array('DATEDIFF("'.$date.'",MIN(start_use)) as date_diff'));
        $staff = $this->my_model->getInfo('tbl_staff_rate',$id,'staff_id');
        $leave = 0;

        if(count($staff) > 0){
           foreach($staff as $val){
               $total_days_in_year = date("z", mktime(0,0,0,06,30,date('Y',strtotime($date)))) + 1;
               $total_year =  $val->date_diff / $total_days_in_year;
               $int = explode('.',$total_year);
               $leave = $int[0] * $default;
           }
        }

        return $leave;
    }

    function getAnnualLeave($id,$date,$default = 20){
        $date = date('Y-m-d',strtotime($date));
        $this->my_model->setSelectFields(array('DATEDIFF("'.$date.'",MIN(start_use)) as date_diff'));
        $staff = $this->my_model->getInfo('tbl_staff_rate',$id,'staff_id');
        $leave = 0;

        if(count($staff) > 0){
            foreach($staff as $val){
                $total_days_in_year = date("z", mktime(0,0,0,12,31,date('Y',strtotime($date)))) + 1;
                $total_year =  $val->date_diff / $total_days_in_year;
                $int = explode('.',$total_year);
                $leave = $int[0] * $default;
            }
        }

        return $leave;
    }
}