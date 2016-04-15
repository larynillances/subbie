<?php

class Staff_Helper extends CI_Controller{

    var $settings;

    function index(){
        DisplayArray($this->hours_rendered());
    }

    function hours_rendered($type = 'weekly'){
        $this->my_model->setSelectFields(array(
            'IF(time_out != "" AND time_in != "",
                SUM((TIMESTAMPDIFF(SECOND, time_in, time_out) / 3600) - IF(TIME(DATE_FORMAT(time_out,"%H:%i:%s")) > MAKETIME(13,30,0), 0.50, 0))
                    , 0) as hours',
            'time_in','time_out','staff_id','date',
            'SUM((TIMESTAMPDIFF(SECOND, time_in, time_out) / 3600)) as hours_',
            'id as dtr_id','working_type_id','(WEEK(date,2) + 1) as w',
            'CONCAT(DATE_FORMAT(date,"%Y"),"-",(WEEK(date,2) + 1)) as week_year',
            'DATE_ADD(date, INTERVAL(7-DAYOFWEEK(date)) DAY) as day'
        ));

        $this->my_model->setGroupBy(array('staff_id','week_year'));
        $this->my_model->setOrder('week_year');

        $dtr = $this->my_model->getinfo('tbl_login_sheet');
        $data = array();

        if(count($dtr) > 0){
            foreach($dtr as $row){
                $data[$row->staff_id][] = $row;
            }
        }
        return $data;
    }

    function staff_details($what_val = '',$what_fld = ''){
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_rate',
                'tbl_wage_type',
                'tbl_currency',
                'tbl_tax_codes',
                'tbl_staff_rate',
                'tbl_staff_nz_rate',
                'tbl_hourly_nz_rate',
                'tbl_deductions',
                'tbl_kiwi as employee',
                'tbl_kiwi as employer',
                'tbl_esct_rate',
                'tbl_salary_freq',
                'tbl_salary_type',
                'tbl_staff_status',
                'tbl_project_type'
            ),
            'join_field' => array(
                'id', 'id',
                'id','id',
                'staff_id','staff_id',
                'id','staff_id',
                'id','id','id','id','id','id','id'
            ),
            'source_field' => array(
                'tbl_staff.rate',
                'tbl_staff.wage_type',
                'tbl_staff.currency',
                'tbl_staff.tax_code_id',
                'tbl_staff.id',
                'tbl_staff.id',
                'tbl_staff_nz_rate.hourly_nz_rate_id',
                'tbl_staff.id',
                'tbl_staff.kiwi_id',
                'tbl_staff.employeer_kiwi',
                'tbl_staff.esct_rate_id',
                'tbl_wage_type.frequency',
                'tbl_wage_type.type',
                'tbl_staff.status_id',
                'tbl_staff.project_id'
            ),
            'type' => 'left',
            'join_append' => array(
                'tbl_rate',
                'tbl_wage_type',
                'tbl_currency',
                'tbl_tax_codes',
                'tbl_staff_rate',
                'tbl_staff_nz_rate',
                'tbl_hourly_nz_rate',
                'tbl_deductions',
                'employee',
                'employer',
                'tbl_esct_rate',
                'tbl_salary_freq',
                'tbl_salary_type',
                'tbl_staff_status',
                'tbl_project_type'
            )
        ));
        $fields = ArrayWalk($this->my_model->getFields('tbl_staff'),'tbl_staff.');
        $fields[] = 'tbl_rate.rate_name';
        $fields[] = 'tbl_wage_type.description';
        $fields[] = 'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name';
        $fields[] = 'IF(tbl_tax_codes.tax_code != "" , tbl_tax_codes.tax_code, "") as tax_code';
        $fields[] = 'IF(tbl_staff.ird_num != "-" ,LPAD(tbl_staff.ird_num,11,"0"),tbl_staff.ird_num) as ird_num';
        $fields[] = 'tbl_wage_type.type as wage_type';
        $fields[] = 'tbl_wage_type.frequency as frequency_id';
        $fields[] = 'tbl_salary_freq.frequency';
        $fields[] = 'tbl_salary_freq.code as frequency_code';
        $fields[] = 'tbl_salary_type.type as salary_type';
        $fields[] = 'tbl_salary_type.code as salary_code';
        $fields[] = 'tbl_esct_rate.field_name';
        $fields[] = 'tbl_esct_rate.cec_name';
        $fields[] = 'tbl_esct_rate.esct_rate';
        $fields[] = 'employee.kiwi';
        $fields[] = 'employer.kiwi as emp_kiwi';
        $fields[] = 'employer.kiwi as employeer_kiwi';
        $fields[] = 'tbl_tax_codes.has_st_loan';
        $fields[] = 'IF(tbl_staff.date_employed != "0000-00-00" ,DATE_FORMAT(tbl_staff.date_employed,"%d-%m-%Y"),"") as date_employed';
        $fields[] = 'tbl_currency.currency_code';
        $fields[] = 'CONCAT("$",tbl_rate.rate_cost) as rate_cost';
        $fields[] = 'tbl_currency.symbols';
        $fields[] = 'IF(tbl_tax_codes.tax_code !="",tbl_tax_codes.tax_code,"") as tax_code';
        $fields[] = 'IF('.'tbl_staff.ird_num != "-" ,LPAD('.'tbl_staff.ird_num,11,"0"),'.'tbl_staff.ird_num) as ird_num';
        $fields[] = 'tbl_staff_rate.start_use';
        $fields[] = 'tbl_hourly_nz_rate.hourly_rate';
        $fields[] = 'tbl_deductions.flight_deduct';
        $fields[] = 'tbl_deductions.flight_debt';
        $fields[] = 'tbl_deductions.visa_deduct';
        $fields[] = 'tbl_deductions.visa_debt';
        $fields[] = 'tbl_deductions.accommodation';
        $fields[] = 'tbl_deductions.transport';
        $fields[] = 'tbl_deductions.id as deduction_id';
        $fields[] = 'tbl_staff_status.staff_status';
        $fields[] = 'tbl_staff_status.color';
        $fields[] = 'tbl_tax_codes.field_code_name as field_code';
        $fields[] = 'tbl_project_type.project_name';
        $fields[] = 'IF(tbl_currency.symbols = "₱","Php",tbl_currency.symbols) as symbols';

        $this->my_model->setSelectFields($fields);

        $this->my_model->setGroupBy('tbl_staff.id');
        $this->my_model->setOrder(array('lname','fname'));

        $this->settings = $this->my_model->model_config;

        $data = $this->my_model->getinfo('tbl_staff',$what_val,$what_fld);

        $rate = $this->staff_rate();

        if(count($data)>0){
            foreach($data as $v){
                if(count(@$rate[$v->id]) > 0){
                    foreach(@$rate[$v->id] as $row){
                        $v->rate_name = $row->rate_name;
                        $v->rate_cost = '$'.$row->rate;;
                    }
                }

                switch($v->currency_code){
                    case 'NZD':
                        $v->rate = '$1';
                        break;
                    default:
                        $converted_amount = CurrencyConverter($v->currency_code);
                        $v->rate = $v->symbols.' '.$converted_amount;
                        break;
                }
            }
        }

        return $data;
    }

    function staff_kiwi(){

        $this->my_model->setJoin(array(
            'table' => array('tbl_kiwi as employee','tbl_kiwi as employer_kiwi','tbl_esct_rate'),
            'join_field' => array('id','id','id'),
            'source_field' => array('tbl_staff_kiwi.kiwi_id','tbl_staff_kiwi.employer_kiwi','tbl_staff_kiwi.esct_rate_id'),
            'type' => 'left',
            'join_append' => array(
                'employee','employer_kiwi','tbl_esct_rate'
            )
        ));
        $fields = ArrayWalk($this->my_model->getFields('tbl_staff_kiwi',array('id')),'tbl_staff_kiwi.');
        $fields[] = 'employee.kiwi';
        $fields[] = 'employer_kiwi.kiwi as employer_kiwi';
        $fields[] = 'tbl_esct_rate.field_name';
        $fields[] = 'tbl_esct_rate.cec_name';
        $fields[] = 'tbl_esct_rate.esct_rate';
        $this->my_model->setSelectFields($fields);
        $this->my_model->setOrder('date_start','DESC');
        $rate = $this->my_model->getInfo('tbl_staff_kiwi');

        $kiwi_data = array();

        if(count($rate) > 0){
            foreach($rate as $row){
                $kiwi_data[$row->staff_id][$row->date_start] = (object)array(
                    'kiwi' => $row->kiwi,
                    'employer_kiwi' => $row->employer_kiwi,
                    'field_name' => $row->field_name,
                    'cec_name' => $row->cec_name,
                    'esct_rate' => $row->esct_rate,
                    'date_end' => $row->date_end,
                    'date_start' => $row->date_start
                );
            }
        }

        return $kiwi_data;
    }

    function staff_rate(){

        $this->my_model->setJoin(array(
            'table' => array('tbl_rate'),
            'join_field' => array('id'),
            'source_field' => array('tbl_staff_rate.rate_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk($this->my_model->getFields('tbl_staff_rate'),'tbl_staff_rate.');
        $fields[] = 'tbl_rate.rate_cost';
        $fields[] = 'tbl_rate.rate_name';
        $this->my_model->setSelectFields($fields);
        $this->my_model->setOrder('end_use','DESC');
        $rate = $this->my_model->getInfo('tbl_staff_rate');

        $rate_data = array();

        if(count($rate) > 0){
            foreach($rate as $row){
                $rate_data[$row->staff_id][$row->start_use] = (object)array(
                    'id' => $row->id,
                    'rate_cost' => $row->rate_cost,
                    'rate_name' => $row->rate_name,
                    'rate' => $row->rate_cost,
                    'date_added' => $row->date_added,
                    'start_use' => $row->start_use,
                    'end_use' => $row->end_use
                );
            }
        }

        return $rate_data;
    }

    function staff_total_hours($id = '',$year_month = '',$week_year = '', $start_week_range = '',$end_week_range = '',$is_yearly = false){
        $whatVal = array();
        $whatFld = array();
        if($id){
            $whatVal[] = $id;
            $whatFld[] = 'staff_id';
        }
        if($week_year){
            $whatVal[] = $week_year;
            $whatFld[] = 'week_year';
        }
        if($start_week_range && $end_week_range){
            $whatVal[] = 'date >= "' . $start_week_range .'" AND date <= "' . $end_week_range .'"';
            $whatFld[] = '';
        }
        if($year_month){
            $whatVal[] = $year_month;
            $whatFld[] = 'EXTRACT(YEAR_MONTH FROM date) =';
        }

        $fld = array(
            'SUM(
                IF(time_out != "" AND time_in != "",
                    FORMAT(TIMESTAMPDIFF(SECOND, time_in, time_out) / 3600 - (
                        IF(
                            TIME(DATE_FORMAT(time_in ,  "%H:%i:%s" )) <= MAKETIME( 12, 00, 0 ) AND TIME(DATE_FORMAT(time_out ,  "%H:%i:%s" )) >= MAKETIME( 13, 30, 0 ),
                            0.50,
                            0)
                        ),
                    2),
                0)
            ) as hours',
            'time_in','time_out','staff_id','date',
            'tbl_login_sheet.id as dtr_id','working_type_id','week_year'
        );

        if($is_yearly){
            $this->my_model->setJoin(array(
                'table' => array(
                    'tbl_staff',
                    'tbl_tax_codes',
                    'tbl_wage_type'
                ),
                'join_field' => array(
                    'id', 'id', 'id'
                ),
                'source_field' => array(
                    'tbl_login_sheet.staff_id',
                    'tbl_staff.tax_code_id',
                    'tbl_staff.wage_type'
                ),
                'type' => 'left'
            ));
            $fld[] = 'tbl_wage_type.frequency as frequency_id';
            $fld[] = 'tbl_tax_codes.field_code_name as field_code';
        }

        $this->my_model->setSelectFields($fld);

        $this->my_model->setOrder(array('staff_id','date'));
        $this->my_model->setGroupBy(array('staff_id','week_year'));
        $dtr = $this->my_model->getinfo('tbl_login_sheet', $whatVal,$whatFld);

        $data = array();
        $arr = array();

        $rate = $this->staff_rate();

        if(count($dtr) > 0){
            foreach($dtr as $v){
                $v->days = $v->staff_id != 4 ? 5 : 6;
                $v->rate_cost = 0;
                if(count(@$rate[$v->staff_id]) > 0){
                    foreach(@$rate[$v->staff_id] as $used_date=>$val){
                        if(strtotime($used_date) <= strtotime($v->date)){
                            $v->rate_name = $val->rate_name;
                            $v->rate_cost = $val->rate;
                            $v->start_use = $val->start_use;
                        }
                    }
                }

                $v->gross = ($v->hours * $v->rate_cost);

                @$arr[$v->staff_id]['work_weeks'] += $v->hours > 0 ? 1 : 0;
                @$arr[$v->staff_id]['hours'] += $v->hours;
                @$arr[$v->staff_id]['rate'] += $v->gross;
                $work_week = $arr[$v->staff_id]['work_weeks'];

                $v->weeks = $work_week > 52 ? 52 : $work_week;
                $v->total_hours = $arr[$v->staff_id]['hours'];
                $v->total_rate = $arr[$v->staff_id]['rate'];

                if($v->hours > 0){
                    $v->daily_hours = ($v->total_hours / $v->weeks) / $v->days;
                    $v->daily_gross = ($v->total_rate / $v->weeks) / $v->days;
                    $v->ave_gross = ($v->total_rate / $v->weeks);
                    $v->ave_hours = ($v->total_hours / $v->weeks);
                    $v->formula_daily_gross = '(' . $v->total_rate . ' / ' . $v->weeks . ') / ' . $v->days;
                }

                if($is_yearly){
                    $data[$v->staff_id] = $v;
                }
                else{
                    $data[$v->staff_id][$v->week_year] = $v;
                }
            }
        }

        return $data;
    }

    function staff_employment($desc = false){
        if($desc){
            $this->my_model->setOrder('date_employed','DESC');
        }
        $employment = $this->my_model->getInfo('tbl_staff_employment');

        $employment_data = array();

        if(count($employment) > 0){
            foreach($employment as $row){
                $employment_data[$row->staff_id][$row->date_employed] = (object)array(
                    'id' => $row->id,
                    'date_employed' => $row->date_employed,
                    'unemployed_date' => $row->unemployed_date,
                    'date_last_pay' => $row->date_last_pay,
                    'termination_type' => $row->termination_type,
                    'has_final_pay' => $row->has_final_pay,
                    'last_week_pay' => $row->last_week_pay
                );
            }
        }

        return $employment_data;
    }

    function stat_holiday($year = '',$month = '',$week = ''){
        $whatValue = array(3);
        $whatFld = array('type !=');
        $sql_val = '';
        if($year){
            $whatValue[] = $year;
            $whatFld[] = 'YEAR(tbl_holiday.date) =';
        }
        if($week){
            $last_week = mktime(0, 0, 0, $month, 0, $year) - (7*3600*24);

            $_week = new DateTime(date('Y-m-d',$last_week));
            $sql_val .= "(WEEK(tbl_holiday.date) <= '" . $week ."' AND YEAR(tbl_holiday.date) ='" . $year . "')";
            $sql_val .= " OR (WEEK(tbl_holiday.date) >= '" . $_week->format('W')."' AND YEAR(tbl_holiday.date) ='".$_week->format('Y')."')";
        }

        if($month){
            $last_month = mktime(0, 0, 0, $month, 0, $year) - ((30*3600*24));
            $sql_val .= $week ? 'AND' : '';
            $sql_val .= "(MONTH(tbl_holiday.date) <= '" . $month ."' AND YEAR(tbl_holiday.date) ='" . $year . "')";
            $sql_val .= " OR (MONTH(tbl_holiday.date) >= '" . date('m',$last_month) ."' AND YEAR(tbl_holiday.date) ='".date('Y',$last_month)."')";
        }

        $whatValue[] = $sql_val;
        $whatFld[] = '';
        $holiday = $this->my_model->getInfo('tbl_holiday',$whatValue,$whatFld);

        $data = array();
        if(count($holiday) > 0){
            foreach($holiday as $val){
                $data[$val->date] = $val;
            }
        }
        return $data;
    }

    function top_up_hours($year,$month = '',$week_number = ''){
        $whatValue = array($year,$week_number);
        $whatFld = array('YEAR(date) =','week_num');
        $sql_val = '';
        if($month){
            $last_month = mktime(0, 0, 0, $month, 0, $year) - ((30*3600*24));
            $sql_val .= "(MONTH(date) <= '" . $month ."' AND YEAR(date) ='" . $year . "')";
            $sql_val .= " OR (MONTH(date) >= '" . date('m',$last_month) ."' AND YEAR(date) ='".date('Y',$last_month)."')";
        }
        if($week_number){
            $whatValue[] = $week_number;
            $whatFld[] = 'week_num';
        }

        $whatValue[] = $sql_val;
        $whatFld[] = '';

        $adjustment = $this->my_model->getInfo('tbl_topup_hours',$whatValue,$whatFld);
        $data = array();
        $total = array();
        if(count($adjustment) > 0){
            foreach($adjustment as $val){
                @$total[$val->staff_id][$val->date] += floatval($val->topup_hours);
                $val->total = @$total[$val->staff_id][$val->date];
                $data[$val->staff_id][$val->date] = (Object)$val;
            }
        }

        return $data;
    }

    function adjustment($year,$month = '',$week_number = ''){
        $whatValue = array($year,$week_number);
        $whatFld = array('YEAR(date) =','week_number');
        $sql_val = '';
        if($month){
            $last_month = mktime(0, 0, 0, $month, 0, $year) - ((30*3600*24));
            $sql_val .= "(MONTH(date) <= '" . $month ."' AND YEAR(date) ='" . $year . "')";
            $sql_val .= " OR (MONTH(date) >= '" . date('m',$last_month) ."' AND YEAR(date) ='".date('Y',$last_month)."')";
        }
        if($week_number){
            $whatValue[] = $week_number;
            $whatFld[] = 'week_number';
        }

        $whatValue[] = $sql_val;
        $whatFld[] = '';

        $this->my_model->setJoin(array(
            'table' => array('tbl_adjustment_type'),
            'join_field' => array('id'),
            'source_field' => array('tbl_adjustment.adjustment_type_id'),
            'type' => 'left'
        ));
        $fld = ArrayWalk($this->my_model->getFields('tbl_adjustment'),'tbl_adjustment.');
        $fld[] = 'tbl_adjustment_type.adjustment_type';
        $fld[] = 'tbl_adjustment_type.adjustment_code';

        $this->my_model->setSelectFields($fld);
        $adjustment = $this->my_model->getInfo('tbl_adjustment',$whatValue,$whatFld);
        $data = array();
        $total = array();
        if(count($adjustment) > 0){
            foreach($adjustment as $val){
                @$total[$val->staff_id][$val->date][$val->adjustment_type_id] += floatval(str_replace('-','',$val->amount));
                $val->total_debit = @$total[$val->staff_id][$val->date][1];
                $val->total_credit = @$total[$val->staff_id][$val->date][2];
                $val->total = $val->total_credit - $val->total_debit;
                $val->code = $val->total > 0 ? 'CR' : 'DR';
                $val->type_id = $val->total > 0 ? 2 : 1;
                $data[$val->staff_id][$val->date] = (Object)$val;
            }
        }

        return $data;
    }

    function staff_leave_application($whatVal = '',$whatFld = '',$by_week = false, $first_week_only = false,$is_sort = false,$has_limit = false){
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_leave_decision',
                'tbl_leave_type',
                'tbl_staff',
                'tbl_day_type'
            ),
            'join_field' => array(
                'id','id','id','id'
            ),
            'source_field' => array(
                'tbl_leave.decision',
                'tbl_leave.type',
                'tbl_leave.user_id',
                'tbl_leave.leave_range'
            ),
            'type' => 'left'
        ));
        $fld =  ArrayWalk($this->my_model->getFields('tbl_leave'),'tbl_leave.');
        $fld[] = 'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name';
        $fld[] = 'tbl_leave_type.type as leave_type';
        $fld[] = 'tbl_leave_decision.decision as decision_type';
        $fld[] = 'WEEK(leave_start, 1 ) as week';
        $fld[] = 'tbl_day_type.holiday_type as day_type';
        $fld[] = 'tbl_day_type.id as range_type';
        $fld[] = 'tbl_day_type.day_number';
        $fld[] = 'tbl_day_type.hours';

        $this->my_model->setSelectFields($fld);
        if(!$whatVal && !$whatFld){
            $whatVal = '';//array(1);
            $whatFld = '';//array('tbl_leave.decision');
        }
        if($by_week){
            $this->my_model->setGroupBy(array('id','week'));
        }
        if($is_sort){
            $this->my_model->setOrder('leave_start');
        }
        if($has_limit){
            $this->my_model->setConfig(5,0,true);
        }
        $leave = $this->my_model->getInfo('tbl_leave',$whatVal,$whatFld);

        $leave_ = array();
        $leave_data = array();
        if(count($leave) > 0){
            foreach($leave as $row){
                $date = createDateRangeArray($row->leave_start,$row->leave_end);
                $holiday = $this->subbie_date_helper->holidays;
                $leave_days_count = $this->getLeaveDaysCount($row->leave_start,$row->leave_end,$holiday);

                if(count($date) > 0){
                    foreach($date as $val){
                        $cal_hours = $row->hours * 3600;
                        $date_ = new DateTime($row->leave_start);

                        $data = (object)array(
                            'id' => $row->id,
                            'date_requested' => $row->date_requested,
                            'date_decision' => $row->date_decision,
                            'leave_start' => $row->leave_start,
                            'leave_end' => $row->leave_end,
                            'week' => $date_->format('W'),
                            'user_id' => $row->user_id,
                            'type' => $row->type,
                            'leave_type' => $row->leave_type,
                            'day_type' => $row->day_type,
                            'range_type' => $row->range_type,
                            'leave_in_seconds' => $row->type != 6 ? $cal_hours : 0,
                            'leave_in_hours' => $row->type != 6 ? $row->hours : 0,
                            'reason_request' => $row->reason_request,
                            'reason_decision' => $row->reason_decision,
                            'decision' => $row->decision_type,
                            'days' => $leave_days_count
                        );

                        if($by_week){
                            $leave_[$row->user_id][$date_->format('W-Y')] = $data;
                        }
                        else{
                            $leave_[$row->user_id][$val] = $data;
                        }
                    }
                }
            }
        }

        if($first_week_only){
            if(count($leave_) > 0){
                foreach($leave_ as $id=>$val){
                    $ref = 1;
                    if(count($val) > 0){
                        foreach($val as $key=>$value){
                            if($ref == 1){
                                $leave_data[$id][$key] = (Object)$value;
                            }
                            $ref++;
                        }
                    }
                }
            }
        }

        return $first_week_only ? $leave_data : $leave_;
    }

    private function getLeaveDaysCount($start, $end, $holidays){
        $datesInBetween = createDateRangeArray($start, $end);
        if(count($holidays) > 0){
            foreach($holidays as $h){
                if(in_array($h, $datesInBetween)){
                    $key = array_search($h, $datesInBetween);
                    unset($datesInBetween[$key]);
                }
            }
        }
        $count = count($datesInBetween);

        $s = strtotime($start);
        $e = strtotime($end);
        $start_hour = date('H', $s);
        $end_hour = date('H', $e);
        if(in_array(date('Y-m-d'), $datesInBetween) && $start_hour >= 13){
            $count -= 0.5;
        }
        if(in_array(date('Y-m-d'), $datesInBetween) && $end_hour <= 12){
            $count -= 0.5;
        }

        return $count;
    }

    function staff_hourly_rate($whatVal = '',$whatFld = ''){
        $this->my_model->setJoin(array(
            'table' => array('tbl_hourly_nz_rate'),
            'join_field' => array('id'),
            'source_field' => array('tbl_staff_nz_rate.hourly_nz_rate_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk($this->my_model->getFields('tbl_staff_nz_rate',array('id')),'tbl_staff_nz_rate.');
        $fields[] = 'tbl_hourly_nz_rate.hourly_rate';
        $this->my_model->setSelectFields($fields);
        $rate = $this->my_model->getInfo('tbl_staff_nz_rate',$whatVal,$whatFld);

        $rate_data = array();

        if(count($rate) > 0){
            foreach($rate as $row){
                $rate_data[$row->staff_id][$row->date_used] = (object)array(
                    'hourly_rate' => $row->hourly_rate,
                    'hourly_nz_rate_id' => $row->hourly_nz_rate_id,
                    'date_used' => $row->date_used,
                    'date_end' => $row->date_end
                );
            }
        }

        return $rate_data;
    }

    function is_decimal( $val )
    {
        return is_numeric( $val ) && floor( $val ) != $val;
    }
}