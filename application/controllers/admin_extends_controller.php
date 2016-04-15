<?php

include('admin_controller.php');

class Admin_Extends_Controller extends Admin_Controller{

    function payPeriodSummaryReportReview(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $staff_id = $this->uri->segment(2);

        if(!$staff_id){
            exit;
        }

        $year = isset($_GET['year']) ? $_GET['year'] : '';
        $month = isset($_GET['month']) ? $_GET['month'] : '';
        $week = isset($_GET['week']) ? $_GET['week'] : '';

        $this->getWageData($year,$month,$week,'weekly','',$staff_id);

        $this->load->view('backend/dtr/pay_period_summary_report_review', $this->data);
    }

    function topUpHours(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            if(
                count($_POST['topup_hours']) > 0
                || count($_POST['notes']) > 0
            ){
                foreach($_POST['topup_hours'] as $k=>$v){
                    $ref = $k + 1;
                    $whatVal = array($_POST['staff_id'],$_POST['week_number'],$_POST['date'],$ref);
                    $whatFld = array('staff_id','week_num','date','reference');

                    $record_exist = $this->my_model->getInfo('tbl_topup_hours',$whatVal,$whatFld);

                    if($v){
                        $post = array(
                            'topup_hours' => $v,
                            'staff_id' => $_POST['staff_id'],
                            'date' => $_POST['date'],
                            'reference' => $k + 1,
                            'notes' => $_POST['notes'][$k],
                            'week_num' => $_POST['week_number'],
                        );

                        if(count($record_exist) > 0){
                            foreach($record_exist as $val){
                                $this->my_model->update('tbl_topup_hours',$post,$val->id);
                            }
                        }
                        else{
                            $this->my_model->insert('tbl_topup_hours',$post,false);
                        }
                    }
                    else{
                        if(count($record_exist) > 0){
                            foreach($record_exist as $val){
                                $post = array(
                                    'topup_hours' => 0,
                                    'notes' => ''
                                );
                                $this->my_model->update('tbl_topup_hours',$post,$val->id);
                            }
                        }
                    }
                }
            }

        }else{
            $staff_id = $this->uri->segment(2);

            if(!$staff_id){
                exit;
            }

            $date = new DateTime();
            $week = isset($_POST['week']) ? $_POST['week'] : $date->format('W');
            $year = isset($_POST['year']) ? $_POST['year'] : $date->format('Y');
            $month = isset($_POST['month']) ? $_POST['month'] : $date->format('m');

            $this_week = getWeekDateInMonth($year,$month);
            $this->data['id'] = $staff_id;
            $this->data['date'] = $this_week[$week];
            $this->data['week'] = $week;

            $whatVal = array($staff_id,$week,$this->data['date']);
            $whatFld = array('staff_id','week_num','date');
            $this->data['topup_hours'] = $this->my_model->getInfo('tbl_topup_hours',$whatVal,$whatFld);

            $this->load->view('backend/dtr/top_up_hours_view', $this->data);
        }
    }

    function annualLeaveLumpSum(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
        $staff_data = new Staff_Helper();
        $hours_data = $staff_data->staff_total_hours();
        //DisplayArray($hours_data);exit;
        $this->data['page_load'] = 'backend/staff/annual_leave_lump_sum_view';
        $this->load->view('main_view',$this->data);
    }

}