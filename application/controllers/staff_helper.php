<?php

class Staff_Helper extends CI_Controller{

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
        $whatVal = '';
        $whatFld = '';
        $dtr = $this->my_model->getinfo('tbl_login_sheet');
        $data = array();

        if(count($dtr) > 0){
            foreach($dtr as $row){
                $data[$row->staff_id][] = array(
                    'hours' => $row->hours,
                    'hours_' => $row->hours_,
                    'time_in' => $row->time_in,
                    'time_out' => $row->time_out,
                    'date' => $row->date,
                    'dtr_id' => $row->dtr_id,
                    'day' => $row->day,
                    'week_year' => $row->week_year,
                    'week' => $row->w,
                    'working_type_id' => $row->working_type_id
                );
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

    /*function staff_kiwi(){

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
    }*/


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
                    'rate' => $row->rate,
                    'date_added' => $row->date_added,
                    'start_use' => $row->start_use,
                    'end_use' => $row->end_use
                );
            }
        }

        return $rate_data;
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
}