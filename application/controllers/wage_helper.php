<?php

class Wage_Helper extends CI_Controller{

    function index(){

    }

    function get_sick_leave($id,$date,$default = 5){
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

    function get_annual_leave($id,$date,$default = 20){
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