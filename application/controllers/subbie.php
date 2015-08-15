<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @property My_Model $my_model Optional description
 */
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
        $_date = new DateTime($date);
        $week = $_date->format('W');
        $week_data = StartWeekNumber($week,$year);
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

    function getTotalHours($date,$id,$action = 'weekly',$start_date = 2){
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
                $hours_gain[$dv->staff_id][$dv->date] = $dv->hours;
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
                $hours_gain[$dv->staff_id][$dv->date] = $dv->hours;
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
                $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
            }
            foreach ($second_year_week_period as $week => $tuesday) {
                $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
            }
        }else if($end_year < 2015){
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$start_year-W01-$start"),
                new DateInterval('P1W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
            }
            $second_year_week_period = new DatePeriod(
                new DateTime("$start_year-W31-1"),
                new DateInterval('P1W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($second_year_week_period as $week => $tuesday) {
                $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
            }
        }else{
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$start_year-W01-1"),
                new DateInterval('P1W'),
                new DateTime("$end_year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                $year_week[$tuesday->format('W-Y')] = $tuesday->format('Y-m-d');
            }
        }

        return $year_week;
    }

    function getYearNumWeek($year,$start = 2){

        /*$weeksPeriod = new DatePeriod(
            new DateTime("$year-W01-$start"),
            new DateInterval('P1W'),
            new DateTime("$year-12-31T23:59:59Z")
        );
        $year_week = array();
        foreach ($weeksPeriod as $week => $monday) {
            $year_week[$monday->format('Y-m-d')] = $monday->format('W');
        }*/
        //$this->displayarray($this->data['year_week']);
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
                $year_week[$tuesday->format('Y-m-d')] = $tuesday->format('Y-m-d');
            }
            foreach ($second_year_week_period as $week => $tuesday) {
                $year_week[$tuesday->format('Y-m-d')] = $tuesday->format('Y-m-d');
            }
        }else if($year < 2015){
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$year-W01-$start"),
                new DateInterval('P1W'),
                new DateTime("$year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                $year_week[$tuesday->format('Y-m-d')] = $tuesday->format('Y-m-d');
            }
        }else{
            $endYearWeeksPeriod = new DatePeriod(
                new DateTime("$year-W01-1"),
                new DateInterval('P1W'),
                new DateTime("$year-12-31T23:59:59Z")
            );
            foreach ($endYearWeeksPeriod as $week => $tuesday) {
                $year_week[$tuesday->format('Y-m-d')] = $tuesday->format('Y-m-d');
            }
        }

        return $year_week;
    }

    function getFirstNextLastDay($y, $m, $day = 'tuesday')
    {
        $start_date = $y.'-'.$m.'-01';
        $start_day = date('l',strtotime($start_date));

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
                $week_num = date('W',strtotime($start_date));
                if(strtotime($start_date) > strtotime('2015-07-26')){
                    $next_day = date('Y-m-d',strtotime('last monday '.$start_date));
                    $date[$week_num] = $next_day;
                }else{
                    $next_day = date('Y-m-d',strtotime('last '. $day .' '.$start_date));
                    $date[$week_num] = $next_day;
                }
            }
        }

        if(count($date_range) > 0){
            foreach($date_range as $dv){
                $this_date = $dv->format('Y-m-d');
                $week_num = $dv->format('W');
                $date[$week_num] = $this_date;

                if(strtotime($this_date) > strtotime('2015-07-26')){
                    $next_day = date('Y-m-d',strtotime('last monday '.$this_date));
                    $date[$week_num] = $next_day;
                }else{
                    $date[$week_num] = $this_date;
                }
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
                $year_week[$monday->format('Y-m-d')] = $monday->format('W');
            }

            $_date = new DateTime($next_day);
            $_week = $_date->format("W");

            $weeksPeriod = new DatePeriod(
                new DateTime("$year-W$_week-$start"),
                new DateInterval('P1W'),
                new DateTime("2015-07-26T23:59:59Z")
            );
            foreach ($weeksPeriod as $week => $monday) {
                $year_week[$monday->format('Y-m-d')] = $monday->format('W');
            }
            $start = 1;
            $weeksPeriod = new DatePeriod(
                new DateTime("$year-W31-$start"),
                new DateInterval('P1W'),
                new DateTime("$end_month_day T23:59:59Z")
            );
            foreach ($weeksPeriod as $week => $monday) {
                $year_week[$monday->format('Y-m-d')] = $monday->format('W');
            }
        }else if($year > 2015){
            $start = 1;
            $start_week =  new DatePeriod(
                new DateTime("$year-W$week-$whatDay"),
                new DateInterval('P1W'),
                new DateTime("$next_day T23:59:59Z")
            );
            foreach ($start_week as $week => $monday) {
                $year_week[$monday->format('Y-m-d')] = $monday->format('W');
            }

            $_date = new DateTime($next_day);
            $_week = $_date->format("W");

            $weeksPeriod = new DatePeriod(
                new DateTime("$year-W$_week-$start"),
                new DateInterval('P1W'),
                new DateTime("2015-07-26T23:59:59Z")
            );

            foreach ($weeksPeriod as $week => $monday) {
                $year_week[$monday->format('Y-m-d')] = $monday->format('W');
            }
        }else{
            $weeksPeriod = new DatePeriod(
                new DateTime("$year-W$week-$start"),
                new DateInterval('P1W'),
                new DateTime("$end_year-03-31T23:59:59Z")
            );
            $year_week = array();
            foreach ($weeksPeriod as $week => $monday) {
                $year_week[$monday->format('Y-m-d')] = $monday->format('W');
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
        for($day=2; $day<=8; $day++)
        {
            $days[$day] = date('l',strtotime($year."W".$week_number.$day));
        }

        return $days;
    }

    function getWeeksNumberInMonth($year, $month, $start = 2){
        $date = $year.'-'.$month.'-01';
        $start_day = date('N',strtotime($date));

        //$days_in_week = $this->getDaysInWeek();

        $days = $this->getFirstNextLastDay($year,$month);
        $thisDays = array();
        if($start_day != $start){
            if($start_day > $start){
                $week_num = date('W',strtotime($date));
                //$next_day = date('Y-m-d',strtotime('last '. $days_in_week[$start].' '.$date));
                $thisDays[$week_num] = $week_num;
            }

        }

        if(count($days) > 0){
            foreach($days as $v){
                $week = date('W',strtotime($v));

                $thisDays[$week] = $week;
            }
        }
        $days_array = $thisDays;
        return $days_array;
    }

    function getWeekDateInMonth($year, $month, $start = 2){
        $date = $year.'-'.$month.'-01';
        $start_day = date('N',strtotime($date));

        $days_in_week = $this->getDaysInWeek();

        $days = $this->getFirstNextLastDay($year,$month);
        $thisDays = array();
        if($start_day != $start){
            if($start_day > $start){
                $week_num = date('W',strtotime($date));
                $next_day = date('Y-m-d',strtotime('last '. $days_in_week[$start].' '.$date));
                $thisDays[$week_num] = $next_day;
            }

        }

        if(count($days) > 0){
            foreach($days as $v){
                $week = date('W',strtotime($v));

                $thisDays[$week] = $v;
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

    function getOverAllWageTotalPay($date,$id = '',$is_details = false){

        $this_year = date('Y',strtotime($date));
        $first_tuesday_in_month = $this_year.'-04-01';//$this->first_day_of_month($this_year);
        $year = strtotime($first_tuesday_in_month) > strtotime($date) ? date('Y-m-d',strtotime('-1 year'.$date)) : date('Y-m-d',strtotime($date));

        $this->getYearTotalBalance($this_year);

        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_rate',
                'tbl_currency',
                'tbl_deductions',
                'tbl_wage_type',
                'tbl_kiwi as employee',
                'tbl_kiwi as employeer',
                'tbl_esct_rate',
                'tbl_tax_codes'
            ),
            'join_field' => array(
                'id','id','staff_id','id','id','id','id','id'
            ),
            'source_field' => array(
                'tbl_staff.rate','tbl_staff.currency','tbl_staff.id','tbl_staff.wage_type',
                'tbl_staff.kiwi_id','tbl_staff.employeer_kiwi','tbl_staff.esct_rate_id',
                'tbl_staff.tax_code_id'
            ),
            'type' => 'left',
            'join_append' => array(
                'tbl_rate',
                'tbl_currency',
                'tbl_deductions',
                'tbl_wage_type',
                'employee',
                'employeer',
                'tbl_esct_rate',
                'tbl_tax_codes'
            )
        ));
        $deductions = $this->arrayWalk(array(
            'flight_deduct','flight_debt',
            'visa_deduct','visa_debt',
            'accommodation','transport'
        ),'tbl_deductions.');
        $staff = $this->arrayWalk(array(
            'tax_number','installment','balance',
            'nz_account as nz_account_','account_two'
        ),'tbl_staff.');

        $fields = array_merge($deductions,$staff);
        $fields[] = 'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name';
        $fields[] = 'tbl_rate.rate_cost';
        $fields[] = 'tbl_staff.id as employee';
        $fields[] = 'tbl_currency.symbols';
        $fields[] = 'tbl_currency.currency_code';
        $fields[] = 'tbl_wage_type.type as wage_type';
        $fields[] = 'tbl_wage_type.frequency as frequency_id';
        $fields[] = 'employee.kiwi';
        $fields[] = 'employeer.kiwi as emp_kiwi';
        $fields[] = 'tbl_esct_rate.field_name';
        $fields[] = 'tbl_esct_rate.cec_name';
        $fields[] = 'tbl_tax_codes.has_st_loan';

        $whatVal = false;
        $whatFld = 'tbl_staff.is_unemployed';

        if($id){
            $whatVal = $id;
            $whatFld = 'tbl_staff.id';
        }

        $this->my_model->setSelectFields($fields);
        $staff_list = $this->my_model->getinfo('tbl_staff',$whatVal,$whatFld);

        $set_wage_date = $this->getPaymentStartDate(date('Y',strtotime($year)));
        $wage_total_data = array();

        if(count($set_wage_date) > 0){
            foreach($set_wage_date as $key=>$dv){
                if(count($staff_list) > 0){
                    foreach($staff_list as $ev){
                        if(strtotime($key) <= strtotime(date('Y-m-d'))){
                            $rate = $this->getStaffRate($ev->employee,$key);
                            $ev->start_use = '';
                            $ev->nz_account = 0;
                            if(count($rate) > 0){
                                foreach($rate as $val){
                                    $ev->rate_name = $val->rate_name;
                                    $ev->rate_cost = $val->rate;
                                    $ev->start_use = $val->start_use;
                                }
                            }


                            $ev->tax = 0;
                            $hours = $ev->wage_type != 1 ? $this->getTotalHoursInMonth($key,$ev->employee) : 1;
                            $ev->gross = $ev->rate_cost * $hours;
                            $ev->gross_ = $ev->gross != 0 ? floatval(number_format($ev->gross,2,'.','')):'0.00';
                            //$ev->gross = $ev->gross != 0 ? floatval(number_format($ev->gross,0,'.','')):'0.00';
                            $ev->kiwi_ = 0;
                            $kiwi = $ev->kiwi ? 'kiwi_saver_'.$ev->kiwi : '';

                            $data_ = $this->getPayeValue($ev->frequency_id,$key,$ev->gross,$kiwi);
                            if(count($data_) > 0){
                                $ev->tax = number_format($data_['tax'],2,'.','');
                                $ev->m_paye = $data_['m_paye'];
                                $ev->me_paye = $data_['me_paye'];
                                $ev->kiwi_ = $kiwi ? $data_['kiwi'] : 0;
                                $ev->st_loan = $ev->has_st_loan ? $data_['st_loan'] : 0;
                            }

                            $hourly_rate = $this->getStaffHourlyRate($ev->employee,$date);
                            $ev->hourly_rate = 0;
                            if(count($hourly_rate) > 0){
                                foreach($hourly_rate as $val){
                                    $ev->hourly_rate = $val->hourly_rate;
                                }
                            }

                            $ev->nz_account = $ev->nz_account_ ? $ev->nz_account_ + ($hours * $ev->hourly_rate) : 0;

                            $ev->flight_debt = @$this->data['total_bal'][$key][$ev->employee]['flight_debt'];
                            $ev->flight_deduct = $ev->flight_debt > 0 ?
                                ($ev->flight_debt <= $ev->flight_deduct ? $ev->flight_debt : $ev->flight_deduct) : 0;

                            $ev->visa_debt = @$this->data['total_bal'][$key][$ev->employee]['visa_debt'];
                            $ev->visa_deduct = $ev->visa_debt > 0 ?
                                ($ev->visa_debt <= $ev->visa_deduct ? $ev->visa_debt : $ev->visa_deduct) : 0;

                            $ev->balance = @$this->data['total_bal'][$key][$ev->employee]['balance'];
                            $ev->installment = $ev->balance > 0 ?
                                ($ev->balance <= $ev->installment ? $ev->balance : $ev->installment) : 0;

                            $ev->recruit = $ev->visa_deduct ? $ev->gross_ * 0.03 : 0;
                            $ev->admin = $ev->visa_deduct ? $ev->gross_ * 0.01 : 0;
                            $ev->nett = $ev->gross_ - ($ev->kiwi_ + $ev->tax + $ev->flight_deduct + $ev->visa_deduct + $ev->accommodation + $ev->transport + $ev->recruit + $ev->admin);

                            $ev->distribution = $ev->nett - $ev->installment;
                            $ev->account_one = $ev->distribution - ($ev->nz_account + $ev->account_two);
                            if($ev->gross > 0){
                                @$ev->total_distribution +=  floatval($ev->distribution);
                                @$ev->total_gross +=  floatval($ev->gross_);
                                @$ev->total_account_one +=  $ev->account_one > 0 ? floatval($ev->account_one) : 0;
                                @$ev->total_account_two +=  floatval($ev->account_two);
                                @$ev->total_nz_account +=  floatval($ev->nz_account);

                                if($is_details){
                                    $wage_total_data[$ev->employee][$key] = array(
                                        'distribution' => floatval($ev->distribution),
                                        'gross' => floatval($ev->gross_),
                                        'financial_year' => date('M-d-Y',mktime(0,0,0,4,1,date('Y',strtotime($year)))) .' to '.date('M-d-Y',mktime(0,0,0,3,31,date('Y',strtotime('+1 year '.$year)))),
                                        'account_one' => $ev->account_one > 0 ? floatval($ev->account_one) : 0,
                                        'account_two' => floatval($ev->account_two),
                                        'nz_account' => floatval($ev->nz_account)
                                    );
                                }else{
                                    $wage_total_data[$ev->employee][$key] = array(
                                        'distribution' => $ev->total_distribution,
                                        'gross' => floatval($ev->total_gross),
                                        'financial_year' => date('M-d-Y',mktime(0,0,0,4,1,date('Y',strtotime($year)))) .' to '.date('M-d-Y',mktime(0,0,0,3,31,date('Y',strtotime('+1 year '.$year)))),
                                        'account_one' => $ev->total_account_one,
                                        'account_two' => $ev->total_account_two,
                                        'nz_account' => $ev->total_nz_account
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
        $this->data['total_bal'] = $this->getYearTotalBalance($year);
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_rate',
                'tbl_currency',
                'tbl_deductions',
                'tbl_wage_type',
                'tbl_tax_codes',
                'tbl_kiwi as kiwi',
                'tbl_kiwi as emp_kiwi',
                'tbl_esct_rate'
            ),
            'join_field' => array(
                'id','id','staff_id','id','id','id','id','id'
            ),
            'source_field' => array(
                'tbl_staff.rate',
                'tbl_staff.currency',
                'tbl_staff.id',
                'tbl_staff.wage_type',
                'tbl_staff.tax_code_id',
                'tbl_staff.kiwi_id',
                'tbl_staff.employeer_kiwi',
                'tbl_staff.esct_rate_id'
            ),
            'type' => 'left',
            'join_append' => array(
                'tbl_rate',
                'tbl_currency',
                'tbl_deductions',
                'tbl_wage_type',
                'tbl_tax_codes',
                'kiwi',
                'emp_kiwi',
                'tbl_esct_rate'
            )
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
        $fields[] = 'tbl_staff.id as staff_id';
        $fields[] = 'tbl_staff.email';
        $fields[] = 'tbl_currency.symbols';
        $fields[] = 'tbl_currency.currency_code';
        $fields[] = 'tbl_wage_type.type as wage_type';
        $fields[] = 'tbl_wage_type.frequency';
        $fields[] = 'tbl_tax_codes.tax_code';
        $fields[] = 'IF(tbl_staff.ird_num != "-" ,LPAD(tbl_staff.ird_num,11,"0"),tbl_staff.ird_num) as ird_num';
        $fields[] = 'kiwi.kiwi';
        $fields[] = 'emp_kiwi.kiwi as emp_kiwi';
        $fields[] = 'tbl_esct_rate.field_name';
        $fields[] = 'tbl_esct_rate.cec_name';
        $fields[] = 'tbl_wage_type.frequency as frequency_id';
        $fields[] = 'tbl_tax_codes.has_st_loan';

        $this->my_model->setSelectFields($fields);
        $this->my_model->setOrder(array('lname','fname'));
        $whatVal = array(false,3);
        $whatFld = array('is_unemployed','status_id');
        $staff_list = $this->my_model->getinfo('tbl_staff',$whatVal,$whatFld);
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
                                $ev->total_holiday_leave = $this->getAnnualLeave($ev->staff_id,$dv);
                                $ev->total_sick_leave = $this->getSickLeave($ev->staff_id,$dv);
                                $rate = $this->getStaffRate($ev->staff_id,$dv);
                                $ev->start_use = '';
                                if(count($rate) > 0){
                                    foreach($rate as $val){
                                        $ev->rate_name = $val->rate_name;
                                        $ev->rate_cost = $val->rate;
                                        $ev->start_use = $val->start_use;
                                    }
                                }

                                $hourly_rate = $this->getStaffHourlyRate($ev->staff_id,$dv);
                                $ev->hourly_rate = 0;
                                if(count($hourly_rate) > 0){
                                    foreach($hourly_rate as $val){
                                        $ev->hourly_rate = $val->hourly_rate;
                                    }
                                }

                                $this->data['balance'][$ev->staff_id] = array(
                                    'balance' => $ev->balance,
                                    'flight_debt' => $ev->flight_debt,
                                    'visa_debt' => $ev->visa_debt
                                );
                                $code = $ev->currency_code != 'NZD' ? $ev->currency_code : 'PHP';
                                $symbols = $ev->currency_code != 'NZD' ? $ev->symbols : '₱';

                                $converted_amount = $ev->currency_code != 'NZD' ? 1 : $phpCurrency;

                                $ev->tax = 0;
                                $hours = $ev->wage_type != 1 ? $this->getTotalHours($dv,$ev->staff_id) : 1;
                                $ev->gross = $ev->rate_cost * $hours;
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

                                $ev->nz_account = $ev->nz_account_ ? $ev->nz_account_ + ($hours * $ev->hourly_rate) : 0;

                                $data_ = $this->getPayeValue($ev->frequency_id,$dv,$ev->gross_,$kiwi,$emp_kiwi,$cec,$esct);
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

                                $ev->flight_debt = @$this->data['total_bal'][$dv][$ev->staff_id]['flight_debt'];
                                $ev->flight_deduct = $ev->flight_debt > 0 ?
                                    ($ev->flight_debt <= $ev->flight_deduct ? $ev->flight_debt : $ev->flight_deduct) : 0;

                                $ev->visa_debt = @$this->data['total_bal'][$dv][$ev->staff_id]['visa_debt'];
                                $ev->visa_deduct = $ev->visa_debt > 0 ?
                                    ($ev->visa_debt <= $ev->visa_deduct ? $ev->visa_debt : $ev->visa_deduct) : 0;

                                $ev->balance = @$this->data['total_bal'][$dv][$ev->staff_id]['balance'];
                                $ev->installment = $ev->balance > 0 ?
                                    ($ev->balance <= $ev->installment ? $ev->balance : $ev->installment) : 0;

                                $ev->recruit = $ev->visa_deduct ? $ev->gross * 0.03 : 0;
                                $ev->admin = $ev->visa_deduct ? $ev->gross * 0.01 : 0;

                                $ev->nett = $ev->gross_ - ($ev->kiwi_ + $ev->tax + $ev->flight_deduct + $ev->visa_deduct + $ev->accommodation + $ev->transport + $ev->recruit + $ev->admin);

                                $ev->distribution = $ev->nett - $ev->installment;
                                $ev->account_one = $ev->distribution - ($ev->nz_account + $ev->account_two);
                                $this->data['wage_data'][$dv][] = array(
                                    'id' => $ev->staff_id,
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
                                    'nz_account' => $hours > 0 ? $ev->nz_account : 0,
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
                                    'kiwi' => $ev->kiwi_,
                                    'has_kiwi' => $ev->kiwi ? 1 : 0,
                                    'emp_kiwi' => $ev->emp_kiwi_,
                                    'cec' => $ev->cec,
                                    'esct' => $ev->esct,
                                    'st_loan' => $ev->st_loan,
                                    'has_st_loan' => $ev->has_st_loan
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
                $date = $this->getFirstNextLastDay($year,$month);

                $phpCurrency = CurrencyConverter('PHP');
                if(count($staff_list)>0){
                    foreach($staff_list as $mv){
                        if(count($date) >0){
                            foreach($date as $dv){
                                $monthly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->staff_id) : 1;
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

                                    $hourly_rate = $this->getStaffHourlyRate($mv->staff_id,$dv);
                                    $mv->hourly_rate = 0;
                                    if(count($hourly_rate) > 0){
                                        foreach($hourly_rate as $val){
                                            $mv->hourly_rate = $val->hourly_rate;
                                        }
                                    }

                                    $mv->hours = $monthly_hours;
                                    $mv->gross_ = number_format($mv->hours * $mv->rate_cost,2,'.','');
                                    $mv->gross = $mv->hours * $mv->rate_cost;
                                    $mv->weekly_gross = number_format($weekly_hours * $mv->rate_cost,0,'.','');

                                    $mv->nz_account = $mv->nz_account_ ? ($mv->nz_account_ + ($mv->hours * $mv->hourly_rate)) : 0;

                                    $mv->flight_debt = @$this->data['total_bal'][$dv][$mv->staff_id]['flight_debt'];
                                    $mv->flight = $mv->flight_debt > 0 ?
                                        ($mv->flight_debt <= $mv->flight_deduct ? $mv->flight_debt : $mv->flight_deduct) : 0;

                                    $mv->visa_debt = @$this->data['total_bal'][$dv][$mv->staff_id]['visa_debt'];
                                    $mv->visa = $mv->visa_debt > 0 ?
                                        ($mv->visa_debt <= $mv->visa_deduct ? $mv->visa_debt : $mv->visa_deduct) : 0;

                                    $mv->balance = @$this->data['total_bal'][$dv][$mv->staff_id]['balance'];
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
                                    $data_ = $this->getPayeValue($mv->frequency_id,$dv,$mv->gross,$kiwi,$emp_kiwi,$cec,$esct);
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
                                    @$mv->total_nz_account += $mv->nz_account;
                                    @$mv->total_kiwi += $mv->kiwi_;
                                    @$mv->total_emp_kiwi += $mv->emp_kiwi_;
                                    @$mv->total_st_loan += $mv->st_loan;
                                    @$mv->total_cec += $mv->cec;
                                    @$mv->total_esct += $mv->esct;

                                    $this->data['monthly_pay'][$mv->staff_id] = array(
                                        'staff_id' => $mv->staff_id,
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
                $whatVal = array(false,3);
                $whatFld = array('is_unemployed','status_id');
                $this->data['staff'] = $this->my_model->getinfo('tbl_staff',$whatVal,$whatFld);
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
                'tbl_kiwi as employee',
                'tbl_kiwi as employeer',
                'tbl_esct_rate',
                'tbl_tax_codes'
            ),
            'join_field' => array(
                'id', 'id',
                'staff_id', 'id',
                'id', 'id',
                'id', 'id'
            ),
            'source_field' => array(
                'tbl_staff.rate',
                'tbl_staff.currency',
                'tbl_staff.id',
                'tbl_staff.wage_type',
                'tbl_staff.kiwi_id',
                'tbl_staff.employeer_kiwi',
                'tbl_staff.esct_rate_id',
                'tbl_staff.tax_code_id'
            ),
            'type' => 'left',
            'join_append' => array(
                'tbl_rate',
                'tbl_currency',
                'tbl_deductions',
                'tbl_wage_type',
                'employee',
                'employeer',
                'tbl_esct_rate',
                'tbl_tax_codes'
            )
        ));
        $this->my_model->setSelectFields(array(
            'tbl_staff.id',
            'tbl_staff.ird_num',
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
            'tbl_wage_type.frequency as frequency_id',
            'employee.kiwi',
            'employeer.kiwi as emp_kiwi',
            'tbl_esct_rate.field_name',
            'tbl_esct_rate.cec_name',
            'tbl_tax_codes.has_st_loan',
            'tbl_staff.email',
            'tbl_staff.is_email_payslip',
            'IF(tbl_currency.symbols = "₱","Php",tbl_currency.symbols) as symbols'
        ),false);

        $data['staff'] = $this->my_model->getinfo('tbl_staff',$id,'tbl_staff.id');

        $this->getYearTotalBalance(date('Y',strtotime($date)));
        $data['staff_name'] = '';
        $data['has_email'] = 0;
        $phpCurrency = CurrencyConverter('PHP');
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

                $rate = $this->getStaffRate($v->id,$date);
                if(count($rate) > 0){
                    foreach($rate as $val){
                        $v->rate_name = $val->rate_name;
                        $v->rate_cost = $val->rate;
                    }
                }

                $hourly_rate = $this->getStaffHourlyRate($v->id,$date);
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
                //$v->gross = $v->gross != 0 ? number_format($v->gross,0,'.',''):'0.00';

                $v->flight_debt = @$this->data['total_bal'][$date][$v->id]['flight_debt'];
                $v->flight = $v->flight_debt > 0 ? $v->flight : 0;

                $v->visa_debt = @$this->data['total_bal'][$date][$v->id]['visa_debt'];
                $v->visa = $v->visa_debt > 0 ? $v->visa : 0;

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

                    $data_ = $this->getPayeValue($v->frequency_id,$date,$v->gross,$kiwi);
                    if(count($data_) > 0){
                        $v->tax = $data_['tax'];
                        $v->m_paye = $data_['m_paye'];
                        $v->me_paye = $data_['me_paye'];
                        $v->kiwi_ = $kiwi ? $data_['kiwi'] : 0;
                        $v->st_loan = $v->has_st_loan ? $data_['st_loan'] : 0;
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
            'IF(tbl_staff.date_employed != "0000-00-00" ,DATE_FORMAT(tbl_staff.date_employed,"%d-%m-%Y"),"") as date_employed'
        ),false);
        $this->my_model->setGroupBy('id');
        $whatVal = 'tbl_staff_rate.start_use <= "'.$date_.'" AND tbl_staff.is_unemployed !=1';
        $this->my_model->setOrder(array('lname','fname'));
        $this->data['staff'] = $this->my_model->getinfo('tbl_staff',$whatVal,'');
        if(count($date) >0){
            foreach($date as $dv){
                if(count($this->data['staff'])>0){
                    foreach($this->data['staff'] as $mv){
                        $monthly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->id) : 1;
                        $rate = $this->getStaffRate($mv->id,$dv);
                        $mv->total_hours = 0;
                        $mv->total_gross = 0;
                        $mv->total_tax = 0;
                        $mv->total_st_loan = 0;
                        $mv->total_emp_kiwi = 0;
                        $mv->total_kiwi = 0;
                        $mv->total_esct = 0;
                        $mv->total_cec = 0;
                        if(count($rate) > 0){
                            foreach($rate as $val){
                                $mv->rate_name = $val->rate_name;
                                $mv->rate_cost = $val->rate;
                            }
                        }

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
                                $data_ = $this->getPayeValue($mv->frequency_id,$dv,$mv->gross,$kiwi,$emp_kiwi,$cec,$esct);
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

        $total_bal = array();

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
                                    $total_bal[$y][$ev->id] = array(
                                        'visa_debt' => $ev->visa_debt > 0 ? $ev->visa_debt : '',
                                        'flight_debt' => $ev->flight_debt > 0 ? $ev->flight_debt : '',
                                        'balance' => $ev->balance > 0 ? $ev->balance : ''
                                    );
                                    break;
                                case 'monthly':
                                    $total_bal[date('Y-m',strtotime($y))][$ev->id] = array(
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

        return $total_bal;
    }

    function getStaffRate($id,$date = ''){
        $whatVal = $id;
        $whatFld = 'staff_id';

        if($date){
            $whatVal = '(start_use <= "' . $date . '" OR start_use <= "'. date('Y-m-d',strtotime('+6 days '.$date)) .'" ) AND staff_id = "' . $id . '"';
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
            $whatVal = '(date_used <= "' . $date . '" OR date_used <= "' . date('Y-m-d',strtotime('+6 days '.$date)) . '") AND staff_id = "' . $id . '"';
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
        $fields = $this->arrayWalk(
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

    function payPeriodSentEmail($date,$range,$msg){
        $has_pay_setup = $this->my_model->getInfo('tbl_pay_setup');

        $debugResult = array();

        if(count($has_pay_setup) > 0){
            $date = date('Y-m-d',strtotime($date));
            $this->my_model->setShift();
            $pay_setup = (Object)$this->my_model->getInfo('tbl_pay_setup');

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
                'subject' => 'Pay Period Summary Report from ' . date('j F Y',strtotime($date)) .' to '.date('j F Y',strtotime('+6 days '.$date)),
                'url' => $url,
                'debug_type' => 2,
                'debug' => true
            );

            $email_send = new Send_Email_Controller();
            $debugResult['result'] = $email_send->sendingEmail(
                $msg,
                $sendMailSetting
            );
            $debugResult['mail_settings'] = $sendMailSetting;
        }

        return $debugResult;
    }

    function getPayeValue($frequency_id,$date,$gross,$kiwi = '',$emp_kiwi='',$cec = '',$esct = ''){
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
            if($frequency_id == 2){
                if($gross > $earnings['fortnightly']){
                    $data['tax'] = ((floatval($gross) - $earnings['fortnightly']) * 0.33) + $m_paye['fortnightly'];
                }else{
                    if($this->is_decimal($gross)){
                        $is_even = $gross % 2;
                        $_gross_val = number_format($gross,0,'.','');
                        if($is_even){
                            $whatVal = 'earnings = (SELECT MIN(earnings) FROM tbl_tax WHERE earnings > '.$_gross_val.') AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';
                            $this->my_model->setShift();
                            $this->my_model->getinfo('tbl_tax',$whatVal,'');
                            $lower_sql_str = $this->db->last_query();

                            $whatVal = 'earnings = (SELECT MAX(earnings) FROM tbl_tax WHERE earnings < '.$_gross_val.') AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';

                            $this->my_model->setShift();
                            $this->my_model->getinfo('tbl_tax',$whatVal,'');
                            $higher_sql_str = $this->db->last_query();

                        }else{
                            $whatVal = 'earnings = (SELECT MIN(earnings) FROM tbl_tax WHERE earnings > '.$_gross_val.') AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';
                            $this->my_model->setShift();
                            $this->my_model->getinfo('tbl_tax',$whatVal,'');
                            $lower_sql_str = $this->db->last_query();

                            $whatVal = 'earnings = (SELECT MAX(earnings) FROM tbl_tax WHERE earnings < '.$_gross_val.') AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';

                            $this->my_model->setShift();
                            $this->my_model->getinfo('tbl_tax',$whatVal,'');
                            $higher_sql_str = $this->db->last_query();
                        }

                        $_paye_table = $this->my_model->mysqlString($higher_sql_str.' UNION '.$lower_sql_str);

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


                        if(count($_paye_table) > 0){
                            foreach($_paye_table as $key=>$paye){
                                if($key == 0){
                                    $lower_tax = $paye->m_paye;
                                    $l_m_paye = $paye->m_paye;
                                    $l_me_paye = $paye->me_paye;
                                    $l_kiwi = $kiwi ? $paye->$kiwi : 0;
                                    $l_st_loan = $paye->sl_loan_ded;
                                    $l_cec = $cec ? $paye->$cec : 0;
                                    $l_esct = $esct ? $paye->$esct : 0;
                                    $l_emp_kiwi = $emp_kiwi ? $paye->$emp_kiwi : 0;
                                }else{
                                    $higher_tax = $paye->m_paye;
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
                                $data['tax'] = $tv->m_paye;
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
            }else if($frequency_id == 1){
                if($gross > $earnings['weekly']){
                    $data['tax'] = ((floatval($gross) - $earnings['weekly']) * 0.33) + $m_paye['weekly'];
                }else{
                    if($this->is_decimal($gross)){
                        $_gross = number_format($gross,0,'.','');
                        $l_gross = $_gross - 1;
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


                        $whatVal = 'earnings = (SELECT MAX(earnings) FROM tbl_tax WHERE earnings = '.$l_gross.') AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';
                        $this->my_model->setShift();
                        $this->my_model->getinfo('tbl_tax',$whatVal,'');
                        $lower_sql_str = $this->db->last_query();

                        $whatVal = 'earnings = (SELECT MAX(earnings) FROM tbl_tax WHERE earnings = '.$_gross.') AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';

                        $this->my_model->setShift();
                        $this->my_model->getinfo('tbl_tax',$whatVal,'');
                        $higher_sql_str = $this->db->last_query();

                        $_paye_table = $this->my_model->mysqlString($higher_sql_str.' UNION '.$lower_sql_str);

                        if(count($_paye_table) > 0){
                            foreach($_paye_table as $key=>$paye){
                                if($key == 0){
                                    $lower_tax = $paye->m_paye;
                                    $l_m_paye = $paye->m_paye;
                                    $l_me_paye = $paye->me_paye;
                                    $l_kiwi = $kiwi ? $paye->$kiwi : 0;
                                    $l_st_loan = $paye->sl_loan_ded;
                                    $l_cec = $cec ? $paye->$cec : 0;
                                    $l_esct = $esct ? $paye->$esct : 0;
                                    $l_emp_kiwi = $emp_kiwi ? $paye->$emp_kiwi : 0;
                                }else{
                                    $higher_tax = $paye->m_paye;
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

                        $data['data'] = $_paye_table;
                        $data['tax'] = $lower_tax + (($higher_tax - $lower_tax)/2);
                        $data['m_paye'] = $l_m_paye + (($h_m_paye - $l_m_paye)/2);
                        $data['me_paye'] = $l_me_paye + (($h_me_paye - $l_me_paye)/2);
                        $data['kiwi'] = $l_kiwi + (($h_kiwi - $l_kiwi)/2);
                        $data['st_loan'] = $l_st_loan + (($h_st_loan - $l_st_loan)/2);
                        $data['cec'] = $l_cec + (($h_cec - $l_cec)/2);
                        $data['esct'] = $l_esct + (($h_esct - $l_esct)/2);
                        $data['emp_kiwi'] = $l_emp_kiwi + (($h_emp_kiwi - $l_emp_kiwi)/2);
                    }else{
                        $_gross = number_format($gross,0,'.','');
                        $whatVal = 'earnings ="'.$_gross.'" AND start_date <= "'.$date.'" AND end_date >= "'.$date.'" AND frequency_id="'.$frequency_id.'"';
                        $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');
                        if(count($tax)>0){
                            foreach($tax as $tv){
                                $data['tax'] = $tv->m_paye;
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
            }else{

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
        $this->getYearTotalBalance(date('Y',strtotime($date)));
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
        $fld[] = 'tbl_wage_type.frequency';
        $fld[] = 'tbl_deductions.transport';
        $fld[] = 'tbl_kiwi.kiwi';
        $fld[] = 'tbl_tax_codes.has_st_loan';

        $this->my_model->setSelectFields($fld);
        $this->my_model->setOrder(array('lname','fname'));
        $staff = $this->my_model->getInfo('tbl_staff',$whatVal,$whatFld);
        $data = array(
            'staff_count' => count($staff) > 0 ? count($staff) : 0
        );
        $wage_ = 0;
        $paye_ = 0;
        if(!$is_count){
            if(count($staff) > 0){
                foreach($staff as $v){
                    $rate = $this->getStaffRate($v->id,$date);
                    $v->rate_cost = 0;
                    $v->rate_name = '';
                    if(count($rate) > 0){
                        foreach($rate as $val){
                            $v->rate_name = $val->rate_name;
                            $v->rate_cost = $val->rate;
                        }
                    }

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

                        $data_ = $this->getPayeValue($v->frequency,$date,$v->gross,$kiwi);
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

    function is_decimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}