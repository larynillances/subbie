<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property My_Model $my_model Optional description
 */
include('wage_controller.php');
include('staff_helper.php');
include('send_email_controller.php');

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

        $this->load->view('frontend/login_view',$this->data);
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
                redirect('?page=login');
            }

            if($data['is_logged_in']){
                $this->session->set_userdata($data);
                switch($data['account_type']){
                    case 1:
                        redirect('timeSheetDefault');
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

        redirect('?page=login');
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
        $this->my_model->setSelectFields(array('MAX(earnings) as earnings'));
        $this->data['earnings']['fortnightly'] = $this->my_model->getInfo('tbl_tax',2,'frequency_id');

        $this->my_model->setLastId('m_paye');
        $this->my_model->setSelectFields(array('MAX(m_paye) as m_paye'));
        $this->data['m_paye']['fortnightly'] = $this->my_model->getInfo('tbl_tax',2,'frequency_id');

        $this->my_model->setLastId('earnings');
        $this->my_model->setSelectFields(array('MAX(earnings) as earnings'));
        $this->data['earnings']['weekly'] = $this->my_model->getInfo('tbl_tax',1,'frequency_id');

        $this->my_model->setLastId('m_paye');
        $this->my_model->setSelectFields(array('MAX(m_paye) as m_paye'));
        $this->data['m_paye']['weekly'] = $this->my_model->getInfo('tbl_tax',1,'frequency_id');

        $this->data['info_array'] = $this->my_model->getInfo('tbl_invoice_info');

        $this->data['account_type'] = $this->session->userdata('account_type');

        $this->my_model->setShift();
        $this->data['user'] = (Object)$this->my_model->getInfo('tbl_user',$this->session->userdata('user_id'));


        $this->data['notification'] = $this->getInvoiceNotification(1);
        $this->data['count_msg'] = count($this->data['notification']);

        $this->data['notification_dp'] = array(
            '1' => 'New',
            '2' => 'Active',
            '3' => 'Archived'
        );

        $this->my_model->setShift();
        $this->data['user_data'] = (Object)$this->my_model->getInfo('tbl_user',3);

        $this->my_model->setJoin(array(
            'table' => array('tbl_downloadable_form','tbl_account_type'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_user_download_form.form_id','tbl_user_download_form.account_type_id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array(
            'tbl_user_download_form.id',
            'tbl_downloadable_form.menu_name',
            'tbl_downloadable_form.file_name',
            'tbl_account_type.account_type'
        ));
        $form_links = $this->my_model->getInfo('tbl_user_download_form',$this->data['account_type'],'account_type_id');

        $links_array = array();
        if(count($form_links) > 0){
            foreach($form_links as $row){
                $links = 'uploads/form/'.$row->file_name;
                $links_array[$links] = $row->menu_name;
            }
        }

        $this->data['form_links'] = $links_array;

        if($has_return){
            return $this->data;
        }
    }

    function getWorkingHours($date,$id,$working = 1,$action = 'weekly',$start_date = 2){
        $totalHours = array();
        $hours_gain = array();
        $year = date('Y',strtotime($date));
        $month = date('m',strtotime($date));
        $_date = new DateTime($date);
        $week = $_date->format('W');
        $week_data = StartWeekNumber($week,$year);
        $dt = new DateTime;
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $this->my_model->setJoin(array(
            'table' => array('tbl_holiday_type as tbl_holiday_leave','tbl_holiday_type as tbl_sick_leave'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_login_sheet.holiday_type_id','tbl_login_sheet.sick_leave_type_id'),
            'type' => 'left',
            'join_append' => array('tbl_holiday_leave','tbl_sick_leave')
        ));
        $this->my_model->setSelectFields(array(
            'TIMESTAMPDIFF(SECOND, time_in, time_out) as hours',
            'time_in','time_out','staff_id','date',
            'tbl_login_sheet.id as dtr_id','working_type_id','tbl_holiday_leave.hours as holiday_hours',
            'tbl_holiday_leave.day_number as holiday_day_number', 'tbl_sick_leave.hours as sick_hours',
            'tbl_sick_leave.day_number as sick_day_number'

        ));

        $dtr = $this->my_model->getinfo('tbl_login_sheet', $id,'staff_id');

        if(count($dtr) >0){
            foreach($dtr as $dv){
                $time_in = strtotime(date('g:i a',strtotime($dv->time_in)));
                $time_out = strtotime(date('g:i a',strtotime($dv->time_out)));
                $break_time_deduction = 0;
                $hours = 0;

                if($dv->working_type_id == 2){
                    $total_leave = $dv->holiday_hours + $dv->sick_hours;
                    $hours += $total_leave;
                }else{
                    if($time_in > 0 && $time_out > 0){
                        $break_time_deduction = BreakTimeDeduction($dv->time_in,$dv->time_out,true);
                        $hours = $dv->hours;
                    }
                }

                $hours_gain[$dv->staff_id][$dv->working_type_id][$dv->date] = $hours > 0 ? ($hours - $break_time_deduction) : 0;
            }
        }

        $_start_day = $week_data['start_day'];
        $_end_day = $week_data['end_day'];
        switch($action){
            case 'weekly':
                for($whatDay=$_start_day; $whatDay<=$_end_day; $whatDay++){
                    $getDate =  $dt->setISODate($year, $week , $whatDay)->format('Y-m-d');
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

    function getTotalHours($date,$id,$action = 'weekly'){
        $totalHours = array();
        $hours_gain = array();
        $_date = new DateTime($date);
        $week = $_date->format('W');
        $year = date('Y',strtotime($date));
        $month = date('m',strtotime($date));
        $week_data = StartWeekNumber($week,$year);
        $dt = new DateTime;
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $this->my_model->setSelectFields(array(
            'IF(time_out != "" AND time_in != "", TIMESTAMPDIFF(SECOND, time_in, time_out) , 0) as hours',
            'time_in','time_out','staff_id','date',
            'id as dtr_id','working_type_id'
        ));

        $dtr = $this->my_model->getinfo('tbl_login_sheet', $id,'staff_id');
        if(count($dtr) >0){
            foreach($dtr as $dv){
                $time_in = strtotime(date('g:i a',strtotime($dv->time_in)));
                $time_out = strtotime(date('g:i a',strtotime($dv->time_out)));
                $break_time_deduction = 0;
                $hours = 0;
                if($time_in > 0 && $time_out > 0){
                    $break_time_deduction = BreakTimeDeduction($dv->time_in,$dv->time_out,true);
                    $hours = $dv->hours;
                }

                $hours_gain[$dv->staff_id][$dv->date] = $hours > 0 ? ($hours - $break_time_deduction) : 0;
            }
        }
        $_start_day = $week_data['start_day'];
        $_end_day = $week_data['end_day'];
        switch($action){
            case 'weekly':
                for($whatDay=$_start_day; $whatDay<=$_end_day; $whatDay++){
                    $getDate =  $dt->setISODate($year, $week , $whatDay)->format('Y-m-d');
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

    function getTotalHoursInMonth($date,$id,$action = 'weekly'){
        $totalHours = array();
        $hours_gain = array();
        $_date = new DateTime($date);
        $week = $_date->format('W');
        $year = date('Y',strtotime($date));
        $month = date('m',strtotime($date));

        $start_fin_month = $year.'-04-01';
        $start_fin_day = date('N',strtotime($start_fin_month));
        $end_fin_month = $year.'-03-31';
        $end_fin_day = date('N',strtotime($end_fin_month));
        $last_day_of_march = $this->last_day_of_month($year);

        $week_data = StartWeekNumber($week,$year);
        $dt = new DateTime;
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $this->my_model->setSelectFields(array(
            'IF(time_out != "" AND time_in != "", TIMESTAMPDIFF(SECOND, time_in, time_out) , 0) as hours',
            'time_in','time_out','staff_id','date',
            'id as dtr_id','working_type_id'
        ));

        $dtr = $this->my_model->getinfo('tbl_login_sheet', $id,'staff_id');
        if(count($dtr) >0){
            foreach($dtr as $dv){
                $time_in = strtotime(date('g:i a',strtotime($dv->time_in)));
                $time_out = strtotime(date('g:i a',strtotime($dv->time_out)));
                $break_time_deduction = 0;
                $hours = 0;
                if($time_in > 0 && $time_out > 0){
                    $break_time_deduction = BreakTimeDeduction($dv->time_in,$dv->time_out,true);
                    $hours = $dv->hours;
                }

                $hours_gain[$dv->staff_id][$dv->date] = $hours > 0 ? ($hours - $break_time_deduction) : 0;
            }
        }

        $_start_day = $start_fin_month == $date ? $start_fin_day : $week_data['start_day'];

        $_end_day = $date == $last_day_of_march ? $end_fin_day : $week_data['end_day'];
        switch($action){
            case 'weekly':
                for($whatDay=$_start_day; $whatDay<=$_end_day; $whatDay++){
                    $getDate =  $dt->setISODate($year, $week , $whatDay)->format('Y-m-d');
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

    function getWeekBetweenDates($start,$end,$day_start = 'tuesday'){
        $year = date('Y',strtotime($start));
        $year_week = array();

        if($year == 2015){
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

        }else if($year < 2015){
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
        }else{

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

    function getWeekInYearBetweenDates($end_year,$start_year = 2014,$start = 2){
        $year_week = array();
        if($end_year == 2015){
            $first_year_week_period = new DatePeriod(
                new DateTime("$start_year-W01-$start"),
                new DateInterval('P1W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );

            $second_year_week_period = new DatePeriod(
                new DateTime("$start_year-W31-1"),
                new DateInterval('P1W'),
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
                new DateTime("$start_year-W01-$start"),
                new DateInterval('P1W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
                }
            }
            $second_year_week_period = new DatePeriod(
                new DateTime("$start_year-W31-1"),
                new DateInterval('P1W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($second_year_week_period as $week => $tuesday) {
                if($tuesday->format('Y-m-d') <= date('Y-m-d')){
                    $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
                }
            }
        }else{
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$start_year-W01-1"),
                new DateInterval('P1W'),
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

    function getFirstNextLastDay($y, $m)
    {
        $start_date = $y.'-'.$m.'-01';
        $start_day = date('l',strtotime($start_date));

        $day = strtotime($start_date) > strtotime('2015-07-26') ? 'monday' : 'tuesday';
        $begin = new DateTime("first $day of $y-$m");

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
                $date[$week_num] = $this_date;
            }
        }
        return $date;
    }

    function getPaymentStartDate($year,$month = 'April',$day = 'tuesday',$start = 2){

        $month_day = $year.'-04-01';
        $next_day = $this->first_day_of_month($year,$month,$day);
        $next_day = date('Y-m-d',strtotime('-1 day '.$next_day));
        $date = new DateTime($month_day);
        $week = $date->format("W");

        $end_year = $year + 1;
        $end_month_day = $end_year.'-03-31';//$this->first_day_of_month($end_year,$month,$day);
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

    function first_day_of_month($year,$month = 'April',$day="Tuesday"){
        $_day = $year > 2015 ? 'Monday' : $day;
        $day = new DateTime(sprintf("First $_day of $month %s", $year));
        return $day->format('Y-m-d');
    }

    function last_day_of_month($year,$month = 'March',$day="Tuesday"){
        $_day = $year > 2015 ? 'Monday' : $day;
        $day = new DateTime(sprintf("Last $_day of $month %s", $year));
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
        $date_ = new DateTime($date);
        $start_ = StartWeekNumber($date_->format('W'),$y);

        for($whatDay=$start_['start_day']; $whatDay<=$start_['end_day']; $whatDay++){
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

    function getDaysInWeek(){

        $days = array();
        $year = date('Y');
        $week_number = date('W');
        for($day=1; $day<=7; $day++)
        {
            $days[$day] = date('l',strtotime($year."W".$week_number.$day));
        }

        return $days;
    }

    function getWeeksNumberInMonth($year, $month){
        $days = $this->getFirstNextLastDay($year,$month);
        $thisDays = array();

        if(count($days) > 0){
            foreach($days as $key=>$v){
                $thisDays[$key] = $key;
            }
        }
        $days_array = $thisDays;
        return $days_array;
    }

    function getWeekDateInMonth($year, $month){
        $days = $this->getFirstNextLastDay($year,$month);
        $thisDays = array();

        if(count($days) > 0){
            foreach($days as $key=>$v){
                $thisDays[$key] = $v;
            }
        }

        $days_array = $thisDays;
        return $days_array;
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

    function getStaffLastPay($id = '',$year,$week = ''){
        $whatVal = array(false);
        $whatFld = array('is_unemployed');
        if($id || count($id) > 0){
            $whatVal = is_array($id) ? $id : array($id);
            $whatFld = is_array($id) ?  'tbl_staff.id' : array('tbl_staff.id');
        }


        $staff_data = new Staff_Helper();
        $staff_list = $staff_data->staff_details($whatVal,$whatFld);
        $set_wage_date = $this->getPaymentStartDate($year);
        $wage_total_data = array();
        $wage_data = new Wage_Controller();
        $this->data['total_bal'] = $wage_data->get_year_total_balance();
        $rate = $staff_data->staff_rate();
        $hourly_rate = $staff_data->staff_hourly_rate();
        //$kiwi_data = $staff_data->staff_kiwi();

        if(count($set_wage_date) > 0){
            foreach($set_wage_date as $key=>$dv){
                $_date = new DateTime($key);
                $_year = $_date->format('Y');
                $_week = $_date->format('W');
                if(count($staff_list) > 0){
                    foreach($staff_list as $ev){

                        if(strtotime($key) <= strtotime(date('Y-m-d'))){
                            $ev->start_use = '';
                            //$ev->kiwi = '';

                            if(count(@$rate[$ev->id]) > 0){
                                foreach(@$rate[$ev->id] as $start_use=>$val){
                                    if(strtotime($start_use) <= strtotime($key) ||
                                        strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$key)))){
                                        $ev->rate_name = $val->rate_name;
                                        $ev->rate_cost = $val->rate;
                                        $ev->start_use = $val->start_use;
                                    }
                                }
                            }

                            /*if(count(@$kiwi_data[$ev->id]) > 0){
                                foreach(@$kiwi_data[$ev->id] as $start_use=>$val){
                                    if(strtotime($start_use) <= strtotime($key) ||
                                        strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$key)))){
                                        $ev->kiwi = $val->kiwi;
                                        $ev->employer_kiwi = $val->employer_kiwi;
                                        $ev->field_name = $val->field_name;
                                        $ev->esct_rate = $val->esct_rate;
                                    }
                                }
                            }*/

                            $then = date('Y-m-d H:i:s',strtotime($ev->date_employed));
                            $then = new DateTime($then);

                            $now = date('Y-m-d H:i:s',strtotime($key));
                            $now = new DateTime($now);

                            $sinceThen = $then->diff($now);

                            $leave = $this->countLeaveTaken($ev->id,$key);

                            $holiday_leave_taken = $leave['holiday_leave'];
                            $sick_leave_taken = $leave['sick_leave'];

                            $overall_holiday_leave = $this->getAnnualLeave($ev->id,$key);
                            $overall_sick_leave = $this->getSickLeave($ev->id,$key);

                            $total_holiday_leave = $overall_holiday_leave - $holiday_leave_taken;
                            $total_sick_leave = $overall_sick_leave - $sick_leave_taken;

                            $ev->tax = 0;
                            $ev->m_paye = 0;
                            $ev->me_paye = 0;
                            $ev->kiwi_ = 0;
                            $ev->st_loan = 0;

                            $hours = $ev->wage_type != 1 ? $this->getTotalHoursInMonth($key,$ev->id) : 1;
                            $ev->gross = $ev->rate_cost * $hours;
                            $ev->gross_ = $ev->gross != 0 ? floatval(number_format($ev->gross,2,'.','')):'0.00';
                            //$ev->gross = $ev->gross != 0 ? floatval(number_format($ev->gross,0,'.','')):'0.00';
                            $ev->kiwi_ = 0;
                            $kiwi = $ev->kiwi ? 'kiwi_saver_'.$ev->kiwi : '';

                            $data_ = $this->getPayeValue($ev->field_code,$ev->frequency_id,$key,$ev->gross,$kiwi);
                            if(count($data_) > 0){
                                $ev->tax = $data_['tax'];
                                $ev->m_paye = $data_['m_paye'];
                                $ev->me_paye = $data_['me_paye'];
                                $ev->kiwi_ = $kiwi ? $data_['kiwi'] : 0;
                                $ev->st_loan = $ev->has_st_loan ? $data_['st_loan'] : 0;
                            }

                            $ev->hourly_rate = 0;
                            if(count(@$hourly_rate[$ev->id]) > 0){
                                foreach(@$hourly_rate[$ev->id] as $val){
                                    $ev->hourly_rate = $val->hourly_rate;
                                }
                            }

                            $ev->nz_account_ = $ev->nz_account ? $ev->nz_account + ($hours * $ev->hourly_rate) : 0;
                            $ev->flight_debt = @$this->data['total_bal'][$ev->id][$_year][$_week]['flight_debt'];
                            $ev->flight_deduct = $ev->flight_debt > 0 ?
                                ($ev->flight_debt <= $ev->flight_deduct ? $ev->flight_debt : $ev->flight_deduct) : 0;

                            $ev->visa_debt = @$this->data['total_bal'][$ev->id][$_year][$_week]['visa_debt'];
                            $ev->visa_deduct = $ev->visa_debt > 0 ?
                                ($ev->visa_debt <= $ev->visa_deduct ? $ev->visa_debt : $ev->visa_deduct) : 0;

                            $ev->balance = @$this->data['total_bal'][$ev->id][$_year][$_week]['balance'];
                            $ev->installment = $ev->balance > 0 ?
                                ($ev->balance <= $ev->installment ? $ev->balance : $ev->installment) : 0;

                            $ev->recruit = $ev->visa_deduct ? $ev->gross_ * 0.03 : 0;
                            $ev->admin = $ev->visa_deduct ? $ev->gross_ * 0.01 : 0;
                            $ev->nett = $ev->gross_ - ($ev->st_loan + $ev->kiwi_ + $ev->tax + $ev->flight_deduct + $ev->visa_deduct + $ev->accommodation + $ev->transport + $ev->recruit + $ev->admin);

                            $ev->distribution = $ev->nett - $ev->installment;
                            $ev->account_one = $ev->distribution - ($ev->nz_account_ + $ev->account_two);
                            //$ev->annual_pay = $sinceThen->y == 0 ? ($ev->gross * 0.08) : ($sinceThen->y > 0 && $sinceThen->m > 0 ? ($ev->gross * 0.08) + ($ev->gross * $total_holiday_leave) : ($ev->gross * $total_holiday_leave));

                            if($ev->gross > 0){
                                @$ev->total_distribution +=  floatval($ev->distribution);
                                @$ev->total_gross +=  floatval($ev->gross_);
                                @$ev->total_account_one +=  $ev->account_one > 0 ? floatval($ev->account_one) : 0;
                                @$ev->total_account_two +=  floatval($ev->account_two);
                                @$ev->total_nz_account +=  floatval($ev->nz_account_);

                                $ev->annual_pay = $sinceThen->y == 0 ? (($ev->total_gross + $ev->gross_) * 0.08) : ($ev->gross_ * $total_holiday_leave);
                                $annual_ = $this->getPayeValue($ev->field_code,$ev->frequency_id,$key,$ev->annual_pay,'','','','',true);

                                $ev->annual_tax = 0;
                                if(count($annual_) > 0){
                                    $ev->annual_tax = $annual_['tax'];
                                }

                                $options = is_array($week) ? count($week) > 0 && $week[$ev->id] == $_week : $week && $week == $_week;
                                if($options){
                                    $wage_total_data[$ev->id] = array(
                                        'total_distribution' => $ev->total_distribution,
                                        'total_gross' => floatval($ev->total_gross + $ev->gross_),
                                        'financial_year' => date('M-d-Y',mktime(0,0,0,4,1,date('Y',strtotime($year)))) .' to '.date('M-d-Y',mktime(0,0,0,3,31,date('Y',strtotime('+1 year '.$year)))),
                                        'total_account_one' => $ev->total_account_one,
                                        'total_account_two' => $ev->total_account_two,
                                        'total_nz_account' => $ev->total_nz_account,
                                        'distribution' => floatval($ev->distribution),
                                        'last_date_pay' => $key,
                                        'hours' => floatval($hours),
                                        'tax' => floatval($ev->tax),
                                        'gross' => floatval($ev->gross_),
                                        'account_one' => $ev->account_one > 0 ? floatval($ev->account_one) : 0,
                                        'account_two' => floatval($ev->account_two),
                                        'nz_account' => floatval($ev->nz_account_),
                                        'annual_leave_pay' => floatval($ev->annual_pay),
                                        'annual_tax' => $ev->annual_tax,
                                        'last_week' => $_week,
                                        'calculation_type' => $sinceThen->y == 0 ? '8%' : 'Avg. Weekly',
                                        'total_holiday_leave' => $total_holiday_leave,
                                        'holiday_leave_taken' => $holiday_leave_taken,
                                        'total_sick_leave' => $total_sick_leave,
                                        'sick_leave_taken' => $sick_leave_taken,
                                        'overall_holiday_leave' => $overall_holiday_leave,
                                        'overall_sick_leave' => $overall_sick_leave,
                                    );
                                }else{
                                    $wage_total_data[$ev->id] = array(
                                        'total_distribution' => $ev->total_distribution,
                                        'total_gross' => floatval($ev->total_gross + $ev->gross_),
                                        'financial_year' => date('M-d-Y',mktime(0,0,0,4,1,date('Y',strtotime($year)))) .' to '.date('M-d-Y',mktime(0,0,0,3,31,date('Y',strtotime('+1 year '.$year)))),
                                        'total_account_one' => $ev->total_account_one,
                                        'total_account_two' => $ev->total_account_two,
                                        'total_nz_account' => $ev->total_nz_account,
                                        'distribution' => floatval($ev->distribution),
                                        'last_date_pay' => $key,
                                        'hours' => floatval($hours),
                                        'tax' => floatval($ev->tax),
                                        'gross' => floatval($ev->gross_),
                                        'account_one' => $ev->account_one > 0 ? floatval($ev->account_one) : 0,
                                        'account_two' => floatval($ev->account_two),
                                        'nz_account' => floatval($ev->nz_account_),
                                        'annual_leave_pay' => floatval($ev->annual_pay),
                                        'annual_tax' => $ev->annual_tax,
                                        'last_week' => $_week,
                                        'calculation_type' => $sinceThen->y == 0 ? '8%' : 'Avg. Weekly',
                                        'total_holiday_leave' => $total_holiday_leave,
                                        'holiday_leave_taken' => $holiday_leave_taken,
                                        'total_sick_leave' => $total_sick_leave,
                                        'sick_leave_taken' => $sick_leave_taken,
                                        'overall_holiday_leave' => $overall_holiday_leave,
                                        'overall_sick_leave' => $overall_sick_leave,
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        return $wage_total_data;
    }

    function getOverAllWageTotalPay($date,$id = '',$is_details = false){

        $this_year = date('Y',strtotime($date));
        $first_tuesday_in_month = $this_year.'-04-01';//$this->first_day_of_month($this_year);
        $year = strtotime($first_tuesday_in_month) > strtotime($date) ? date('Y-m-d',strtotime('-1 year'.$date)) : date('Y-m-d',strtotime($date));

        $whatVal = array(false,1);
        $whatFld = array('is_unemployed','project_id');

        $what_val = 'date_last_pay != "0000-00-00" AND YEAR(date_last_pay) ="'.$this_year.'" AND has_final_pay =1';

        if($id){
            $whatVal = array($id);
            $whatFld = array('tbl_staff.id');

            $what_val = 'id ="'.$id.'" AND date_last_pay != "0000-00-00" AND YEAR(date_last_pay) ="'.$this_year.'" AND has_final_pay =1';
        }

        $staff_data = new Staff_Helper();
        $staff_list = $staff_data->staff_details($whatVal,$whatFld);
        $set_wage_date = $this->getPaymentStartDate(date('Y',strtotime($year)));
        $wage_total_data = array();
        $wage_data = new Wage_Controller();
        $this->data['total_bal'] = $wage_data->get_year_total_balance();
        $rate = $staff_data->staff_rate();
        //$kiwi_data = $staff_data->staff_kiwi();
        $hourly_rate = $staff_data->staff_hourly_rate();

        $_id = array();
        $staff_ = $this->my_model->getInfo('tbl_staff',$what_val,'');

        $week_data = array();
        if(count($staff_) > 0){
            foreach($staff_ as $row){
                $_id[] = $row->id;
                $week_data[$row->id] = $row->last_week_pay;
            }
        }
        $last_pay_data = count($_id) > 0 ? $this->getStaffLastPay($_id,$this_year,$week_data) : array();
        if(count($set_wage_date) > 0){
            foreach($set_wage_date as $key=>$dv){
                $_date = new DateTime($key);
                $_year = $_date->format('Y');
                $_week = $_date->format('W');
                if(count($staff_list) > 0){
                    foreach($staff_list as $ev){

                        if(strtotime($key) <= strtotime(date('Y-m-d'))){
                            $ev->start_use = '';
                            //$ev->kiwi = '';

                            if(count(@$rate[$ev->id]) > 0){
                                foreach(@$rate[$ev->id] as $start_use=>$val){
                                    if(strtotime($start_use) <= strtotime($key) ||
                                        strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$key)))){
                                        $ev->rate_name = $val->rate_name;
                                        $ev->rate_cost = $val->rate;
                                        $ev->start_use = $val->start_use;
                                    }
                                }
                            }

                            /*if(count(@$kiwi_data[$ev->id]) > 0){
                                foreach(@$kiwi_data[$ev->id] as $start_use=>$val){
                                    if(strtotime($start_use) <= strtotime($key) ||
                                        strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$key)))){
                                        $ev->kiwi = $val->kiwi;
                                        $ev->employer_kiwi = $val->employer_kiwi;
                                        $ev->field_name = $val->field_name;
                                        $ev->esct_rate = $val->esct_rate;
                                    }
                                }
                            }*/

                            $ev->tax = 0;
                            $ev->m_paye = 0;
                            $ev->me_paye = 0;
                            $ev->kiwi_ = 0;
                            $ev->st_loan = 0;

                            $hours = $ev->wage_type != 1 ? $this->getTotalHoursInMonth($key,$ev->id) : 1;
                            $ev->gross = $ev->rate_cost * $hours;
                            $ev->gross_ = $ev->gross != 0 ? floatval(number_format($ev->gross,2,'.','')):'0.00';
                            //$ev->gross = $ev->gross != 0 ? floatval(number_format($ev->gross,0,'.','')):'0.00';
                            $ev->kiwi_ = 0;
                            $kiwi = $ev->kiwi ? 'kiwi_saver_'.$ev->kiwi : '';

                            $data_ = $this->getPayeValue($ev->field_code,$ev->frequency_id,$key,$ev->gross,$kiwi);
                            if(count($data_) > 0){
                                $ev->tax = number_format($data_['tax'],2,'.','');
                                $ev->m_paye = $data_['m_paye'];
                                $ev->me_paye = $data_['me_paye'];
                                $ev->kiwi_ = $kiwi ? $data_['kiwi'] : 0;
                                $ev->st_loan = $ev->has_st_loan ? $data_['st_loan'] : 0;
                            }

                            $ev->hourly_rate = 0;
                            if(count(@$hourly_rate[$ev->id]) > 0){
                                foreach(@$hourly_rate[$ev->id] as $val){
                                    $ev->hourly_rate = $val->hourly_rate;
                                }
                            }

                            $ev->nz_account_ = $ev->nz_account ? $ev->nz_account + ($hours * $ev->hourly_rate) : 0;
                            $ev->flight_debt = @$this->data['total_bal'][$ev->id][$_year][$_week]['flight_debt'];
                            $ev->flight_deduct = $ev->flight_debt > 0 ?
                                ($ev->flight_debt <= $ev->flight_deduct ? $ev->flight_debt : $ev->flight_deduct) : 0;

                            $ev->visa_debt = @$this->data['total_bal'][$ev->id][$_year][$_week]['visa_debt'];
                            $ev->visa_deduct = $ev->visa_debt > 0 ?
                                ($ev->visa_debt <= $ev->visa_deduct ? $ev->visa_debt : $ev->visa_deduct) : 0;

                            $ev->balance = @$this->data['total_bal'][$ev->id][$_year][$_week]['balance'];
                            $ev->installment = $ev->balance > 0 ?
                                ($ev->balance <= $ev->installment ? $ev->balance : $ev->installment) : 0;

                            $ev->recruit = $ev->visa_deduct ? $ev->gross_ * 0.03 : 0;
                            $ev->admin = $ev->visa_deduct ? $ev->gross_ * 0.01 : 0;
                            $ev->nett = $ev->gross_ - ($ev->st_loan + $ev->kiwi_ + $ev->tax + $ev->flight_deduct + $ev->visa_deduct + $ev->accommodation + $ev->transport + $ev->recruit + $ev->admin);

                            $ev->distribution = $ev->nett - $ev->installment;
                            $ev->account_one = $ev->distribution - ($ev->nz_account_ + $ev->account_two);

                            $pay_data = @$last_pay_data[$ev->id];
                            $last_day = date('Y-m-d',strtotime('+6 days '.$pay_data['last_date_pay']));
                            $final_pay = 0;
                            $final_gross = 0;
                            if($pay_data['last_week'] == $_week){
                                $final_pay = ($pay_data['annual_leave_pay'] - $pay_data['annual_tax']) + $pay_data['distribution'];
                                $final_gross = $pay_data['gross'];
                            }

                            if($ev->gross > 0){
                                @$ev->total_distribution +=  floatval($ev->distribution + $final_pay);
                                @$ev->total_gross +=  floatval($ev->gross_ + $final_gross);
                                @$ev->total_account_one +=  $ev->account_one > 0 ? floatval($ev->account_one) : 0;
                                @$ev->total_account_two +=  floatval($ev->account_two);
                                @$ev->total_nz_account +=  floatval($ev->nz_account_);

                                if($is_details){
                                    /*$wage_total_data[$ev->id][$last_day] = array(
                                        'distribution' => floatval($final_pay),
                                        'gross' => floatval($final_gross),
                                        'financial_year' => date('M-d-Y',mktime(0,0,0,4,1,date('Y',strtotime($year)))) .' to '.date('M-d-Y',mktime(0,0,0,3,31,date('Y',strtotime('+1 year '.$year)))),
                                        'account_one' => $ev->account_one > 0 ? floatval($ev->account_one) : 0,
                                        'account_two' => floatval($ev->account_two),
                                        'nz_account' => floatval($ev->nz_account_),
                                        'final_pay' => $final_pay
                                    );*/

                                    $wage_total_data[$ev->id][$key] = array(
                                        'distribution' => $final_pay ? floatval($ev->distribution + $final_pay) : floatval($ev->distribution),
                                        'gross' => $final_gross ? floatval($ev->gross_ + $final_gross) : floatval($ev->gross_),
                                        'financial_year' => date('M-d-Y',mktime(0,0,0,4,1,date('Y',strtotime($year)))) .' to '.date('M-d-Y',mktime(0,0,0,3,31,date('Y',strtotime('+1 year '.$year)))),
                                        'account_one' => $ev->account_one > 0 ? floatval($ev->account_one) : 0,
                                        'account_two' => floatval($ev->account_two),
                                        'nz_account' => floatval($ev->nz_account_),
                                        'final_pay' => $final_pay
                                    );
                                }else{

                                    /*$wage_total_data[$ev->id][$key] = array(
                                        'distribution' => $ev->total_distribution,
                                        'gross' => floatval($ev->total_gross),
                                        'financial_year' => date('M-d-Y',mktime(0,0,0,4,1,date('Y',strtotime($year)))) .' to '.date('M-d-Y',mktime(0,0,0,3,31,date('Y',strtotime('+1 year '.$year)))),
                                        'account_one' => $ev->total_account_one,
                                        'account_two' => $ev->total_account_two,
                                        'nz_account' => $ev->total_nz_account,
                                        'final_pay' => $final_pay
                                    );*/

                                    $wage_total_data[$ev->id][$key] = array(
                                        'distribution' => $ev->total_distribution,
                                        'gross' => floatval($ev->total_gross),
                                        'financial_year' => date('M-d-Y',mktime(0,0,0,4,1,date('Y',strtotime($year)))) .' to '.date('M-d-Y',mktime(0,0,0,3,31,date('Y',strtotime('+1 year '.$year)))),
                                        'account_one' => $ev->total_account_one,
                                        'account_two' => $ev->total_account_two,
                                        'nz_account' => $ev->total_nz_account,
                                        'final_pay' => $final_pay
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
        //DisplayArray($wage_total_data);
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

        $fields = ArrayWalk(array('product_name','price'),'tbl_product_list.');
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
        $wage_data = new Wage_Controller();
        $staff_data = new Staff_Helper();

        //$whatVal = array('0000-00-00',3);
        //$whatFld = array('date_employed !=','status_id');

        $whatVal = '(date_employed != "0000-00-00" AND project_id ="1")';
        $whatFld = '';

        $staff_list = $staff_data->staff_details($whatVal,$whatFld);

        $this->data['total_bal'] = $wage_data->get_year_total_balance();
        $rate = $staff_data->staff_rate();
        $hourly_rate = $staff_data->staff_hourly_rate();
        //$kiwi_data = $staff_data->staff_kiwi();

        switch($type){
            case 'weekly':
                $this->data['balance'] = array();
                $this->data['wage_data'] = array();

                $this->data['date'] = $this->getFirstNextLastDay($year,$month);
                $this->data['wage_total_data'] = array();
                $phpCurrency = CurrencyConverter('PHP');

                if(count($this->data['date']) > 0){
                    foreach($this->data['date'] as $dv){
                        $_date = new DateTime($dv);
                        $_year = $_date->format('Y');
                        $_week = (int)$_date->format('W');
                        $week_num = $_date->format('W');
                        if(count($staff_list) > 0){
                            foreach($staff_list as $ev){
                                if(($ev->date_employed != "0000-00-00" && $ev->status_id == 3)
                                    || ($ev->last_week_pay && $ev->last_week_pay >= $week_num)){
                                    $leave = $this->countLeaveTaken($ev->id,$dv);

                                    $ev->holiday_leave_taken = $leave['holiday_leave'];
                                    $ev->sick_leave_taken = $leave['sick_leave'];

                                    $ev->overall_holiday_leave = $this->getAnnualLeave($ev->id,$dv);
                                    $ev->overall_sick_leave = $this->getSickLeave($ev->id,$dv);

                                    $ev->total_holiday_leave = $this->getAnnualLeave($ev->id,$dv) - $ev->holiday_leave_taken;
                                    $ev->total_sick_leave = $this->getSickLeave($ev->id,$dv) - $ev->sick_leave_taken;

                                    $ev->start_use = '';
                                    /*$ev->kiwi = '';
                                    $ev->emp_kiwi = '';
                                    $ev->cec_name = '';
                                    $ev->field_name = '';*/

                                    if(count(@$rate[$ev->id]) > 0){
                                        foreach(@$rate[$ev->id] as $used_date=>$val){
                                            if(strtotime($used_date) <= strtotime($dv) ||
                                                strtotime($used_date) <= strtotime(date('Y-m-d',strtotime('+6 days '.$dv)))){
                                                $ev->rate_name = $val->rate_name;
                                                $ev->rate_cost = $val->rate;
                                                $ev->start_use = $val->start_use;
                                            }
                                        }
                                    }

                                    /*if(count(@$kiwi_data[$ev->id]) > 0){
                                        foreach(@$kiwi_data[$ev->id] as $start_use=>$val){
                                            if(strtotime($start_use) <= strtotime($dv) ||
                                                strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$dv)))){
                                                $ev->kiwi = $val->kiwi;
                                                $ev->employer_kiwi = $val->employer_kiwi;
                                                $ev->field_name = $val->field_name;
                                                $ev->esct_rate = $val->esct_rate;
                                            }
                                        }
                                    }*/

                                    $ev->hourly_rate = 0;
                                    if(count(@$hourly_rate[$ev->id]) > 0){
                                        foreach(@$hourly_rate[$ev->id] as $used_date=>$val){
                                            //$ev->hourly_rate = $val->hourly_rate;
                                            if(strtotime($used_date) <= strtotime($dv) ||
                                                strtotime($used_date) <= strtotime(date('Y-m-d',strtotime('+6 days '.$dv)))){
                                                $ev->hourly_rate = $val->hourly_rate;
                                            }
                                        }
                                    }

                                    $this->data['balance'][$ev->id] = array(
                                        'balance' => $ev->balance,
                                        'flight_debt' => $ev->flight_debt,
                                        'visa_debt' => $ev->visa_debt
                                    );
                                    $code = $ev->currency_code != 'NZD' ? $ev->currency_code : 'PHP';
                                    $symbols = $ev->currency_code != 'NZD' ? $ev->symbols : '₱';

                                    $converted_amount = $ev->currency_code != 'NZD' ? 1 : $phpCurrency;

                                    $ev->tax = 0;

                                    $hours = $ev->wage_type != 1 ? $this->getTotalHours($dv,$ev->id) : 1;
                                    $ev->gross = $hours ? $ev->rate_cost * $hours : 0;
                                    $ev->gross_ = number_format($ev->gross,2,'.','');
                                    //$ev->gross = $ev->gross != 0 ? number_format($ev->gross,0,'.',''):'0.000';

                                    $ev->kiwi_ = 0;
                                    $ev->st_loan = 0;
                                    $ev->emp_kiwi_ = 0;
                                    $ev->cec = 0;
                                    $ev->esct = 0;
                                    $kiwi = $ev->kiwi ? 'kiwi_saver_'.$ev->kiwi : '';
                                    $emp_kiwi = $ev->emp_kiwi ? 'kiwi_saver_'.$ev->emp_kiwi : '';
                                    $cec = $ev->cec_name ? $ev->cec_name : '';
                                    $esct = $ev->field_name ? $ev->field_name : '';

                                    $data_ = $this->getPayeValue($ev->field_code,$ev->frequency_id,$dv,$ev->gross,$kiwi,$emp_kiwi,$cec,$esct);

                                    if(count($data_) > 0){
                                        $ev->tax = number_format($data_['tax'],2,'.','');
                                        $ev->m_paye = $data_['m_paye'];
                                        $ev->me_paye = $data_['me_paye'];
                                        $ev->kiwi_ = $kiwi ? $data_['kiwi'] : 0;
                                        $ev->st_loan = $ev->has_st_loan ? $data_['st_loan'] : 0;
                                        $ev->emp_kiwi_ = $emp_kiwi ? $data_['emp_kiwi'] : 0;
                                        $ev->cec = $cec ? $data_['cec'] : 0;
                                        $ev->esct = $esct ? $data_['esct'] : 0;
                                    }

                                    $ev->nz_account_ = $ev->nz_account ? $ev->nz_account + ($hours * $ev->hourly_rate) : 0;

                                    $ev->flight_debt = @$this->data['total_bal'][$ev->id][$_year][$_week]['flight_debt'];
                                    $ev->flight_deduct = $ev->flight_debt > 0 ?
                                        ($ev->flight_debt <= $ev->flight_deduct ? $ev->flight_debt : $ev->flight_deduct) : 0;

                                    $ev->visa_debt = @$this->data['total_bal'][$ev->id][$_year][$_week]['visa_debt'];
                                    $ev->visa_deduct = $ev->visa_debt > 0 ?
                                        ($ev->visa_debt <= $ev->visa_deduct ? $ev->visa_debt : $ev->visa_deduct) : 0;

                                    $ev->balance = @$this->data['total_bal'][$ev->id][$_year][$_week]['balance'];
                                    $ev->installment = $ev->balance > 0 ?
                                        ($ev->balance <= $ev->installment ? $ev->balance : $ev->installment) : 0;

                                    $ev->recruit = $ev->visa_deduct ? $ev->gross * 0.03 : 0;
                                    $ev->admin = $ev->visa_deduct ? $ev->gross * 0.01 : 0;

                                    $ev->nett = $ev->gross_ - ($ev->st_loan + $ev->kiwi_ + $ev->tax + $ev->flight_deduct + $ev->visa_deduct + $ev->accommodation + $ev->transport + $ev->recruit + $ev->admin);

                                    $ev->distribution = $ev->nett - $ev->installment;
                                    $ev->account_one = $ev->distribution - ($ev->nz_account_ + $ev->account_two);
                                    $wage_data = array(
                                        'id' => $ev->id,
                                        'name' => $ev->name,
                                        'ird_num' => $ev->ird_num,
                                        'tax_number' => $ev->tax_number,
                                        'tax_code' => $ev->tax_code,
                                        'gross' => $ev->gross_,
                                        'rate_cost' => $ev->rate_cost,
                                        'hours' => $ev->wage_type != 1 ? $hours : 40,
                                        'tax' => $ev->tax != 0 ? $ev->tax : 0,
                                        'flight' => $ev->flight_deduct,
                                        'visa' => $ev->visa_deduct,
                                        'accommodation' => $ev->accommodation != '' ? $ev->accommodation: 0,
                                        'transport' => $ev->transport ? $ev->transport : 0,
                                        'deduction' => $ev->installment != '' || $ev->installment != 0 ? $ev->installment : 0,
                                        'distribution' => $ev->distribution,
                                        'date' => $dv,
                                        'nett' => number_format($ev->nett,2,'.',''),
                                        'recruit' => $ev->visa_deduct ? number_format($ev->recruit,2,'.','') : 0,
                                        'admin' => $ev->visa_deduct ? number_format($ev->admin,2,'.','') : 0,
                                        'currency' => $code,
                                        'account_two' => $hours > 0 ? ($ev->nz_account ? $ev->account_two : 0) : 0,
                                        'account_one' => $hours > 0 ? ($ev->nz_account ? $ev->account_one : 0) : 0,
                                        'nz_account' => $hours > 0 ? $ev->nz_account_ : 0,
                                        'has_nz_account' => $ev->nz_account ? 1 : 0,
                                        'rate_value' => $converted_amount,
                                        'week' => $this->getWeeks($dv),
                                        'start_use' => $ev->start_use,
                                        'wage_type' => $ev->wage_type,
                                        'symbols' => $symbols,
                                        'installment' => $ev->installment,
                                        'email' => $ev->email,
                                        'total_sick_leave' => $ev->total_sick_leave,
                                        'total_holiday_leave' => $ev->total_holiday_leave,
                                        'holiday_leave' => $ev->holiday_leave_taken,
                                        'sick_leave' => $ev->sick_leave_taken,
                                        'overall_holiday_leave' => $ev->overall_holiday_leave,
                                        'overall_sick_leave' => $ev->overall_sick_leave,
                                        'kiwi' => $ev->kiwi_,
                                        'has_kiwi' => $ev->kiwi ? 1 : 0,
                                        'emp_kiwi' => $ev->emp_kiwi_,
                                        'cec' => $ev->cec,
                                        'esct' => $ev->esct,
                                        'st_loan' => $ev->st_loan,
                                        'has_st_loan' => $ev->has_st_loan
                                    );

                                    $this->data['wage_data'][$dv][] = $wage_data;

                                    $this->data['last_week'] = $this->getWeeks(date('Y-m-t',strtotime('-1 week '.$dv)));
                                    $this->data['start_week'] = $this->getWeekNumberOfDateInYear($ev->start_use);
                                }
                            }
                        }
                    }
                }
                break;
            case 'monthly':
                $this->data['monthly_pay'] = array();
                $date = $this->getFirstNextLastDay($year,$month);

                $phpCurrency = CurrencyConverter('PHP');
                if(count($staff_list)>0){
                    foreach($staff_list as $mv){
                        if(count($date) >0){
                            foreach($date as $dv){

                                $_date = new DateTime($dv);
                                $_year = $_date->format('Y');
                                $_week = (int)$_date->format('W');

                                $monthly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->id) : 1;
                                $weekly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->id) : 1;

                                if($monthly_hours != 0){
                                    $mv->start_use = '';
                                    if(count(@$rate[$mv->id]) > 0){
                                        foreach(@$rate[$mv->id] as $used_date=>$val){
                                            if(strtotime($used_date) <= strtotime($dv) ||
                                                strtotime($used_date) <= strtotime(date('Y-m-d',strtotime('+6 days '.$dv)))){
                                                $mv->rate_name = $val->rate_name;
                                                $mv->rate_cost = $val->rate;
                                                $mv->start_use = $val->start_use;
                                            }
                                        }
                                    }

                                    $mv->hourly_rate = 0;
                                    if(count(@$hourly_rate[$mv->id]) > 0){
                                        foreach(@$hourly_rate[$mv->id] as $used_date=>$val){
                                            if(strtotime($used_date) <= strtotime($dv) ||
                                                strtotime($used_date) <= strtotime(date('Y-m-d',strtotime('+6 days '.$dv)))){
                                                $mv->hourly_rate = $val->hourly_rate;
                                            }
                                        }
                                    }

                                    /*$mv->kiwi = '';
                                    $mv->emp_kiwi = '';
                                    $mv->cec_name = '';
                                    $mv->field_name = '';

                                    if(count(@$kiwi_data[$mv->id]) > 0){
                                        foreach(@$kiwi_data[$mv->id] as $start_use=>$val){
                                            if(strtotime($start_use) <= strtotime($dv) ||
                                                strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$dv)))){
                                                $mv->kiwi = $val->kiwi;
                                                $mv->employer_kiwi = $val->employer_kiwi;
                                                $mv->field_name = $val->field_name;
                                                $mv->esct_rate = $val->esct_rate;
                                            }
                                        }
                                    }*/

                                    $mv->hours = $monthly_hours;
                                    $mv->gross_ = number_format($mv->hours * $mv->rate_cost,2,'.','');
                                    $mv->gross = $mv->hours * $mv->rate_cost;
                                    $mv->weekly_gross = number_format($weekly_hours * $mv->rate_cost,0,'.','');

                                    $mv->nz_account_ = $mv->nz_account ? ($mv->nz_account + ($mv->hours * $mv->hourly_rate)) : 0;

                                    $mv->flight_debt = @$this->data['total_bal'][$mv->id][$_year][$_week]['flight_debt'];
                                    $mv->flight = $mv->flight_debt > 0 ?
                                        ($mv->flight_debt <= $mv->flight_deduct ? $mv->flight_debt : $mv->flight_deduct) : 0;

                                    $mv->visa_debt = @$this->data['total_bal'][$mv->id][$_year][$_week]['visa_debt'];
                                    $mv->visa = $mv->visa_debt > 0 ?
                                        ($mv->visa_debt <= $mv->visa_deduct ? $mv->visa_debt : $mv->visa_deduct) : 0;

                                    $mv->balance = @$this->data['total_bal'][$mv->id][$_year][$_week]['balance'];
                                    $mv->installment = $mv->balance > 0 ?
                                        ($mv->balance <= $mv->installment ? $mv->balance : $mv->installment) : 0;

                                    $mv->recruit = $mv->visa ? $mv->gross * 0.03 : 0;
                                    $mv->admin = $mv->visa ? $mv->gross * 0.01 : 0;

                                    $code = $mv->currency_code != 'NZD' ? $mv->currency_code : 'PHP';
                                    $symbols = $mv->currency_code != 'NZD' ? $mv->symbols : '₱';

                                    $converted_amount = $mv->currency_code != 'NZD' ? 1 : $phpCurrency;

                                    $mv->kiwi_ = 0;
                                    $mv->st_loan = 0;
                                    $mv->emp_kiwi_ = 0;
                                    $mv->cec = 0;
                                    $mv->esct = 0;
                                    $kiwi = $mv->kiwi ? 'kiwi_saver_'.$mv->kiwi : '';
                                    $emp_kiwi = $mv->emp_kiwi ? 'kiwi_saver_'.$mv->emp_kiwi : '';
                                    $cec = $mv->cec_name ? $mv->cec_name : '';
                                    $esct = $mv->field_name ? $mv->field_name : '';
                                    $data_ = $this->getPayeValue($mv->field_code,$mv->frequency_id,$dv,$mv->gross,$kiwi,$emp_kiwi,$cec,$esct);
                                    if(count($data_) > 0){
                                        $mv->tax = $data_['tax'];
                                        $mv->m_paye = $data_['m_paye'];
                                        $mv->me_paye = $data_['me_paye'];
                                        $mv->kiwi_ = $kiwi ? $data_['kiwi'] : 0;
                                        $mv->st_loan = $mv->has_st_loan ? $data_['st_loan'] : 0;
                                        $mv->emp_kiwi_ = $emp_kiwi ? $data_['emp_kiwi'] : 0;
                                        $mv->cec = $cec ? $data_['cec'] : 0;
                                        $mv->esct = $esct ? $data_['esct'] : 0;
                                    }

                                    @$mv->tax_total += $mv->tax;
                                    @$mv->total_hours += $mv->hours;
                                    @$mv->total_gross += floatval($mv->gross_);
                                    @$mv->total_install += $mv->installment;
                                    @$mv->total_accom += $mv->accommodation;
                                    @$mv->total_trans += $mv->transport;
                                    @$mv->total_visa += $mv->visa;
                                    @$mv->total_flight += $mv->flight;
                                    @$mv->total_account_two += $mv->account_two;
                                    @$mv->total_nz_account += $mv->nz_account_;
                                    @$mv->total_kiwi += $mv->kiwi_;
                                    @$mv->total_emp_kiwi += $mv->emp_kiwi_;
                                    @$mv->total_st_loan += $mv->st_loan;
                                    @$mv->total_cec += $mv->cec;
                                    @$mv->total_esct += $mv->esct;

                                    $this->data['monthly_pay'][$mv->id] = array(
                                        'staff_id' => $mv->id,
                                        'symbols' => $symbols,
                                        'code' => $code,
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
                                        'tax' => $mv->tax_total,
                                        'hours' => $mv->wage_type != 1 ? number_format($mv->total_hours,2) : number_format(40,2),
                                        'gross' => $mv->total_gross,
                                        'recruit' => $mv->recruit,
                                        'admin' => $mv->admin,
                                        'total_tax' => $mv->tax_total,
                                        'weekly_gross' => $mv->weekly_gross,
                                        'm_paye' => $mv->m_paye,
                                        'me_paye' => $mv->me_paye,
                                        'kiwi' => $mv->total_kiwi,
                                        'emp_kiwi' => $mv->total_emp_kiwi,
                                        'has_st_loan' => $mv->total_st_loan,
                                        'cec' => $mv->total_cec,
                                        'esct' => $mv->total_esct,
                                        'st_loan' => $mv->has_st_loan,
                                        'rate_value' => $converted_amount
                                    );
                                }
                            }
                        }
                    }
                }
                $whatVal = array(false,3,1);
                $whatFld = array('is_unemployed','status_id','project_id');
                $this->data['staff'] = $this->my_model->getinfo('tbl_staff',$whatVal,$whatFld);
                break;
            default:
                break;
        }
    }

    function getPaySlipData($id,$date){
        $wage_data = new Wage_Controller();
        $staff_data = new Staff_Helper();

        $data['staff'] = $staff_data->staff_details(array($id),array('tbl_staff.id'));

        $this->data['total_bal'] = $wage_data->get_year_total_balance($id);
        $data['staff_name'] = '';
        $data['has_email'] = 0;
        $phpCurrency = CurrencyConverter('PHP');

        $_date = new DateTime($date);
        $_year = $_date->format('Y');
        $_week = (int)$_date->format('W');
        $rate = $staff_data->staff_rate();
        $hourly_rate = $staff_data->staff_hourly_rate();
        //$kiwi_data = $staff_data->staff_kiwi();

        if(count($data['staff'])>0){
            foreach($data['staff'] as $v){
                $data['staff_name'] = $v->name;
                $data['has_email'] = $v->email ? ($v->is_email_payslip ? 1 : 2) : 0;
                $v->hours = $v->wage_type != 1 ? $this->getTotalHours($date,$v->id) : 1;
                $v->working_hours = $v->wage_type != 1 ? $this->getWorkingHours($date,$v->id) : 1;
                $v->non_working_hours = $v->wage_type != 1 ? $this->getWorkingHours($date,$v->id,2) : 1;
                $v->code = $v->currency_code != 'NZD' ? $v->currency_code : 'PHP';
                $v->currency_symbols = $v->symbols;
                $v->symbols = $v->currency_code != 'NZD' ? $v->symbols : 'Php';

                if(count(@$rate[$v->id]) > 0){
                    foreach(@$rate[$v->id] as $start_date=>$val){
                        if(strtotime($start_date) <= strtotime($date) ||
                            strtotime($start_date) <= strtotime(date('Y-m-d',strtotime('+6 days '.$date)))){
                            $v->rate_name = $val->rate_name;
                            $v->rate_cost = $val->rate;
                            $v->start_use = $val->start_use;
                        }
                    }
                }

                $v->hourly_rate = 0;
                if(count(@$hourly_rate[$v->id]) > 0){
                    foreach(@$hourly_rate[$v->id] as $used_date=>$val){
                        $v->hourly_rate = $val->hourly_rate;
                        if(strtotime($used_date) <= strtotime($date) ||
                            strtotime($used_date) <= strtotime(date('Y-m-d',strtotime('+6 days '.$date)))){
                            $v->hourly_rate = $val->hourly_rate;
                        }
                    }
                }

                /*$v->kiwi = '';
                $v->emp_kiwi = '';
                $v->cec_name = '';
                $v->field_name = '';

                if(count(@$kiwi_data[$v->id]) > 0){
                    foreach(@$kiwi_data[$v->id] as $start_use=>$val){
                        if(strtotime($start_use) <= strtotime($date) ||
                            strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$date)))){
                            $v->kiwi = $val->kiwi;
                            $v->employer_kiwi = $val->employer_kiwi;
                            $v->field_name = $val->field_name;
                            $v->esct_rate = $val->esct_rate;
                        }
                    }
                }*/

                $converted_amount = $v->currency_code != 'NZD' ? 1 : $phpCurrency;
                $v->converted_amount = $converted_amount;
                $v->nz_account_ = $v->nz_account ? ($v->nz_account + ($v->hours * $v->hourly_rate)) : 0;
                $v->gross = $v->rate_cost * $v->hours;
                $v->gross_ = $v->gross != 0 ? number_format($v->gross,2,'.',''):'0.00';
                //$v->gross = $v->gross != 0 ? number_format($v->gross,0,'.',''):'0.00';

                $v->flight_debt = @$this->data['total_bal'][$v->id][$_year][$_week]['flight_debt'];
                $v->flight = $v->flight_debt > 0 ? $v->flight : 0;

                $v->visa_debt = @$this->data['total_bal'][$v->id][$_year][$_week]['visa_debt'];
                $v->visa = $v->visa_debt > 0 ? $v->visa : 0;

                $v->balance_ = @$this->data['total_bal'][$v->id][$_year][$_week]['balance'];
                $v->installment = $v->balance_ > 0 ? $v->installment : 0;

                $v->tax = 0;
                $v->kiwi_ = 0;
                $v->st_loan = 0;
                $kiwi = $v->kiwi ? 'kiwi_saver_'.$v->kiwi : '';

                $data_ = $this->getPayeValue($v->field_code,$v->frequency_id,$date,$v->gross,$kiwi);
                if(count($data_) > 0){
                    $v->tax = $data_['tax'];
                    $v->m_paye = $data_['m_paye'];
                    $v->me_paye = $data_['me_paye'];
                    $v->kiwi_ = $kiwi ? $data_['kiwi'] : 0;
                    $v->st_loan = $v->has_st_loan ? $data_['st_loan'] : 0;
                }


                @$v->tax_total += $v->tax;
                @$v->total_install += $v->installment;
                @$v->total_flight += $v->flight;
                @$v->total_visa += $v->visa;
                @$v->total_accom += $v->accommodation;
                @$v->total_trans += $v->transport;
                @$v->total_account_two += $v->account_two;
                @$v->total_nz_account += $v->nz_account_;
                @$v->total_kiwi += $v->kiwi_;

                $v->recruit = $v->visa ? $v->gross * 0.03 : 0;
                $v->admin = $v->visa ? $v->gross * 0.01 : 0;
                $v->net = $v->gross - ($v->st_loan + $v->kiwi_ + $v->tax + $v->flight + $v->recruit + $v->admin + $v->visa + $v->accommodation + $v->transport);
                $v->total = $v->total_kiwi + $v->tax_total + $v->total_install + $v->recruit + $v->admin + $v->total_flight + $v->total_visa + $v->total_accom + $v->total_trans;
                $v->distribution = $v->net - $v->installment;
                $v->account_one = $v->distribution - ($v->nz_account_ + $v->account_two);

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
        $date_ = $year.'-'.$month.'-01';
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_rate',
                'tbl_tax_codes',
                'tbl_wage_type',
                'tbl_staff_rate',
                'tbl_kiwi as employee',
                'tbl_kiwi as employeer',
                'tbl_esct_rate',
            ),
            'join_field' => array('id','id','id','staff_id','id','id','id'),
            'source_field' => array(
                'tbl_staff.rate',
                'tbl_staff.tax_code_id',
                'tbl_staff.wage_type',
                'tbl_staff.id',
                'tbl_staff.kiwi_id',
                'tbl_staff.employeer_kiwi',
                'tbl_staff.esct_rate_id',
            ),
            'type' => 'left',
            'join_append' => array(
                'tbl_rate',
                'tbl_tax_codes',
                'tbl_wage_type',
                'tbl_staff_rate',
                'employee',
                'employeer',
                'tbl_esct_rate',
            )
        ));
        $this->my_model->setSelectFields(array(
            'tbl_staff.id',
            'tbl_staff.fname',
            'tbl_staff.lname',
            'tbl_rate.rate_cost',
            'IF(tbl_tax_codes.tax_code != "" , tbl_tax_codes.tax_code, "") as tax_code',
            'IF(tbl_staff.ird_num != "-" ,LPAD(tbl_staff.ird_num,11,"0"),tbl_staff.ird_num) as ird_num',
            'tbl_wage_type.type as wage_type',
            'tbl_wage_type.frequency as frequency_id',
            'tbl_esct_rate.field_name',
            'tbl_esct_rate.cec_name',
            'tbl_esct_rate.cec_name',
            'employee.kiwi',
            'employeer.kiwi as emp_kiwi',
            'tbl_tax_codes.has_st_loan',
            'tbl_tax_codes.field_code_name as field_code',
            'IF(tbl_staff.date_employed != "0000-00-00" ,DATE_FORMAT(tbl_staff.date_employed,"%d-%m-%Y"),"") as date_employed'
        ),false);
        $this->my_model->setGroupBy('id');
        $whatVal = 'tbl_staff_rate.start_use <= "'.$date_.'" AND tbl_staff.is_unemployed !=1 AND project_id ="1"';
        $this->my_model->setOrder(array('lname','fname'));
        $this->data['staff'] = $this->my_model->getinfo('tbl_staff',$whatVal,'');
        $staff_data = new Staff_Helper();
        $rate = $staff_data->staff_rate();
        //$kiwi_data = $staff_data->staff_kiwi();
        if(count($date) >0){
            foreach($date as $dv){
                if(count($this->data['staff'])>0){
                    foreach($this->data['staff'] as $mv){
                        $monthly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->id) : 1;
                        $mv->total_hours = 0;
                        $mv->total_gross = 0;
                        $mv->total_tax = 0;
                        $mv->total_st_loan = 0;
                        $mv->total_emp_kiwi = 0;
                        $mv->total_kiwi = 0;
                        $mv->total_esct = 0;
                        $mv->total_cec = 0;

                        if(count(@$rate[$mv->id]) > 0){
                            foreach(@$rate[$mv->id] as $start_use=>$val){
                                if(strtotime($start_use) <= strtotime($dv) ||
                                    strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$dv)))){
                                    $mv->rate_name = $val->rate_name;
                                    $mv->rate_cost = $val->rate;
                                }
                            }
                        }

                        /*$mv->kiwi = '';
                        $mv->emp_kiwi = '';
                        $mv->cec_name = '';
                        $mv->field_name = '';

                        if(count(@$kiwi_data[$mv->id]) > 0){
                            foreach(@$kiwi_data[$mv->id] as $start_use=>$val){
                                if(strtotime($start_use) <= strtotime($date) ||
                                    strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$dv)))){
                                    $mv->kiwi = $val->kiwi;
                                    $mv->employer_kiwi = $val->employer_kiwi;
                                    $mv->field_name = $val->field_name;
                                    $mv->esct_rate = $val->esct_rate;
                                }
                            }
                        }*/

                        if($monthly_hours != 0){
                            $mv->hours = $monthly_hours;
                            $mv->gross_ = number_format($mv->hours * $mv->rate_cost,2,'.','');
                            $mv->gross = $mv->hours * $mv->rate_cost;
                            $mv->total_hours += $mv->hours;
                            $mv->total_gross += floatval($mv->gross_);
                            if(!$mv->gross){
                                $mv->tax = 0;
                            }else{
                                $mv->tax = 0;
                                $mv->m_paye = 0;
                                $mv->me_paye = 0;
                                $mv->kiwi_ = 0;
                                $mv->emp_kiwi_ = 0;
                                $mv->st_loan = 0;
                                $mv->esct = 0;
                                $mv->cec = 0;
                                $kiwi = $mv->kiwi ? 'kiwi_saver_'.$mv->kiwi : '';
                                $emp_kiwi = $mv->emp_kiwi ? 'kiwi_saver_'.$mv->emp_kiwi : '';
                                $cec = $mv->cec_name ? $mv->cec_name : '';
                                $esct = $mv->field_name ? $mv->field_name : '';
                                $data_ = $this->getPayeValue($mv->field_code,$mv->frequency_id,$dv,$mv->gross,$kiwi,$emp_kiwi,$cec,$esct);
                                if(count($data_) > 0){
                                    $mv->tax = $data_['tax'];
                                    $mv->m_paye = $data_['m_paye'];
                                    $mv->me_paye = $data_['me_paye'];
                                    $mv->kiwi_ = $kiwi ? $data_['kiwi'] : 0;
                                    $mv->emp_kiwi_ = $emp_kiwi ? $data_['emp_kiwi'] : 0;
                                    $mv->st_loan = $mv->has_st_loan ? $data_['st_loan'] : 0;
                                    $mv->esct = $esct ? $data_['esct'] : 0;
                                    $mv->cec = $cec ? $data_['cec'] : 0;
                                }
                            }

                            $mv->total_tax += $mv->tax;
                            $mv->total_st_loan += $mv->st_loan;
                            $mv->total_kiwi += $mv->kiwi_;
                            $mv->total_emp_kiwi += $mv->emp_kiwi_;
                            $mv->total_esct += $mv->esct;
                            $mv->total_cec += $mv->cec;

                            $this->data['monthly_pay'][$mv->id] = array(
                                'id' => $mv->id,
                                'rate_cost' => $mv->rate_cost,
                                'tax' => $mv->total_tax,
                                'hours' => $mv->total_hours,
                                'gross' => $mv->total_gross,
                                'm_paye' => $mv->m_paye,
                                'me_paye' => $mv->me_paye,
                                'kiwi' => $mv->total_kiwi,
                                'emp_kiwi' => $mv->total_emp_kiwi,
                                'st_loan' => $mv->total_st_loan,
                                'esct' => $mv->total_esct,
                                'cec' => $mv->total_cec,
                            );
                        }
                    }
                }
            }
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
        }else if(isset($_GET['read_all']) && $_GET['read_all'] == 1){
            $this->data['page_name'] = 'Read All Archived Messages';
            $this->data['page_load'] = 'backend/notification/notification_read_all_view';
            $this->data['notification'] = $this->getInvoiceNotification(3);
            $this->load->view('main_view',$this->data);
        }else{
            if(isset($_GET['active'])){
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
        $fields = ArrayWalk(
            array(
                'id','your_ref','job_id','meter','date','job_name','is_archive','is_open','is_new'
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
                    $this->my_model->setOrder(array('is_archive','is_open','is_new'));
                    $whatVal = array(true,false,false);
                    $whatFld = array('tbl_invoice.is_new','tbl_invoice.is_open','tbl_invoice.is_archive');
                    break;
                case 2:
                    $this->my_model->setOrder(array('is_archive','is_open','is_new'));
                    $whatVal = '(tbl_invoice.is_new = 1 OR tbl_invoice.is_open = 1) AND tbl_invoice.is_archive = 0';
                    $whatFld = '';
                    break;
                case 3:
                    $this->my_model->setOrder('date','DESC');
                    $whatVal = array(true,true,true);
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

    function countLeaveTaken($id,$date){
        $date = date('Y-m-d',strtotime($date));

        $this->my_model->setJoin(array(
            'table' => array('tbl_holiday_type as sick_leave','tbl_holiday_type as holiday_leave'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_login_sheet.sick_leave_type_id','tbl_login_sheet.holiday_type_id'),
            'type' => 'left',
            'join_append' => array(
                'sick_leave','holiday_leave'
            )
        ));
        $this->my_model->setSelectFields(array('sick_leave.day_number as sick_leave','holiday_leave.day_number as holiday_leave'));
        $whatVal = 'date <= "' . $date . '" AND staff_id ="' . $id . '"';
        $leave_data = $this->my_model->getInfo('tbl_login_sheet',$whatVal,'');
        $sick_leave = 0;
        $holiday_leave = 0;

        if(count($leave_data) > 0){
            foreach($leave_data as $val){
                $sick_leave += $val->sick_leave;
                $holiday_leave += $val->holiday_leave;
            }
        }

        $leave = array(
            'sick_leave' => $sick_leave,
            'holiday_leave' => $holiday_leave
        );

        return $leave;
    }

    function getStaffFortnightlyHours($id, $year = 2015,$start_pay_week = 27,$start_year = 2015,$size = 2){
        $_date = new DateTime();
        $start_week = $start_pay_week - 1;
        $what_week_date = $_date->setISODate($start_year, $start_week)->format('Y-m-d');
        $weeks_in_year = $this->getWeekInYearBetweenDates($year + 1);

        $date_array = array();


        if(count($weeks_in_year) > 0){
            foreach($weeks_in_year as $week=>$date){
                $start_date = strtotime($what_week_date);
                $week_date = strtotime($date);

                if($week_date >= $start_date){
                    $date_array[$week] = $date;
                }
            }
        }
        $fortnightly = array_chunk($date_array,$size);

        $staff_hours = array();
        $total_hours_ = 0;
        if(count($fortnightly) > 0){
            foreach($fortnightly as $week_date){
                if(count($week_date) > 0){
                    foreach($week_date as $key=>$week){
                        $hours = floatval($this->getTotalHours($week,$id));
                        $total_hours_ += $hours;
                        if($key == 1){
                            $staff_hours[$id][$week] = $total_hours_;
                            $total_hours_ = 0;
                        }
                    }
                }
            }
        }
        return $staff_hours;
    }

    function payPeriodSentEmail($date,$range,$msg,$send = true){
        $has_pay_setup = $this->my_model->getInfo('tbl_pay_setup');

        $debugResult = array();

        if(count($has_pay_setup) > 0){
            foreach($has_pay_setup as $pay_setup){
                $date = date('Y-m-d',strtotime($date));

                $this->my_model->setShift();
                $whatVal = array($date,'report');
                $whatFld = array('date','type');
                $pdf_file = (Object)$this->my_model->getInfo('tbl_pdf_archive',$whatVal,$whatFld);

                $cc = array(
                    $pay_setup->director_email,
                    $pay_setup->enderly_email
                );
                $cc_alias = array(
                    $pay_setup->director_name,
                    $pay_setup->enderly_name
                );
                $dir = realpath(APPPATH.'../pdf');;
                $url = $dir.'/pay period/'.$range.'/'.$pdf_file->file_name;

                $sendMailSetting = array(
                    'to' => $pay_setup->accountant_email,
                    'to_alias' => $pay_setup->accountant_name,
                    'cc' => $cc,
                    'cc_alias' => $cc_alias,
                    'from' => 'no-reply@subbiesolutions.co.nz',
                    'name' => 'Subbie Solutions Administrator',
                    'subject' => 'Pay Period Summary Report from ' . date('j F Y',strtotime($date)) .' to '.date('j F Y',strtotime('+6 days '.$date)),
                    'url' => $url,
                    'file_names' => $pdf_file->file_name,
                    'debug_type' => 2,
                    'debug' => true
                );

                if($send){
                    $email_send = new Send_Email_Controller();
                    $debugResult['result'] = $email_send->sendingEmail(
                        $msg,
                        $sendMailSetting
                    );
                }else{
                    $debugResult['result'] = (Object)array(
                        'type' => 2,
                        'debug' => 'Email needs to be review before sending'
                    );
                }
                $debugResult['is_send'] = $send;
                $debugResult['mail_settings'] = $sendMailSetting;
            }
        }

        return $debugResult;
    }

    function sentAllStaffPaySlip($this_week,$this_date,$send = true,$staff_id = '',$email_id = ''){

        if(!$this_date && !$this_week){
            exit;
        }

        $this->my_model->setSelectFields(array('CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name','email','id'));
        if($staff_id){
            $whatVal = $staff_id;
            $whatFld = 'tbl_staff.id';
        }else{
            $whatVal = 'project_id = "1" AND is_email_payslip ="1" AND (date_employed != "0000-00-00" AND status_id = "3") OR (last_week_pay >= "' . $this_week . '")';
            $whatFld = '';
        }
        $staff = $this->my_model->getInfo('tbl_staff',$whatVal,$whatFld);

        if($staff_id){
            $whatVal = array($staff_id,$this_date);
            $whatFld = array('staff_id','date');
        }else{
            $whatVal = array($this_date);
            $whatFld = array('date');
        }

        $filename = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,$whatFld);
        $file = array();

        if(count($filename) > 0){
            foreach($filename as $fv){
                $file[$fv->staff_id] = $fv->file_name;
            }
        }

        $has_pay_setup = $this->my_model->getInfo('tbl_pay_setup');

        $bcc = array();
        $bcc_alias = array();

        if(count($has_pay_setup) > 0) {
            foreach ($has_pay_setup as $pay_setup) {
                $bcc       = array(
                    $pay_setup->director_email,
                    $pay_setup->accountant_email,
                    $pay_setup->enderly_email
                );
                $bcc_alias = array(
                    $pay_setup->director_name,
                    $pay_setup->accountant_name,
                    $pay_setup->enderly_name
                );
            }
        }

        if(count($staff) > 0){
            foreach($staff as $v){
                $this->data['email'] = $v->email;
                //$staff_name = $v->name;

                $dir = realpath(APPPATH.'../pdf');
                $path = 'payslip/'.date('Y/F',strtotime($this_date));
                $_path = $dir.'/'.$path.'/'.@$file[$v->id];
                if(file_exists($_path)) {
                    $this->data['dir'] = $dir . '/' . $path;
                    //$file_to_save      = $this->data['dir'] . '/' . $filename;
                    //save the pdf file on the server

                    $sendMailSetting = array(
                        'to' => $v->email,
                        'to_alias' => $v->name,
                        'name' => 'Subbie Solutions Administrator',
                        'from' => 'no-reply@subbiesolutions.co.nz',
                        'subject' => 'Pay Slip for Week '. $this_week . ' (ending ' . date('d/m/Y', strtotime('+6 days '.$this_date)).')',
                        'bcc' => $bcc,
                        'bcc_alias' => $bcc_alias,
                        'url' => $_path,
                        'file_names' => $file[$v->id],
                        'debug_type' => 2,
                        'debug' => true
                    );
                    $msg = 'Attached please find your Pay Slip for Week '.$this_week . ' (Week Ending '.date('d/m/Y', strtotime('+6 days '.$this_date)).').<br/><br/>';
                    $msg .= 'Regards,<br/><br/>';
                    $msg .= 'Subbie Solutions Admin';
                    if($send){
                        $send_mail = new Send_Email_Controller();
                        $debugResult = $send_mail->sendingEmail(
                            $msg,
                            $sendMailSetting
                        );
                    }else{
                        $debugResult = (Object)array(
                            'type' => 2,
                            'debug' => 'Email needs to be review before sending'
                        );
                    }

                    $post = array(
                        'user_id' => $this->session->userdata('user_id'),
                        'week_number' => $this_week,
                        'pay_period' => $this_date,
                        'message' => json_encode($sendMailSetting),
                        'email_type_id' => 2,
                        'staff_id' => $v->id,
                        'type' => $debugResult->type,
                        'debug' => $debugResult->debug,
                        'date' => date('Y-m-d H:i:s')
                    );
                    if(!$email_id){
                        $this->my_model->insert('tbl_email_export_log', $post, false);
                    }else{
                        $this->my_model->update('tbl_email_export_log', $post,$email_id,'id',false);
                    }
                }
            }
        }
    }

    function getPayeValue($code,$frequency_id,$date,$gross,$kiwi = '',$emp_kiwi='',$cec = '',$esct = '',$is_annual = false){
        $earnings = $this->data['earnings'];
        $m_paye = $this->data['m_paye'];
        $data = array();
        $data['tax'] = 0;
        $data['m_paye'] = 0;
        $data['me_paye'] = 0;
        $data['kiwi'] = 0;
        $data['st_loan'] = 0;
        $data['cec'] = 0;
        $data['esct'] = 0;
        $data['emp_kiwi'] = 0;

        if($gross){

            $is_decimal = $this->is_decimal($gross);

            switch($frequency_id){
                case 1:
                    if($gross > $earnings['weekly']){
                        $data['tax'] = ((floatval($gross) - $earnings['weekly']) * 0.33) + $m_paye['weekly'];
                    }else{
                        $_gross = number_format($gross,0,'.','');

                        if($is_decimal){
                            $h_gross = $is_annual ? (int)$_gross + 1 : $_gross;
                            $l_gross = !$is_annual ? (int)$_gross - 1 : $_gross;
                            $lower_tax = 0;
                            $l_m_paye = 0;
                            $l_me_paye = 0;
                            $l_kiwi = 0;
                            $l_st_loan = 0;
                            $l_emp_kiwi = 0;
                            $l_cec = 0;
                            $l_esct = 0;

                            $higher_tax = 0;
                            $h_m_paye = 0;
                            $h_me_paye = 0;
                            $h_kiwi = 0;
                            $h_st_loan = 0;
                            $h_emp_kiwi = 0;
                            $h_cec = 0;
                            $h_esct = 0;


                            $whatVal = 'earnings ="'.$l_gross.'" AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';
                            $lower = $this->my_model->getinfo('tbl_tax',$whatVal,'');

                            if(count($lower) > 0){
                                foreach($lower as $paye){
                                    $lower_tax = $code ? $paye->$code : 0;
                                    $l_m_paye = $paye->m_paye;
                                    $l_me_paye = $paye->me_paye;
                                    $l_kiwi = $kiwi ? $paye->$kiwi : 0;
                                    $l_st_loan = $paye->sl_loan_ded;
                                    $l_cec = $cec ? $paye->$cec : 0;
                                    $l_esct = $esct ? $paye->$esct : 0;
                                    $l_emp_kiwi = $emp_kiwi ? $paye->$emp_kiwi : 0;
                                }
                            }

                            $whatVal = 'earnings ="'.$h_gross.'" AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';
                            $higher = $this->my_model->getinfo('tbl_tax',$whatVal,'');

                            if(count($higher) > 0){
                                foreach($higher as $paye){
                                    $higher_tax = $code ? $paye->$code : 0;
                                    $h_m_paye = $paye->m_paye;
                                    $h_me_paye = $paye->me_paye;
                                    $h_kiwi = $kiwi ? $paye->$kiwi : 0;
                                    $h_st_loan = $paye->sl_loan_ded;
                                    $h_cec = $cec ? $paye->$cec : 0;
                                    $h_esct = $esct ? $paye->$esct : 0;
                                    $h_emp_kiwi = $emp_kiwi ? $paye->$emp_kiwi : 0;
                                }
                            }

                            if($is_annual){
                                $data['tax'] = $lower_tax + (($higher_tax - $lower_tax)*0.16);
                                $data['m_paye'] = $l_m_paye + (($h_m_paye - $l_m_paye)*0.16);
                                $data['me_paye'] = $l_me_paye + (($h_me_paye - $l_me_paye)*0.16);
                                $data['kiwi'] = $l_kiwi + (($h_kiwi - $l_kiwi)*0.16);
                                $data['st_loan'] = $l_st_loan + (($h_st_loan - $l_st_loan)*0.16);
                                $data['cec'] = $l_cec + (($h_cec - $l_cec)*0.16);
                                $data['esct'] = $l_esct + (($h_esct - $l_esct)*0.16);
                                $data['emp_kiwi'] = $l_emp_kiwi + (($h_emp_kiwi - $l_emp_kiwi)*0.16);
                            }else{
                                $data['tax'] = $lower_tax + (($higher_tax - $lower_tax)/2);
                                $data['m_paye'] = $l_m_paye + (($h_m_paye - $l_m_paye)/2);
                                $data['me_paye'] = $l_me_paye + (($h_me_paye - $l_me_paye)/2);
                                $data['kiwi'] = $l_kiwi + (($h_kiwi - $l_kiwi)/2);
                                $data['st_loan'] = $l_st_loan + (($h_st_loan - $l_st_loan)/2);
                                $data['cec'] = $l_cec + (($h_cec - $l_cec)/2);
                                $data['esct'] = $l_esct + (($h_esct - $l_esct)/2);
                                $data['emp_kiwi'] = $l_emp_kiwi + (($h_emp_kiwi - $l_emp_kiwi)/2);
                            }
                        }
                        else{
                            $whatVal = 'earnings ="'.$_gross.'" AND (start_date <= "'.$date.'" AND end_date >= "'.$date.'") AND frequency_id="'.$frequency_id.'"';
                            $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                            if(count($tax)>0){
                                foreach($tax as $tv){
                                    $data['tax'] = $code ? $tv->$code : 0;
                                    $data['m_paye'] = $tv->m_paye;
                                    $data['me_paye'] = $tv->me_paye;
                                    $data['kiwi'] = $kiwi ? $tv->$kiwi : 0;
                                    $data['st_loan'] = $tv->sl_loan_ded;
                                    $data['cec'] = $cec ? $tv->$cec : 0;
                                    $data['esct'] = $esct ? $tv->$esct : 0;
                                    $data['emp_kiwi'] = $emp_kiwi ? $tv->$emp_kiwi : 0;
                                }
                            }
                        }
                    }
                    break;
                case 2:
                    if($gross > $earnings['fortnightly']){
                        $data['tax'] = ((floatval($gross) - $earnings['fortnightly']) * 0.33) + $m_paye['fortnightly'];
                    }else{
                        if($is_decimal){
                            $is_even = $gross % 2;
                            $_gross_val = number_format($gross,0,'.','');
                            $lower_tax = 0;
                            $l_m_paye = 0;
                            $l_me_paye = 0;
                            $l_kiwi = 0;
                            $l_st_loan = 0;
                            $l_emp_kiwi = 0;
                            $l_cec = 0;
                            $l_esct = 0;

                            $higher_tax = 0;
                            $h_m_paye = 0;
                            $h_me_paye = 0;
                            $h_kiwi = 0;
                            $h_st_loan = 0;
                            $h_emp_kiwi = 0;
                            $h_cec = 0;
                            $h_esct = 0;

                            if($is_even){
                                $whatVal = 'earnings = (SELECT MIN(earnings) FROM tbl_tax WHERE earnings > '.$_gross_val.') AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';

                                $lower = $this->my_model->getinfo('tbl_tax',$whatVal,'');

                                if(count($lower) > 0){
                                    foreach($lower as $paye){
                                        $lower_tax = $code ? $paye->$code : 0;
                                        $l_m_paye = $paye->m_paye;
                                        $l_me_paye = $paye->me_paye;
                                        $l_kiwi = $kiwi ? $paye->$kiwi : 0;
                                        $l_st_loan = $paye->sl_loan_ded;
                                        $l_cec = $cec ? $paye->$cec : 0;
                                        $l_esct = $esct ? $paye->$esct : 0;
                                        $l_emp_kiwi = $emp_kiwi ? $paye->$emp_kiwi : 0;
                                    }
                                }

                                $whatVal = 'earnings = (SELECT MAX(earnings) FROM tbl_tax WHERE earnings < '.$_gross_val.') AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';
                                $higher = $this->my_model->getinfo('tbl_tax',$whatVal,'');

                                if(count($higher) > 0){
                                    foreach($higher as $paye){
                                        $higher_tax = $code ? $paye->$code : 0;
                                        $h_m_paye = $paye->m_paye;
                                        $h_me_paye = $paye->me_paye;
                                        $h_kiwi = $kiwi ? $paye->$kiwi : 0;
                                        $h_st_loan = $paye->sl_loan_ded;
                                        $h_cec = $cec ? $paye->$cec : 0;
                                        $h_esct = $esct ? $paye->$esct : 0;
                                        $h_emp_kiwi = $emp_kiwi ? $paye->$emp_kiwi : 0;
                                    }
                                }

                            }else{
                                $whatVal = 'earnings = (SELECT MIN(earnings) FROM tbl_tax WHERE earnings > '.$_gross_val.') AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';
                                $lower = $this->my_model->getinfo('tbl_tax',$whatVal,'');

                                if(count($lower) > 0){
                                    foreach($lower as $paye){
                                        $lower_tax = $code ? $paye->$code : 0;
                                        $l_m_paye = $paye->m_paye;
                                        $l_me_paye = $paye->me_paye;
                                        $l_kiwi = $kiwi ? $paye->$kiwi : 0;
                                        $l_st_loan = $paye->sl_loan_ded;
                                        $l_cec = $cec ? $paye->$cec : 0;
                                        $l_esct = $esct ? $paye->$esct : 0;
                                        $l_emp_kiwi = $emp_kiwi ? $paye->$emp_kiwi : 0;
                                    }
                                }

                                $whatVal = 'earnings = (SELECT MAX(earnings) FROM tbl_tax WHERE earnings < '.$_gross_val.') AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';
                                $higher = $this->my_model->getinfo('tbl_tax',$whatVal,'');

                                if(count($higher) > 0){
                                    foreach($higher as $paye){
                                        $higher_tax = $code ? $paye->$code : 0;
                                        $h_m_paye = $paye->m_paye;
                                        $h_me_paye = $paye->me_paye;
                                        $h_kiwi = $kiwi ? $paye->$kiwi : 0;
                                        $h_st_loan = $paye->sl_loan_ded;
                                        $h_cec = $cec ? $paye->$cec : 0;
                                        $h_esct = $esct ? $paye->$esct : 0;
                                        $h_emp_kiwi = $emp_kiwi ? $paye->$emp_kiwi : 0;
                                    }
                                }
                            }

                            $data['tax'] = $lower_tax + (($higher_tax - $lower_tax)/2);
                            $data['m_paye'] = $l_m_paye + (($h_m_paye - $l_m_paye)/2);
                            $data['me_paye'] = $l_me_paye + (($h_me_paye - $l_me_paye)/2);
                            $data['kiwi'] = $l_kiwi + (($h_kiwi - $l_kiwi)/2);
                            $data['st_loan'] = $l_st_loan + (($h_st_loan - $l_st_loan)/2);
                            $data['cec'] = $l_cec + (($h_cec - $l_cec)/2);
                            $data['esct'] = $l_esct + (($h_esct - $l_esct)/2);
                            $data['emp_kiwi'] = $l_emp_kiwi + (($h_emp_kiwi - $l_emp_kiwi)/2);
                        }else{
                            $_gross_val = number_format($gross,0,'.','');
                            $whatVal = 'earnings ="'.$_gross_val.'" AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';
                            $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                            if(count($tax)>0){
                                foreach($tax as $tv){
                                    $data['tax'] = $code ? $tv->$code : 0;
                                    $data['m_paye'] = $tv->m_paye;
                                    $data['me_paye'] = $tv->me_paye;
                                    $data['kiwi'] = $kiwi ? $tv->$kiwi : 0;
                                    $data['st_loan'] = $tv->sl_loan_ded;
                                    $data['cec'] = $cec ? $tv->$cec : 0;
                                    $data['esct'] = $esct ? $tv->$esct : 0;
                                    $data['emp_kiwi'] = $emp_kiwi ? $tv->$emp_kiwi : 0;
                                }
                            }
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        return $data;
    }

    function getWageTypeHoursValue($staff_id,$wage_type,$frequency,$date){
        $year = date('Y',strtotime($date));
        switch($wage_type){
            case 1:
                $hours = 1;
                break;
            default:
                switch($frequency){
                    case 2:
                        $fortnightly = $this->getStaffFortnightlyHours($staff_id,$year);
                        $hours = @$fortnightly[$staff_id][$date] ? $fortnightly[$staff_id][$date] : 0;
                        break;
                    default:
                        $hours = $this->getTotalHours($date,$staff_id);
                        break;
                }
                break;
        }

        return $hours;
    }

    function getTotalStaffData($date,$is_count = false){
        $whatVal = 'start_use <= "' . $date . '" AND is_unemployed != "1"';
        $whatFld = '';
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_staff_rate',
                'tbl_wage_type',
                'tbl_deductions',
                'tbl_kiwi',
                'tbl_tax_codes'
            ),
            'join_field' => array('staff_id','id','staff_id','id','id'),
            'source_field' => array(
                'tbl_staff.id',
                'tbl_staff.wage_type',
                'tbl_staff.id',
                'tbl_staff.kiwi_id',
                'tbl_staff.tax_code_id'
            ),
            'type' => 'left'
        ));
        $this->my_model->setGroupBy('tbl_staff.id');
        $fld = ArrayWalk(
            $this->my_model->getFields('tbl_staff'),
            'tbl_staff.'
        );
        $fld[] = 'tbl_staff_rate.start_use';
        $fld[] = 'tbl_staff.has_loans';
        $fld[] = 'tbl_wage_type.frequency';
        $fld[] = 'tbl_deductions.transport';
        $fld[] = 'tbl_kiwi.kiwi';
        $fld[] = 'tbl_tax_codes.has_st_loan';
        $fld[] = 'tbl_tax_codes.field_code_name as field_code';

        $this->my_model->setSelectFields($fld);
        $this->my_model->setOrder(array('lname','fname'));
        $staff = $this->my_model->getInfo('tbl_staff',$whatVal,$whatFld);
        $data = array(
            'staff_count' => count($staff) > 0 ? count($staff) : 0
        );
        $wage_ = 0;
        $paye_ = 0;
        $wage_data = new Wage_Controller();
        $this->data['total_bal'] = $wage_data->get_year_total_balance();
        $staff_data = new Staff_Helper();
        $rate = $staff_data->staff_rate();
        //$kiwi_data = $staff_data->staff_kiwi();
        if(!$is_count){
            if(count($staff) > 0){
                foreach($staff as $v){
                    $v->rate_cost = 0;
                    $v->rate_name = '';
                    if(count(@$rate[$v->id]) > 0){
                        foreach(@$rate[$v->id] as $start_date=>$val){
                            if(strtotime($start_date) <= strtotime($date) ||
                                strtotime($start_date) <= strtotime(date('Y-m-d',strtotime('+6 days '.$date)))){
                                $v->rate_name = $val->rate_name;
                                $v->rate_cost = $val->rate;
                            }
                        }
                    }

                    /*$v->kiwi = '';
                    $v->emp_kiwi = '';
                    $v->cec_name = '';
                    $v->field_name = '';

                    if(count(@$kiwi_data[$v->id]) > 0){
                        foreach(@$kiwi_data[$v->id] as $start_use=>$val){
                            if(strtotime($start_use) <= strtotime($date) ||
                                strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$date)))){
                                $v->kiwi = $val->kiwi;
                                $v->employer_kiwi = $val->employer_kiwi;
                                $v->field_name = $val->field_name;
                                $v->esct_rate = $val->esct_rate;
                            }
                        }
                    }*/

                    $v->hours = $this->getWageTypeHoursValue($v->id,$v->wage_type,$v->frequency,$date);
                    $v->gross = $v->rate_cost * $v->hours;
                    $v->gross_ = number_format($v->rate_cost * $v->hours,2,'.','');
                    $v->balance = @$this->data['total_bal'][$date][$v->id]['balance'];
                    $v->installment = $v->balance > 0 ? $v->installment : 0;
                    if(!$v->gross){
                        $v->tax = 0;
                        $v->kiwi_ = 0;
                        $v->st_loan = 0;
                    }else{
                        $v->tax = 0;
                        $v->kiwi_ = 0;
                        $v->st_loan = 0;
                        $kiwi = $v->kiwi ? 'kiwi_saver_'.$v->kiwi : '';

                        $data_ = $this->getPayeValue($v->field_code,$v->frequency,$date,$v->gross,$kiwi);
                        if(count($data_) > 0){
                            $v->tax = $data_['tax'];
                            $v->m_paye = $data_['m_paye'];
                            $v->me_paye = $data_['me_paye'];
                            $v->kiwi_ = $kiwi ? $data_['kiwi'] : 0;
                            $v->st_loan = $v->has_st_loan ? $data_['st_loan'] : 0;
                        }
                    }

                    $v->net = $v->gross_ - ($v->kiwi_ + $v->st_loan + $v->tax + $v->transport + $v->installment);

                    $wage_ += floatval($v->net);
                    $paye_ += floatval($v->tax);
                }
            }
        }
        $data['wage_total'] = $wage_;
        $data['paye_total'] = $paye_;
        return $data;
    }

    function generatePaySlip($week,$month,$year,$id){
        $week_in_month = $this->getWeekDateInMonth($year,$month);
        $_week_format = str_pad($week,2,'0',STR_PAD_LEFT);
        $this_date = $week_in_month[$_week_format];

        $this->data['total_paid'] = $this->getOverAllWageTotalPay($this_date,$id);
        $payslip = $this->getPaySlipData($id,$this_date);

        if(count($payslip['staff']) > 0){
            foreach($payslip['staff'] as $val) {
                if((int)$val->hours){
                    $dir                               = realpath(APPPATH . '../pdf');
                    $path                              = 'payslip/' . date('Y/F', strtotime($this_date));
                    $this->data['dir']                 = $dir . '/' . $path;
                    $this->data['total_holiday_leave'] = $this->getAnnualLeave($id, $this_date);
                    $this->data['total_sick_leave']    = $this->getSickLeave($id, $this_date);
                    $this->data['last_pay']            = $val->has_final_pay && $val->last_week_pay == $week ? $this->getStaffLastPay($id,date('Y',strtotime($this_date))) : array();

                    $this->my_model->setSelectFields(array('MIN(start_use) as start_use'));
                    $start_date                    = $this->my_model->getInfo('tbl_staff_rate', $id, 'staff_id');
                    $this->data['start_date']      = '';
                    $this->data['is_download']     = true;
                    $this->data['pay_period_date'] = $this_date;
                    if (count($start_date) > 0) {
                        foreach ($start_date as $sv) {
                            $this->data['start_date'] = date('d/m/Y', strtotime($sv->start_use));
                        }
                    }
                    if (!is_dir($this->data['dir'])) {
                        mkdir($this->data['dir'], 0777, TRUE);
                    }

                    $str                     = $val->has_final_pay && $val->last_week_pay == $week ? '_Final_Payslip_' : '_Payslip_';
                    //$regular                 = date('Ymd', strtotime($this_date)) . $str . str_replace(' ', '', $payslip['staff_name']);
                    $last_pay                = date('Ymd', strtotime('+6 days '.$this_date)) . $str . str_replace(' ', '', $payslip['staff_name']);
                    $filename                = $last_pay;
                    $staff_id                = $id;
                    $this->data['has_email'] = $payslip['has_email'];
                    $this->data['staff']     = $payslip['staff'];
                    $this->data['file_name'] = $filename;

                    $has_value = $this->my_model->getInfo('tbl_pdf_archive', array($filename, $staff_id), array('file_name', 'staff_id'));
                    $post      = array(
                        'staff_id' => $id,
                        'file_name' => $filename . '.pdf',
                        'type' => 'payslip',
                        'date' => date('Y-m-d', strtotime($this_date))
                    );

                    if (count($has_value) > 0) {
                        $this->my_model->update('tbl_pdf_archive', $post, array($filename, $staff_id), array('file_name', 'staff_id'));
                    } else {
                        $this->my_model->insert('tbl_pdf_archive', $post);
                    }
                    $view = $val->has_final_pay && $val->last_week_pay == $week ? 'backend/staff/final_pay/print_pdf_final_payslip' : 'backend/staff/print_pdf_payslip';
                    $this->load->view($view, $this->data);
                }
            }
        }
    }

    function is_decimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }

    function employeeKiwiSaver($id = '',$kiwi_data = ''){
        $whatVal = '';
        $whatFld = '';

        if($id){
            $whatVal = $id;
            $whatFld = 'tbl_staff.id';
        }
        $staff_data = new Staff_Helper();
        $staff_list = $staff_data->staff_details($whatVal,$whatFld);
        $rate = $staff_data->staff_rate();
        //$kiwi_list = $staff_data->staff_kiwi();
        $end = date('Y-m-d');
        $staff = array();
        if(count($staff_list) > 0){
            foreach($staff_list as $val){
                $staff[$id] = array(
                    'date_employed' => $val->date_employed,
                    'frequency_id' => $val->frequency_id,
                    'field_code' => $val->field_code,
                );
            }
        }
        $date_employed = $staff[$id]['date_employed'];
        $frequency_id = $staff[$id]['frequency_id'];
        $field_code = $staff[$id]['field_code'];
        $year_dates = $date_employed ? $this->getWeekBetweenDates($date_employed,$end) : array();
        $data = array();
        if(count($year_dates) > 0){
            foreach($year_dates as $week_year=>$date){
                $hours = $this->getTotalHours($date,$id);
                $rate_cost = 0;
                if(count(@$rate[$id]) > 0){
                    foreach(@$rate[$id] as $used_date=>$val){
                        if(strtotime($used_date) <= strtotime($date) ||
                            strtotime($used_date) <= strtotime(date('Y-m-d',strtotime('+6 days '.$date)))){
                            $rate_cost = $val->rate;
                        }
                    }
                }

                /*$kiwi_val = '';
                if(count(@$kiwi_list[$id]) > 0){
                    foreach(@$kiwi_list[$id] as $start_use=>$val){
                        if(strtotime($start_use) <= strtotime($date) ||
                            strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$date)))){
                            $kiwi_val = $val->kiwi;
                        }
                    }
                }*/

                $gross = $hours * $rate_cost;
                $kiwi_ = $kiwi_data ? 'kiwi_saver_'.$kiwi_data : '';
                $paye_data = $this->getPayeValue($field_code,$frequency_id,$date,$gross,$kiwi_);
                $kiwi = 0;
                if(count($paye_data) > 0){
                    $kiwi = $paye_data['kiwi'];
                }
                $data[$date] = array(
                    'hours' => $hours,
                    'gross' => $gross,
                    'kiwi' => $kiwi
                );
            }
        }

        return $data;
    }
}