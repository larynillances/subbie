<?php

class Wage_Controller extends CI_Controller{
    var $data;

    function __construct(){
        parent::__construct();
        $this->paye_table_data();
    }

    function paye_table_data(){

        $this->my_model->setLastId('earnings');
        $this->my_model->setSelectFields(array('MAX(earnings) as earnings'));
        $data['earnings']['fortnightly'] = $this->my_model->getInfo('tbl_tax',2,'frequency_id');

        $this->my_model->setLastId('m_paye');
        $this->my_model->setSelectFields(array('MAX(m_paye) as m_paye'));
        $data['m_paye']['fortnightly'] = $this->my_model->getInfo('tbl_tax',2,'frequency_id');

        $this->my_model->setLastId('earnings');
        $this->my_model->setSelectFields(array('MAX(earnings) as earnings'));
        $data['earnings']['weekly'] = $this->my_model->getInfo('tbl_tax',1,'frequency_id');

        $this->my_model->setLastId('m_paye');
        $this->my_model->setSelectFields(array('MAX(m_paye) as m_paye'));
        $data['m_paye']['weekly'] = $this->my_model->getInfo('tbl_tax',1,'frequency_id');

        return $data;

    }

    function get_total_hours($date,$id,$action = 'weekly'){
        $hours_gain = array();
        $year = date('Y',strtotime($date));
        //$month = date('m',strtotime($date));
        $_date = new DateTime($date);
        $week = $_date->format('W');

        $week_data = StartWeekNumber($week,$year);
        //$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $this->my_model->setSelectFields(array(
            'IF(time_out != "" AND time_in != "",
                SUM((TIMESTAMPDIFF(SECOND, time_in, time_out) / 3600) - IF(TIME(DATE_FORMAT(time_out,"%H:%i:%s")) > MAKETIME(13,30,0), 0.50, 0))
                    , 0) as hours',
            'time_in','time_out','staff_id','date',
            'id as dtr_id','working_type_id'
        ));

        $_added_days = $week_data['days_count'];

        if($action == 'weekly'){
            $date_end = date('Y-m-d',strtotime('+' . $_added_days . ' days '.$date));
            $whatVal = '(date BETWEEN "' . $date . '" AND "' . $date_end. '") AND staff_id ="'.$id.'"';
        }else{
            $start_month_year = date('Ym',strtotime($date));
            $whatVal = 'EXTRACT( YEAR_MONTH FROM date) = "' . $start_month_year . '" AND staff_id ="'.$id.'"';
        }

        $dtr = $this->my_model->getinfo('tbl_login_sheet', $whatVal,'');

        if(count($dtr) > 0){
            foreach($dtr as $dv){
                @$hours_gain[$dv->staff_id] = $dv->hours;
            }
        }

        $thisDtr = array_key_exists($id, $hours_gain) ? $hours_gain[$id] : 0;

        $hoursValue = number_format((@$thisDtr),2);
        return $hoursValue;
    }

    function get_total_hours_in_month($date,$id,$action = 'weekly'){
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
        $last_day_of_march = last_day_of_month($year);

        $week_data = StartWeekNumber($week,$year);
        $dt = new DateTime;
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $this->my_model->setSelectFields(array(
            'IF(time_out != "" AND time_in != "", TIMESTAMPDIFF(SECOND, time_in, time_out) , 0) as hours',
            'time_in','time_out','staff_id','date',
            'id as dtr_id','working_type_id'
        ));

        $_added_days = $week_data['days_count'];

        $date_end = date('Y-m-d',strtotime('+'.$_added_days.' days '.$date));
        $whatVal = '(date BETWEEN "' . $date . '" AND "' . $date_end. '") AND staff_id ="'.$id.'"';
        $dtr = $this->my_model->getinfo('tbl_login_sheet', $whatVal,'');

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
        $hoursValue = number_format((@$totalHours[$id]/3600),2);
        return $hoursValue;
    }

    function get_year_total_balance($id = ''){
        $date = strtotime(date('Y-m-d'));//
        $this->my_model->setJoin(array(
            'table' => array('tbl_deductions','tbl_wage_type','tbl_login_sheet'),
            'join_field' => array('staff_id','id','staff_id'),
            'source_field' => array('tbl_staff.id','tbl_staff.wage_type','tbl_staff.id'),
            'type' => 'left'
        ));
        $deductions = ArrayWalk(array(
            'flight_deduct','flight_debt',
            'visa_deduct','visa_debt',
            'accommodation','transport'
        ),'tbl_deductions.');
        $staff = ArrayWalk(array(
            'id','tax_number','installment','balance', 'balance as start_balance',
            'nz_account','account_two','date_employed'
        ),'tbl_staff.');

        $fields = array_merge($deductions,$staff);
        $fields[] = 'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name';
        $fields[] = 'tbl_wage_type.type as wage_type';
        $fields[] = 'CONCAT(YEAR(time_in), "-", (WEEK(time_in,1) + 1)) as w';
        $fields[] = 'UNIX_TIMESTAMP (date) as t';
        $fields[] = '(WEEK(time_in,1) + 1) as _week';
        $fields[] = 'DATE_FORMAT(date,"%Y") as _year';
        $fields[] = 'MAX(date) as _date';
        $this->my_model->setSelectFields($fields);

        if($id){
            $whatVal = 'UNIX_TIMESTAMP (date) < " '. $date .' " AND tbl_login_sheet.staff_id ="' . $id . '"';
        }else{
            $whatVal = 'UNIX_TIMESTAMP (date) < " '. $date .' " AND has_loans = 1';
        }

        $this->my_model->setOrder(array('_year','_week'));
        $this->my_model->setGroupBy(array('tbl_staff.id','w'));
        $d = $this->my_model->getInfo('tbl_staff',$whatVal,'');
        $total_bal = array();
        $total_visa_deduct = array();
        $total_flight_deduct = array();
        $total_installment = array();
        if(count($d) > 0){
            foreach($d as $val){
                @$total_visa_deduct[$val->id] += $val->visa_deduct;
                @$total_flight_deduct[$val->id] += $val->flight_deduct;
                @$total_installment[$val->id] += $val->installment;

                $visa_debt = ($val->visa_debt - @$total_visa_deduct[$val->id]);
                $flight_debt = ($val->flight_debt - @$total_flight_deduct[$val->id]);
                $loans = ($val->balance - @$total_installment[$val->id]);
                if($visa_debt >= 0 || $flight_debt >= 0 || $loans >= 0){
                    $total_bal[$val->id][date('Y',strtotime($val->_date))][$val->_week] = array(
                        'visa_debt' => $visa_debt > 0 ? ($visa_debt >= $val->visa_deduct ? $visa_debt : $val->visa_deduct) : 0,
                        'flight_debt' => $flight_debt > 0 ? ($flight_debt >= $val->flight_deduct ? $flight_debt : $val->flight_deduct) : 0,
                        'balance' => $loans > 0 ? ($loans >= $val->installment ? $loans : $val->installment) : 0,
                        'start_balance' => $val->start_balance
                    );
                }
            }
        }
        return $total_bal;
    }

    function get_over_all_wage_total_pay($date,$id = '',$is_details = false){

        $this_year = date('Y',strtotime($date));
        $first_tuesday_in_month = $this_year.'-04-01';//$this->first_day_of_month($this_year);
        $year = strtotime($first_tuesday_in_month) > strtotime($date) ? date('Y-m-d',strtotime('-1 year'.$date)) : date('Y-m-d',strtotime($date));


        $this->my_model->setJoin(array(
            'table' => array('tbl_deductions','tbl_wage_type','tbl_login_sheet'),
            'join_field' => array('staff_id','id','staff_id'),
            'source_field' => array('tbl_staff.id','tbl_staff.wage_type','tbl_staff.id'),
            'type' => 'left'
        ));
        $deductions = ArrayWalk(array(
            'flight_deduct','flight_debt',
            'visa_deduct','visa_debt',
            'accommodation','transport'
        ),'tbl_deductions.');
        $staff = ArrayWalk(array(
            'id','tax_number','installment','balance',
            'nz_account','account_two','date_employed'
        ),'tbl_staff.');

        $fields = array_merge($deductions,$staff);
        $fields[] = 'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name';
        $fields[] = 'tbl_wage_type.type as wage_type';
        $fields[] = 'CONCAT(YEAR(time_in), "-", (WEEK(time_in,1) + 1)) as w';
        $fields[] = 'UNIX_TIMESTAMP (date) as t';
        $fields[] = '(WEEK(time_in,1) + 1) as _week';
        $fields[] = 'DATE_FORMAT(date,"%Y") as _year';
        $fields[] = 'MAX(date) as _date';
        $fields[] = 'IF(time_out != "" AND time_in != "",
                        SUM((TIMESTAMPDIFF(SECOND, time_in, time_out) / 3600) - IF(TIME(DATE_FORMAT(time_out,"%H:%i:%s")) > MAKETIME(13,30,0), 0.50, 0))
                        , 0) as hours';
        $fields[] = 'time_in';
        $fields[] = 'time_out';
        $fields[] = 'date';
        $fields[] = 'tbl_login_sheet.id as dtr_id';

        $this->my_model->setSelectFields($fields);

        if($id){
            $whatVal = 'date >= CURDATE() - INTERVAL 4 WEEK AND staff_id ="'.$id.'"';
        }else{
            $whatVal = 'date >= CURDATE() - INTERVAL 4 WEEK';
        }

        $this->my_model->setOrder(array('_year','_week'));
        $this->my_model->setGroupBy(array('tbl_staff.id','w'));
        $d = $this->my_model->getInfo('tbl_staff',$whatVal,'');
        DisplayArray($d);exit;
        $wage_total_data = array();
        $wage_data = new Wage_Controller();
        $this->data['total_bal'] = $wage_data->get_year_total_balance();

        $_date = new DateTime($key);
        $_year = $_date->format('Y');
        $_week = $_date->format('W');
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
                    $ev->m_paye = 0;
                    $ev->me_paye = 0;
                    $ev->kiwi_ = 0;
                    $ev->st_loan = 0;

                    $hours = $ev->wage_type != 1 ? $this->getTotalHours($key,$ev->employee) : 1;
                    $ev->gross = $ev->rate_cost * $hours;
                    $ev->gross_ = $ev->gross != 0 ? floatval(number_format($ev->gross,2,'.','')):'0.00';

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

                    $ev->flight_debt = @$this->data['total_bal'][$ev->employee][$_year][$_week]['flight_debt'];
                    $ev->flight_deduct = $ev->flight_debt > 0 ?
                        ($ev->flight_debt <= $ev->flight_deduct ? $ev->flight_debt : $ev->flight_deduct) : 0;

                    $ev->visa_debt = @$this->data['total_bal'][$ev->employee][$_year][$_week]['visa_debt'];
                    $ev->visa_deduct = $ev->visa_debt > 0 ?
                        ($ev->visa_debt <= $ev->visa_deduct ? $ev->visa_debt : $ev->visa_deduct) : 0;

                    $ev->balance = @$this->data['total_bal'][$ev->employee][$_year][$_week]['balance'];
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

        return $wage_total_data;
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
        $fields =ArrayWalk($this->my_model->getFields('tbl_staff_rate',array('id')),'tbl_staff_rate.');
        $fields[] = 'tbl_rate.rate_cost';
        $fields[] = 'tbl_rate.rate_name';
        $this->my_model->setSelectFields($fields);
        $rate = $this->my_model->getInfo('tbl_staff_rate',$whatVal,$whatFld);

        return $rate;
    }

    function get_staff_total_hours($year,$month,$week = '',$id = '',$action = 'weekly'){
        $totalHours = array();
        $hours_gain = array();

        $start_fin_month = $year.'-04-01';
        $start_fin_day = date('N',strtotime($start_fin_month));
        $end_fin_month = $year.'-03-31';
        $end_fin_day = date('N',strtotime($end_fin_month));
        $last_day_of_march = last_day_of_month($year);
        $get_week = getWeekDateInMonth($year,$month);

        $week_data = StartWeekNumber($week,$year);
        $dt = new DateTime;
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $this->my_model->setSelectFields(array(
            'IF(time_out != "" AND time_in != "", TIMESTAMPDIFF(SECOND, time_in, time_out) , 0) as hours',
            'time_in','time_out','staff_id','date',
            'id as dtr_id','working_type_id'
        ));
        $whatVal = array($year);
        $whatFld = array('YEAR(date)=');
        if($week){
            $whatVal[] = $week;
            $whatFld[] = 'WEEK(date,1) =';
        }
        if($month){
            $whatVal[] = $month;
            $whatFld[] = 'MONTH(date) =';
        }
        if($id){
            $whatVal[] = $id;
            $whatFld[] = 'tbl_login_sheet.staff_id';
        }
        $dtr = $this->my_model->getinfo('tbl_login_sheet', $whatVal,$whatFld);
        $staff_id = array();
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
                $staff_id[$dv->staff_id] = $dv->staff_id;
            }
        }

        $date = $get_week[$week];
        $_start_day = $start_fin_month == $date ? $start_fin_day : $week_data['start_day'];

        $_end_day = $date == $last_day_of_march ? $end_fin_day : $week_data['end_day'];
        switch($action){
            case 'weekly':
                for($whatDay=$_start_day; $whatDay<=$_end_day; $whatDay++){
                    $getDate =  $dt->setISODate($year, $week , $whatDay)->format('Y-m-d');
                    $day = date('Y-m-d', strtotime($getDate));
                    if(count($staff_id) > 0){
                        foreach($staff_id as $val){
                            $thisDtr = array_key_exists($val, $hours_gain) ? $hours_gain[$val] : array();
                            if(count($thisDtr) > 0){
                                $hasInfo = array_key_exists($day, $thisDtr);

                                if($hasInfo){
                                    $thisTime = $thisDtr[$day];
                                    @$totalHours[$val] += @$thisTime;
                                }
                            }
                        }
                    }
                }
                break;
            default:
                for($whatDay=1; $whatDay<=$num; $whatDay++){
                    //$whatDate = $year.'-'.$month.'-'.$whatDay;
                    $date = mktime(0, 0, 0, $month,$whatDay,$year);
                    $thisDate = date('Y-m-d',$date);

                    if(count($staff_id) > 0){
                        foreach($staff_id as $val){
                            $thisDtr = array_key_exists($val, $hours_gain) ? $hours_gain[$val] : array();
                            if(count($thisDtr) > 0){
                                $hasInfo = array_key_exists($thisDate, $thisDtr);

                                if($hasInfo){
                                    $thisTime = $thisDtr[$thisDate];
                                    @$totalHours[$val] += @$thisTime;
                                }
                            }
                        }
                    }
                }
                break;
        }

        $hoursValue = array();
        if(count($totalHours) > 0){
            foreach($totalHours as $key=>$val){
                $hoursValue[$key] = number_format((@$val/3600),2);
            }
        }

        return $hoursValue;
    }

}