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
        $this->data['earnings'] = $this->my_model->getInfo('tbl_tax');

        $this->my_model->setLastId('m_paye');
        $this->data['m_paye'] = $this->my_model->getInfo('tbl_tax');
        $this->data['info_array'] = $this->my_model->getInfo('tbl_invoice_info');

        $this->data['account_type'] = $this->session->userdata('account_type');

        $this->my_model->setShift();
        $this->data['user'] = (Object)$this->my_model->getInfo('tbl_user',$this->session->userdata('user_id'));


        $this->data['notification'] = $this->getInvoiceNotification();
        $this->data['count_msg'] = count($this->data['notification']);

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

    function getWeeks($date, $rollover)
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

    function currencyConverter($code,$default_currency = 'NZD'){
        $amount = urlencode(1);
        $from_Currency = urlencode($default_currency);
        $to_Currency = urlencode($code);
        $get = file_get_contents("https://www.google.com/finance/converter?a=".$amount."&from=".$from_Currency."&to=".$to_Currency."");
        $get = explode("<span class=bld>",$get);
        $get = explode("</span>",$get[1]);
        $converted_amount = preg_replace("/[^0-9\.]/", null, $get[0]);

        return $converted_amount;
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

        switch($type){
            case 'weekly':
                $this->my_model->setJoin(array(
                    'table' => array('tbl_rate','tbl_currency','tbl_deductions','tbl_wage_type'),
                    'join_field' => array('id','id','staff_id','id'),
                    'source_field' => array('tbl_staff.rate','tbl_staff.currency','tbl_staff.id','tbl_staff.wage_type'),
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

                $this->my_model->setSelectFields($fields);
                $staff_list = $this->my_model->getinfo('tbl_staff');
                $this->data['balance'] = array();
                $this->data['wage_data'] = array();

                $this->data['date'] = $this->getFirstNextLastDay($year,$month,'tuesday');

                if(count($this->data['date']) > 0){
                    foreach($this->data['date'] as $dv){
                        if(count($staff_list) > 0){
                            foreach($staff_list as $ev){
                                $rate = $this->getStaffRate($ev->employee,$dv);

                                if(count($rate) > 0){
                                    foreach($rate as $val){
                                        $ev->rate_name = $val->rate_name;
                                        $ev->rate_cost = $val->rate;
                                    }
                                }

                                $this->data['balance'][$ev->employee] = array(
                                    'balance' => $ev->balance,
                                    'flight_debt' => $ev->flight_debt,
                                    'visa_debt' => $ev->visa_debt
                                );
                                $code = $ev->currency_code != 'NZD' ? $ev->currency_code : 'PHP';
                                $symbols = $ev->currency_code != 'NZD' ? $ev->symbols : '₱';

                                $converted_amount = 1;//$this->currencyConverter($code);

                                $ev->tax = 0;
                                $hours = $ev->wage_type != 1 ? $this->getTotalHours($dv,$ev->employee) : 1;
                                $ev->gross = $ev->rate_cost * $hours;
                                $ev->gross = $ev->gross != 0 ? number_format($ev->gross,0,'',''):'0.00';

                                if($ev->gross > $earnings){
                                    $ev->tax = (($ev->gross - $earnings) * 0.33) + $m_paye;
                                }else{
                                    $whatVal = 'earnings ="'.$ev->gross.'" AND start_date <= "'.$dv.'"';
                                    $tax = $this->my_model->getinfo('tbl_tax',$whatVal,'');

                                    if(count($tax)>0){
                                        foreach($tax as $tv){
                                            $ev->tax = $tv->m_paye;
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
                                $ev->nett = $ev->gross - ($ev->tax + $ev->flight_deduct + $ev->visa_deduct + $ev->accommodation + $ev->transport + $ev->recruit + $ev->admin);

                                $ev->distribution = $ev->nett - $ev->installment;
                                $ev->account_one = $ev->distribution - ($ev->nz_account + $ev->account_two);
                                $this->data['wage_data'][$dv][] = array(
                                    'id' => $ev->employee,
                                    'name' => $ev->name,
                                    'tax_number' => $ev->tax_number,
                                    'gross' => '$'.$ev->gross,
                                    'rate_cost' => $ev->rate_cost,
                                    'hours' => $hours != '' ? $hours : 0,
                                    'tax' => $ev->tax != 0 ? '$'.$ev->tax : '',
                                    'flight' => $ev->flight_deduct,
                                    'visa' => $ev->visa_deduct,
                                    'accommodation' => $ev->accommodation != '' ? '$'.$ev->accommodation: '',
                                    'transport' => $ev->transport != '' ? '$'.$ev->transport : '',
                                    'deduction' => $ev->installment != '' || $ev->installment != 0 ? '$'.$ev->installment : '',
                                    'distribution' => $ev->distribution,
                                    'date' => $dv,
                                    'nett' => '$'.number_format($ev->nett,2,'.',''),
                                    'recruit' => $ev->flight_deduct ? '$'.number_format($ev->recruit,2,'.','') : '',
                                    'admin' => $ev->flight_deduct ? '$'.number_format($ev->admin,2,'.','') : '',
                                    'currency' => $code,
                                    'account_two' => $ev->account_two,
                                    'account_one' => $ev->flight_deduct ? $ev->account_one : 0,
                                    'nz_account' => $ev->nz_account,
                                    'rate_value' => $converted_amount,
                                    'symbols' => $symbols,
                                    'installment' => $ev->installment
                                );
                            }
                        }
                    }
                }
                break;
            case 'monthly':
                $this->data['monthly_pay'] = array();
                $date = $this->getFirstNextLastDay($year,$month,'tuesday');
                $this->data['staff'] = $this->my_model->getinfo('tbl_staff');

                $this->my_model->setJoin(array(
                    'table' => array('tbl_rate','tbl_currency','tbl_deductions','tbl_wage_type'),
                    'join_field' => array('id','id','staff_id','id'),
                    'source_field' => array('tbl_staff.rate','tbl_staff.currency','tbl_staff.id','tbl_staff.wage_type'),
                    'type' => 'left'
                ));
                $this->my_model->setSelectFields(array(
                    'tbl_staff.id as staff_id',
                    'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name',
                    'tbl_currency.symbols',
                    'tbl_deductions.flight_deduct as flight',
                    'tbl_rate.rate_cost',
                    'tbl_deductions.visa_deduct as visa','tbl_deductions.accommodation',
                    'tbl_deductions.transport',
                    'tbl_staff.balance','tbl_staff.installment',
                    'tbl_currency.currency_code','tbl_staff.account_two',
                    'tbl_staff.nz_account',
                    'tbl_wage_type.type as wage_type'
                ),false);

                $staff_wage = $this->my_model->getinfo('tbl_staff');

                if(count($staff_wage)>0){
                    foreach($staff_wage as $mv){
                        if(count($date) >0){
                            foreach($date as $dv){
                                $monthly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->staff_id,'monthly') : 1;
                                $weekly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->staff_id) : 1;
                                $rate = $this->getStaffRate($mv->staff_id,$dv);
                                if(count($rate) > 0){
                                    foreach($rate as $val){
                                        $mv->rate_name = $val->rate_name;
                                        $mv->rate_cost = $val->rate;
                                    }
                                }

                                if($monthly_hours != 0){
                                    $mv->hours = $monthly_hours;
                                    $mv->gross = number_format($mv->hours * $mv->rate_cost,0,'.','');
                                    $mv->weekly_gross = number_format($weekly_hours * $mv->rate_cost,0,'.','');

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

                                    $converted_amount = $this->currencyConverter($code);
                                    if(!$mv->gross){
                                        $mv->tax = 0;
                                    }else{
                                        $mv->tax = 0;
                                        $mv->m_paye = 0;
                                        $mv->me_paye = 0;
                                        if($mv->gross > $earnings){
                                            $mv->tax = (($mv->gross - $earnings) * 0.33) + $m_paye;
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

                                    @$mv->tax_total += $mv->tax;
                                    @$mv->total_install += $mv->installment;
                                    @$mv->total_accom += $mv->accommodation;
                                    @$mv->total_trans += $mv->transport;
                                    @$mv->total_visa += $mv->visa;
                                    @$mv->total_flight += $mv->flight;
                                    @$mv->total_account_two += $mv->account_two;
                                    @$mv->total_nz_account += $mv->nz_account;

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
                                        'hours' => $mv->hours,
                                        'gross' => $mv->gross,
                                        'recruit' => $mv->recruit,
                                        'admin' => $mv->admin,
                                        'total_tax' => $mv->tax_total,
                                        'weekly_gross' => $mv->weekly_gross,
                                        'm_paye' => $mv->m_paye,
                                        'me_paye' => $mv->me_paye,
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

    function getEmployerData($year,$month){

        $date = $this->getFirstNextLastDay($year,$month,'tuesday');
        $this->data['monthly_pay'] = array();
        $this->data['over_all_pay'] = array();
        $earnings = $this->data['earnings'];
        $m_paye = $this->data['m_paye'];

        $this->my_model->setJoin(array(
            'table' => array('tbl_rate','tbl_tax_codes','tbl_wage_type'),
            'join_field' => array('id','id','id'),
            'source_field' => array('tbl_staff.rate','tbl_staff.tax_code_id','tbl_staff.wage_type'),
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

        $this->data['staff'] = $this->my_model->getinfo('tbl_staff');
        if(count($this->data['staff'])>0){
            foreach($this->data['staff'] as $mv){
                if(count($date) >0){
                    foreach($date as $dv){
                        $monthly_hours = $mv->wage_type != 1 ? $this->getTotalHours($dv,$mv->id,'monthly') : 1;
                        $rate = $this->getStaffRate($mv->id,$dv);
                        if(count($rate) > 0){
                            foreach($rate as $val){
                                $mv->rate_name = $val->rate_name;
                                $mv->rate_cost = $val->rate;
                            }
                        }

                        if($monthly_hours != 0){
                            $mv->hours = $monthly_hours;
                            $mv->gross = number_format($mv->hours * $mv->rate_cost,0,'.','');

                            if(!$mv->gross){
                                $mv->tax = 0;
                            }else{
                                $mv->tax = 0;
                                $mv->m_paye = 0;
                                $mv->me_paye = 0;
                                if($mv->gross > $earnings){
                                    $mv->tax = (($mv->gross - $earnings) * 0.33) + $m_paye;
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

                            $this->data['monthly_pay'][$mv->id] = array(
                                'id' => $mv->id,
                                'rate_cost' => $mv->rate_cost,
                                'tax' => $mv->tax,
                                'hours' => $mv->hours,
                                'gross' => $mv->gross,
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

        //$this->displayarray($year_week);
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
        $staff_list = $this->my_model->getinfo('tbl_staff');

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

        //$this->displayarray($this->data['total_bal']);
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

    function displayarray($ar, $color = "F00"){
        echo '<pre style="font-size:12px;z-index:9999;color:#'.$color.'">';
        print_r($ar);
        echo '</pre><br style="clear:both;" /><br />';
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
            }else{
                $id = $this->uri->segment(2);

                if(!$id){
                    exit;
                }

                $post = array(
                    'is_new' => false
                );
                $this->my_model->update('tbl_invoice',$post,$id);
            }
        }

    }

    function getInvoiceNotification(){
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

        $whatVal = true;
        $whatFld = 'tbl_invoice.is_new';

        $invoice = $this->my_model->getInfo('tbl_invoice',$whatVal,$whatFld);

        return $invoice;
    }
}