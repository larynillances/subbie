<?php
include('subbie.php');

class Pay_Integrity_Controller extends Subbie{

    function __construct(){
        parent::__construct();
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
    }

    function payIntegrityCheck(){
        $this->data['page_load'] = 'backend/pay_integrity/pay_integrity_view';

        if(isset($_POST['submit'])){
            $this->session->set_userdata(array('pay_year' => $_POST['year']));
            redirect('payIntegrityCheck');
        }

        $this->data['_year'] = $this->session->userdata('pay_year') ? $this->session->userdata('pay_year') : date('Y');

        if(isset($_GET['json']) && $_GET['json'] == 1){
            $whatVal = $this->data['_year'];
            $whatFld = 'YEAR(pay_period) =';
            $this->my_model->setOrder('date_export','DESC');
            $exported_files = $this->my_model->getInfo('tbl_exported_files',$whatVal,$whatFld);
            $dir = realpath(APPPATH . '../json');
            if(count($exported_files) > 0){
                foreach($exported_files as $val){
                    $val->file_name = str_replace('.json','',$val->json_file);

                    $val->full_path = $dir . '/daily_hours/' . date('Y/F',strtotime($val->pay_period)) . '/' . $val->file_name . '.csv';

                    $val->uploaded_full_path = $dir . '/upload/' . date('Y/F',strtotime($val->pay_period)) . '/' . $val->file_name . '.csv';

                    $path = $val->is_uploaded ? 'upload' : 'daily_hours';

                    $val->file_path = base_url() . '/json/' . $path . '/' . date('Y/F',strtotime($val->pay_period)) . '/' . $val->file_name . '.csv';
                    $upload_btn = '<a href="#" class="tooltip-class" title="Upload" data-placement="left"><i class="fa fa-upload" id="'.  $val->id . '"></i></a>';
                    $download_btn = '<a href="' . $val->file_path . '" class="tooltip-class" title="Download" data-placement="left"><i class="fa fa-download" id="' . $val->id . '"></i></a>';

                    $_added_days = $val->week == 30 && date('Y',strtotime($val->pay_period)) == 2015 ? '+5 days ': '+6 days ';
                    $val->date_period = date('M d/y',strtotime($val->pay_period));
                    $val->date_period .= '-' . date('M d/y',strtotime($_added_days . $val->pay_period));
                    $val->date_export = date('Ymd His',strtotime($val->date_export));
                    $val->committed = '<div style="background: '.(!$val->is_committed ? '#c66363' : '#63c65c').';height: 30px;width: 120%;margin:-7px -5px 0;padding: 7px 0 0;">'.($val->is_committed ? 'Yes' : 'No').'</div>';
                    $val->uploaded = '<div style="background: '.(!$val->is_uploaded ? '#c66363' : '#63c65c').';height: 30px;width: 120%;margin:-7px -5px 0;padding: 7px 0 0;">'.($val->is_uploaded ? 'Yes' : 'No').'</div>';
                    $val->upload_btn = $val->is_uploaded != 1 ? $upload_btn : '';
                    $val->file_exist = file_exists($val->full_path);
                    $val->download_file = $val->is_uploaded ? (file_exists($val->uploaded_full_path) ? $download_btn : '') : (file_exists($val->full_path) ? $download_btn : '');
                }
            }
            echo json_encode($exported_files);
        }
        else if(isset($_GET['u']) && $_GET['u'] == 1) {
            if(isset($_POST['upload'])){
                $id = $this->uri->segment(2);
                $uploadDir = realpath(APPPATH . '../json/upload');
                if(!empty($_FILES)) {
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, TRUE);
                    }
                    $this->my_model->setShift();
                    $file_ = (Object)$this->my_model->getInfo('tbl_exported_files',$id);
                    $file_name_ = str_replace('.json','',$file_->json_file);
                    $dir = $uploadDir.'/'.date('Y/F',strtotime($file_->pay_period));
                    $file = $dir . '/' . $file_name_ . '.csv';

                    if(!is_dir($dir)){
                        mkdir($dir, 0777, TRUE);
                    }

                    if(move_uploaded_file($_FILES['file']['tmp_name'], $file)){
                        $post = array(
                            'uploader_id' => $this->session->userdata('user_id'),
                            'date_uploaded' => date('Y-m-d H:i:s'),
                            'is_uploaded' => 1
                        );

                        $this->my_model->update('tbl_exported_files',$post,$file_->id,'id',false);
                    }
                }
                redirect('payIntegrityCheck');
            }
            $this->load->view('backend/pay_integrity/upload_pay_integrity_view',$this->data);
        }
        else
        {
            $this->data['year'] = getYear();
            $this->load->view('main_view',$this->data);
        }
    }

    function exportPayValues(){//$week,$month,$year
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $week = $this->uri->segment(2);
        $month = $this->uri->segment(3);
        $year = $this->uri->segment(4);

        if(!$week && !$month && !$year){
            exit;
        }

        $data = array();

        $_week = getWeekDateInMonth($year,$month);
        $this->getWageData($year,$month);
        $date = @$_week[$week];
        $_date = @$this->data['date'][$week];
        $whatVal = 'WEEK(date , 1 ) =' . $week .' AND YEAR( date ) ='.$year;

        $data['dtr'] = $this->my_model->getInfo('tbl_login_sheet',$whatVal,'');

        $staff_data = new Staff_Helper();
        $rate = $staff_data->staff_rate();
        $stat_holiday = $staff_data->stat_holiday($year);

        $week_start = StartWeekNumber($week,$year);
        $_start_day = $week_start['start_day'];
        $_end_day = $week_start['end_day'];

        $dt = new DateTime();

        //region Staff
        $whatVal = 'project_id = "1"';
        $whatFld = '';
        $staff = $this->my_model->getinfo('tbl_staff',$whatVal,$whatFld);
        $data['staff'] = array();
        $employment_data = $staff_data->staff_employment();
        $leave_data_approved = $staff_data->staff_leave_application(array(1),array('tbl_leave.decision'));
        $leave_data_pending = $staff_data->staff_leave_application(array(0),array('tbl_leave.decision'));
        if(count($staff) > 0){
            foreach($staff as $ev){
                $week_value = @$_week[$week];
                if(count(@$employment_data[$ev->id]) > 0){
                    foreach(@$employment_data[$ev->id] as $used_date=>$val){
                        if(
                            strtotime($used_date) <= strtotime($week_value) ||
                            strtotime($used_date) <= strtotime(date('Y-m-d',strtotime('+6 days '.$week_value)))){
                            $ev->date_employed = $val->date_employed;
                            $ev->date_last_pay = $val->date_last_pay;
                            $ev->last_week_pay = $val->last_week_pay;
                            $ev->has_final_pay = $val->has_final_pay;
                        }
                    }
                }

                $date_employed = strtotime($ev->date_employed) <= strtotime($week_value) || strtotime($ev->date_employed) <= strtotime(date('Y-m-d',strtotime('+6 days '.$week_value)));
                $last_pay = $ev->date_last_pay != '0000-00-00' && strtotime($ev->date_last_pay) >= strtotime($week_value);
                $last_week_pay = $ev->last_week_pay && $ev->last_week_pay >= $week;
                $has_hours = $this->getTotalHours($week_value,$ev->id);

                if(($ev->date_employed != "0000-00-00" && $date_employed && $ev->status_id == 3)
                    || ($last_week_pay && $last_pay && $date_employed)
                    || ($date_employed && $last_pay)
                    || ($date_employed && $has_hours > 0 && $ev->status_id == 2)
                ){
                    $data['staff'][] = $ev;
                }
            }
        }

        for ($whatDay = $_start_day; $whatDay <= $_end_day; $whatDay++){
            $getDate = $dt->setISODate($year, $week, $whatDay)->format('Y-m-d');

            if(count($data['staff']) > 0){
                foreach($data['staff'] as $sv){
                    $sv->rate_cost = 0;
                    $rate_ = @$rate[$sv->id];
                    if(count($rate_) > 0){
                        foreach($rate_ as $start_use=>$rv){
                            if(strtotime($start_use) <= strtotime($getDate)){
                                $sv->rate_cost = $this->is_decimal($rv->rate_cost) ? number_format($rv->rate_cost,2,'.','') : $rv->rate_cost;
                            }
                        }
                    }
                    if(count(@$leave_data_approved[$sv->id][$getDate])){
                        $data['leave_approved'] = @$leave_data_approved[$sv->id][$getDate];
                    }

                    if(count(@$leave_data_pending[$sv->id][$getDate])){
                        $data['leave_pending'] = @$leave_data_pending[$sv->id][$getDate];
                    }
                }
            }
            if(count(@$stat_holiday[$getDate]) > 0){
                $data['stat_holiday'][] = @$stat_holiday[$getDate];
            }
        }
        //endregion
        $field = @$this->data['wage_data'][$_date];
        $array = array(
            'raw' => $data,
            'output' => $field
        );

        $period_ending = $week == 30 ? date('Ymd',strtotime('+5 days '.$date)) : date('Ymd',strtotime('+6 days '.$date));
        $date = $week == 30 ? date('Y-m-d',strtotime('+5 days '.$date)) : date('Y-m-d',strtotime('+6 days '.$date));
        $file = 'Week_' . $week . '_Pay_Period_Ending_'.$period_ending.'_Daily_Hours';

        header('Content-Type: application/json;charset=utf-8');
        //header('Content-Disposition: attachment; filename=' . $file);
        header('Content-Transfer-Encoding: binary');

        $json = json_pretty(json_encode($array));

        $dir = realpath(APPPATH.'../json/');
        $dir .= '/daily_hours/' . date('Y/F',strtotime($date));

        if(!is_dir($dir)){
            mkdir($dir, 0777, TRUE);
        }

        file_put_contents($dir . '/' . $file . '.json' ,$json,FILE_APPEND);

        $fp = fopen($dir . '/' . $file .'.csv', 'w');
        $fields = array();
        if(count($field) > 0){
            foreach ($field as $row) {
                $_fld = array();
                foreach($row as $name => $v){
                    $_fld[$name] = $name;
                }
                $fields[0] = $_fld;
            }
        }
        $array_merge = array_merge($fields,$field);
        if(count($array_merge) > 0){
            foreach ($array_merge as $row) {
                fputcsv($fp, $row);
            }
        }
        fclose($fp);

        $post = array(
            'json_file' => $file,
            'date_export' => date('Y-m-d H:i:s'),
            'pay_period' => $date,
            'week' => $week,
            'is_committed' => 1,
            'uploader_id' => $this->session->userdata('user_id')
        );
        $is_exist = $this->my_model->getInfo('tbl_exported_files',array($date,$file),array('pay_period','json_file'));
        if(count($is_exist) > 0){
            foreach($is_exist as $dv){
                $this->my_model->update('tbl_exported_files',$post,$dv->id,'id',false);
            }
        }else{
            $this->my_model->insert('tbl_exported_files',$post,false);
        }
        echo $json;
    }
}