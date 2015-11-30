<?php
include('subbie.php');

class Admin_Controller extends Subbie{

    //region Quote
    function quoteList(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_client','tbl_registration'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_quotation.client_id','tbl_quotation.job_id'),
            'type' => 'left'
        ));

        $fields = ArrayWalk(array('id','client_id','job_address','is_accepted','is_archive'),'tbl_quotation.');
        $fields[] = 'tbl_client.client_name';
        $fields[] = 'DATE_FORMAT(tbl_quotation.date_requested,"%d-%m-%Y") as date_requested';
        $fields[] = 'DATE_FORMAT(tbl_quotation.closure_date,"%d-%m-%Y at %h:%i %p") as closure_date';
        $fields[] = 'DATE_FORMAT(tbl_quotation.announce_date,"%d-%m-%Y at %h:%i %p") as announce_date';
        $fields[] = 'IF(tbl_quotation.is_accepted ,"Accepted",
                        IF(tbl_quotation.closure_date < NOW() , "NA","Pending"
                        )
                     ) as status';
        $fields[] = 'IF(tbl_quotation.is_accepted , CONCAT("Accepted (",DATE_FORMAT(tbl_quotation.accepted_date ,"%d-%m-%Y"),")"),"") as accepted';
        $fields[] = 'CONCAT("SQ",LPAD(tbl_quotation.id + 30, 5,"0")) as quote_num';
        $fields[] = 'tbl_registration.job_name';

        $this->my_model->setSelectFields($fields);

        $this->data['quote_list'] = $this->my_model->getinfo('tbl_quotation',array(true,true),array('tbl_quotation.is_archive != ','tbl_client.is_exclude !='));

        $this->data['page_load'] = 'backend/quote/quote_list_view';
        $this->load->view('main_view',$this->data);
    }

    function acceptQuote(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }
        $id = $this->uri->segment(2);

        if(!$id){
            exit;
        }

        $post = array(
            'accepted_date' => date('Y-m-d'),
            'is_accepted' => true
        );

        $this->my_model->update('tbl_quotation',$post,$id);

        redirect('quoteList');
    }

    function quotation(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }

        $action = $this->uri->segment(2);

        if(!$action){
            exit;
        }
        $this->my_model->setNormalized('client_name','id');
        $this->my_model->setSelectFields(array('id','client_name'));
        $this->data['client'] = $this->my_model->getinfo('tbl_client');


        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_registration.client_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','address','client_id','job_name'),'tbl_registration.');
        $fields[] = 'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref';
        $this->my_model->setSelectFields($fields);
        $job = $this->my_model->getInfo('tbl_registration');

        if(count($this->data['client']) >0){
            foreach($this->data['client'] as $ck=>$cv){
                $this->data['job'][$ck][0] = 'Not in Tracking Log';
            }
        }

        $this->data['job'] = array();
        if(count($job) >0){
            foreach($job as $jv){
                $this->data['job'][$jv->client_id][0] = 'Not in Tracking Log';
                $this->data['job'][$jv->client_id][$jv->id] = $jv->job_ref.' ('.$jv->job_name.')';
            }
        }

        //$this->data['page_load'] = 'backend/quote/new_quote_form_view';
        $id = '';
        switch($action){
            case 'new':
                $this->load->view('backend/quote/quote_input_view',$this->data);
                break;
            case 'request':
                $this->data['id'] = $this->uri->segment(3);
                $this->load->view('backend/quote/request_quote_input_view',$this->data);
                break;
            default:
                $id = $this->uri->segment(3);
                $this->data['quotation'] = $this->my_model->getinfo('tbl_quotation',$id);
                $this->load->view('backend/quote/edit_quote_input_view',$this->data);
                break;
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            unset($_POST['gst']);
            $_POST['date_requested'] = date('Y-m-d');
            $_POST['closure_date'] = date('Y-m-d H:i:s',strtotime($_POST['closure_date']));
            $_POST['announce_date'] = date('Y-m-d H:i:s',strtotime($_POST['announce_date']));
            switch($action){
                case 'edit':
                    $this->my_model->update('tbl_quotation',$_POST,$id);

                    redirect('quoteRequested/'.$_POST['client_id'].'/'.$id);
                    break;
                default:
                    //$_POST['closure_date'] = date('Y-m-d H:i:s',strtotime($_POST['closure_date']));
                    if($_POST['job_id']){
                        unset($_POST['job_name']);

                        $quote_id = $this->my_model->insert('tbl_quotation',$_POST);

                        redirect('quoteRequested/'.$_POST['client_id'].'/'.$quote_id);
                    }else{
                        $post = array(
                            'job_name' => $_POST['job_name'],
                            'client_id' => $_POST['client_id'],
                            'address' => json_encode(array(
                                'number' => '',
                                'name' => '',
                                'suburb' => '',
                                'city' => '',
                            )),
                            'date_added' => date('Y-m-d')
                        );
                        $id = $this->my_model->insert('tbl_registration',$post,false);

                        $tracking = array(
                            'job_id' => $id,
                            'status_id' => 1,
                            'date' => date('Y-m-d')
                        );
                        $this->my_model->insert('tbl_tracking_log',$tracking);

                        unset($_POST['job_name']);
                        $_POST['job_id'] = $id;

                        $quote_id = $this->my_model->insert('tbl_quotation',$_POST);

                        redirect('quoteRequested/'.$_POST['client_id'].'/'.$quote_id);
                    }
                    break;
            }

        }

    }

    function quoteRequested(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }
        $quote_id = $this->uri->segment(3);
        $id = $this->uri->segment(2);
        if(!$id && !$quote_id){
            exit;
        }

        $this->data['client_data'] = $this->my_model->getinfo('tbl_client',$id);

        $this->my_model->setJoin(array(
            'table' => array('tbl_registration'),
            'join_field' => array('id'),
            'source_field' => array('tbl_quotation.job_id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array(
            'tbl_quotation.id','tbl_quotation.client_id','tbl_quotation.job_address','tbl_quotation.price','tbl_quotation.tags',
            'tbl_quotation.price * 0.15 as gst', 'CONCAT("SQ",LPAD(tbl_quotation.id + 30, 5,"0")) as quote_num',
            'tbl_registration.address','tbl_quotation.is_archive'
        ));
        $this->data['quotation'] = $this->my_model->getinfo('tbl_quotation',$quote_id,'tbl_quotation.id');
        if(count($this->data['quotation']) > 0){
            foreach($this->data['quotation'] as $v){
                $this->data['page_name'] = $this->data['page_name'].' '.$v->quote_num;
            }
        }

        if(isset($_POST['archive'])){
            $this->my_model->update('tbl_quotation',array('is_archive' => true),$quote_id);
            redirect('quoteList');
        }

        $this->data['page_load'] = 'backend/quote/quote_form_view';
        $this->load->view('main_view',$this->data);
    }

    function printQuote(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }
        $quote_id = $this->uri->segment(3);
        $id = $this->uri->segment(2);
        if(!$id && !$quote_id){
            exit;
        }

        $this->data['client_data'] = $this->my_model->getinfo('tbl_client',$id);

        $this->my_model->setJoin(array(
            'table' => array('tbl_registration'),
            'join_field' => array('id'),
            'source_field' => array('tbl_quotation.job_id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array(
            'tbl_quotation.id','tbl_quotation.client_id','tbl_quotation.job_address','tbl_quotation.price','tbl_quotation.tags',
            'tbl_quotation.price * 0.15 as gst', 'CONCAT("SQ",LPAD(tbl_quotation.id + 30, 5,"0")) as quote_num',
            'tbl_registration.address','tbl_quotation.is_archive'
        ));
        $this->data['quotation'] = $this->my_model->getinfo('tbl_quotation',$quote_id,'tbl_quotation.id');
        $quote_num = '';
        if(count($this->data['quotation']) >0){
            foreach($this->data['quotation'] as $v){
                $quote_num = $v->quote_num;
            }
        }

        $this->data['dir'] = 'pdf/quote/'.date('Y').'/'.date('F');
        if(!is_dir($this->data['dir'])){
            mkdir($this->data['dir'], 0777, TRUE);
        }
        $whatVal = array($id,$quote_num.'-'.date('d F Y').'.pdf','quote');
        $whatFld = array('client_id','file_name','type');
        $post = array(
            'client_id' => $id,
            'file_name' => $quote_num.'-'.date('d F Y').'.pdf',
            'type' => 'quote',
            'date' => date('Y-m-d')
        );

        $has_data = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,$whatFld);

        if(count($has_data) >0){
            foreach($has_data as $v){
                $this->my_model->update('tbl_pdf_archive',$post,$v->id);
            }
        }else{
            $this->my_model->insert('tbl_pdf_archive',$post);
        }
        $this->load->view('backend/quote/print_quote_view',$this->data);
    }
    //endregion

    function clientList(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $this->my_model->setOrder('client_name');
        $this->data['client'] = $this->my_model->getinfo('tbl_client',true,'is_exclude !=');
        $this->data['page_load'] = 'backend/client/client_list_view';
        $this->load->view('main_view',$this->data);
    }

    //region DTR
    function timeSheet(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
        $this->data['year'] = getYear();
        $this->data['month'] = getMonth();

        $this->my_model->setNormalized('holiday_type','id');
        $this->my_model->setSelectFields(array('id','holiday_type'));
        $this->data['holiday'] = $this->my_model->getInfo('tbl_day_type');
        $this->data['holiday'][''] = '-';

        ksort($this->data['holiday']);

        $this->data['dtr'] = array();
        $this->data['job_assign'] = array();
        $this->data['job'] = '';

        if(isset($_POST['search'])){
            $this->data['thisYear'] = $_POST['year'];
            $this->data['thisMonth'] = $_POST['month'];
            $this->data['thisWeek'] = $_POST['week'];
            $this->session->set_userdata(array(
                'year' => $_POST['year'],
                'month' => $_POST['month'],
                'week' => $_POST['week']
            ));
            redirect('timeSheetEdit');
        }
        $date_ = new DateTime();
        $this->data['thisYear'] = $this->session->userdata('year') != '' ? $this->session->userdata('year') : date('Y');
        $this->data['thisMonth'] = $this->session->userdata('month') != '' ? $this->session->userdata('month') : date('m');
        $this->data['thisWeek'] = $this->session->userdata('week') != '' ? $this->session->userdata('week') : $date_->format('W');

        $this->my_model->setJoin(array(
            'table' => array('tbl_registration','tbl_client'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_job_assign.job_id','tbl_registration.client_id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(
            array(
                'tbl_job_assign.id','tbl_job_assign.staff_id','tbl_job_assign.date',
                'CONCAT(tbl_client.client_code,LPAD(tbl_job_assign.job_id, 5,"0")) as job_ref'
            )
        );
        $job_assign = $this->my_model->getinfo('tbl_job_assign');

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_registration.client_id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array('tbl_registration.id','CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref'));
        $job = $this->my_model->getInfo('tbl_registration');
        $this->data['staff'] = $this->my_model->getinfo('tbl_staff',true,'tbl_staff.is_unemployed !=');

        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_day_type as holiday_tbl',
                'tbl_day_type as sick_leave_tbl'
            ),
            'join_field' => array('id','id'),
            'source_field' => array(
                'tbl_login_sheet.holiday_type_id',
                'tbl_login_sheet.sick_leave_type_id',

            ),
            'type' => 'left',
            'join_append' => array(
                'holiday_tbl','sick_leave_tbl'
            )
        ));
        $this->my_model->setSelectFields(array(
            'TIMESTAMPDIFF(SECOND, time_in, time_out) as hours',
            'time_in','time_out','staff_id','date',
            'tbl_login_sheet.id as dtr_id','working_type_id','holiday_type_id','sick_leave_type_id',
            'holiday_tbl.hours as holiday_hours','sick_leave_tbl.hours as sick_hours'
        ));
        $dtr = $this->my_model->getinfo('tbl_login_sheet');
        if(count($job)>0){
            foreach($job as $jv){
                $this->data['job'] = $jv->job_ref;
            }
        }

        $this->data['week'] = getWeeksNumberInMonth($this->data['thisYear'],$this->data['thisMonth']);
        $this_week = getWeekDateInMonth($this->data['thisYear'],$this->data['thisMonth']);
        $whatVal = array(
            $this->data['thisWeek'],
            $this_week[$this->data['thisWeek']]
        );
        $whatFld = array('week_num','date');
        $this->my_model->setShift();
        $this->data['pay_period'] = (Object)$this->my_model->getInfo('tbl_week_pay_period',$whatVal,$whatFld);

        $dt = new DateTime();

        if(isset($_POST['submit'])) {
            for ($whatDay = 1; $whatDay <= 8; $whatDay++) {
                $getDate = $dt->setISODate($_POST['year'], $_POST['week'], $whatDay)->format('Y-m-d');
                $d       = date('j', strtotime($getDate));
                if (count($this->data['staff']) > 0) {
                    foreach ($this->data['staff'] as $staff) {
                        $hasVal = $this->my_model->getinfo('tbl_login_sheet', array($staff->id, $getDate), array('staff_id', 'date'));

                        $this->my_model->setLastId('id');
                        $id      = (int)$this->my_model->getinfo('tbl_login_sheet', array($staff->id, $getDate), array('staff_id', 'date'));
                        $user_id = $this->session->userdata('user_id');
                        if (@$_POST['holiday_type_id'][$staff->id][$getDate]) {
                            $post = array(
                                'staff_id' => $staff->id,
                                'date' => $getDate,
                                'working_type_id' => 2,
                                'user_id' => $user_id,
                                'holiday_type_id' => $_POST['holiday_type_id'][$staff->id][$getDate]
                            );

                            if (count($hasVal) > 0) {
                                $this->my_model->update('tbl_login_sheet', $post, $id);
                            } else {
                                $this_id = $this->my_model->insert('tbl_login_sheet', $post);
                            }
                        }
                        if (@$_POST['sick_leave_type_id'][$staff->id][$getDate]) {
                            $post = array(
                                'staff_id' => $staff->id,
                                'date' => $getDate,
                                'user_id' => $user_id,
                                'working_type_id' => 2,
                                'sick_leave_type_id' => $_POST['sick_leave_type_id'][$staff->id][$getDate]
                            );

                            if (count($hasVal) > 0) {
                                $this->my_model->update('tbl_login_sheet', $post, $id);
                            } else {
                                $this_id = $this->my_model->insert('tbl_login_sheet', $post);
                            }
                        }

                        if (isset($_POST['time_in_' . $staff->id])) {
                            if (@$_POST['time_in_' . $staff->id][$d] != '') {
                                $str_hours    = str_split(@$_POST['time_in_' . $staff->id][$d], 2);
                                $str_hours[0] = $str_hours[0] > 23 ? '00' : $str_hours[0];
                                $str_hours[1] = $str_hours[1] > 59 ? '00' : $str_hours[1];
                                $post         = array(
                                    'staff_id' => $staff->id,
                                    'date' => $getDate,
                                    'time_in' => $getDate . ' ' . $str_hours[0] . ':' . $str_hours[1] . ':00',
                                    'working_type_id' => 1
                                );
                                if (count($hasVal) > 0) {
                                    $this->my_model->update('tbl_login_sheet', $post, $id);
                                } else {
                                    $this_id = $this->my_model->insert('tbl_login_sheet', $post);
                                }
                            }
                            if (@$_POST['time_out_' . $staff->id][$d] != '') {
                                $str_hours    = str_split(@$_POST['time_out_' . $staff->id][$d], 2);
                                $str_hours[0] = $str_hours[0] ? ($str_hours[0] > 23 ? '00' : $str_hours[0]) : '00';
                                $str_hours[1] = $str_hours[1] ? ($str_hours[1] > 59 ? '00' : $str_hours[1]) : '00';
                                $post         = array(
                                    'time_out' => $getDate . ' ' . $str_hours[0] . ':' . $str_hours[1] . ':00'
                                );
                                $thisId       = count($hasVal) > 0 ? $id : $this_id;
                                $this->my_model->update('tbl_login_sheet', $post, $thisId);
                            }
                        }
                    }
                }
            }
            redirect('timeSheetEdit');
        }

        if (isset($_POST['hours_submit'])) {
            $_this_date    = date('Y-m-d', strtotime($this_week[$this->data['thisWeek']]));
            $whatVal       = array(
                $this->data['thisWeek'],
                $_this_date
            );
            $whatFld       = array('week_num', 'date');
            $week_pay_data = $this->my_model->getInfo('tbl_week_pay_period', $whatVal, $whatFld);
            if (count($week_pay_data) > 0) {
                foreach ($week_pay_data as $val) {
                    $post = array(
                        'week_num' => $this->data['thisWeek'],
                        'date' => $_this_date,
                        'is_submitted' => 1
                    );

                    $this->my_model->update('tbl_week_pay_period', $post, $val->id);
                }
            } else {
                $post = array(
                    'week_num' => $this->data['thisWeek'],
                    'date' => $_this_date,
                    'is_submitted' => 1
                );
                $this->my_model->insert('tbl_week_pay_period', $post);
            }
        }

        if (isset($_POST['commit'])) {
            $post = array(
                'is_locked' => 1
            );
            $this->my_model->update('tbl_week_pay_period', $post, $this->data['pay_period']->id);
            $msg = 'Good day. <br/>Here is the attach file for Pay Period Summary Report from ';
            $msg .= '<strong>' . date('j F Y', strtotime($this_week[$this->data['thisWeek']])) . ' to ' . date('j F Y', strtotime('+6 days ' . $this_week[$this->data['thisWeek']])) . '</strong>.';

            $this_date  = $this_week[$this->data['thisWeek']];
            $this_range = date('Y/F', mktime(0, 0, 0, $this->data['thisMonth'], 1, $this->data['thisYear']));

            $result = $this->payPeriodSentEmail($this_date, $this_range, $msg);

            $post = array(
                'user_id' => $this->session->userdata('user_id'),
                'message' => json_encode($result['mail_settings']),
                'email_type' => 1,
                'type' => $result['result']->type,
                'debug' => $result['mail_settings']->debug,
                'date' => date('Y-m-d H:i:s')
            );

            $this->my_model->insert('tbl_email_export_log', $post, false);

            $_this_date    = date('Y-m-d', strtotime($this_week[$this->data['thisWeek']]));
            $whatVal       = array(
                $this->data['thisWeek'],
                $_this_date
            );
            $whatFld       = array('week_num', 'date');
            $week_pay_data = $this->my_model->getInfo('tbl_week_pay_period', $whatVal, $whatFld);

            $_data = $this->getTotalStaffData($_this_date);
            if (count($week_pay_data) > 0) {
                foreach ($week_pay_data as $val) {
                    $post = array(
                        'staff_count' => $_data['staff_count'],
                        'total_wage' => $_data['wage_total'],
                        'total_paye' => $_data['paye_total']
                    );
                    $this->my_model->update('tbl_week_pay_period', $post, $val->id);
                }
            } else {
                $post = array(
                    'week_num' => $this->data['thisWeek'],
                    'date' => $_this_date,
                    'staff_count' => $_data['staff_count'],
                    'total_wage' => $_data['wage_total'],
                    'total_paye' => $_data['paye_total']
                );
                $this->my_model->insert('tbl_week_pay_period', $post);
            }
        }


        if(count($job_assign) > 0){
            foreach($job_assign as $jv){
                $this->data['job_assign'][$jv->staff_id][$jv->date] = array(
                    'job_ref' => $jv->job_ref,
                    'job_id' => $jv->id
                );
            }
        }

        $this->data['working'] = array();
        if(count($dtr) > 0){
            foreach($dtr as $v){
                $hoursValue = number_format(($v->hours/3600),2);
                $hours = $hoursValue;

                $this->data['dtr'][$v->staff_id][$v->date] = array(
                    'time_in' => $v->time_in != '' ? date('Hi',strtotime($v->time_in)) : '',
                    'time_out' => $v->time_out != '' ? date('Hi',strtotime($v->time_out)) : '',
                    'hours' => $hours != '00.00' ? $hours : '&nbsp;',
                    'seconds' => $v->hours,
                    'sick_leave_type_id' => $v->sick_leave_type_id,
                    'holiday_type_id' => $v->holiday_type_id,
                    'sick_hours' => $v->sick_hours,
                    'holiday_hours' => $v->holiday_hours,
                    'dtr_id' => $v->dtr_id,
                    'date' => $v->date
                );

                $this->data['working'][$v->date] = $v->working_type_id;
            }
        }

        $this->my_model->setNormalized('working_type','id');
        $this->my_model->setSelectFields(array('working_type','id'));
        $this->data['working_type'] = $this->my_model->getInfo('tbl_working_type');
        $this->data['working_type'][0] = '-';

        $this->data['page_load'] = 'backend/dtr/new_edit_dtr_view';
        $this->load->view('main_view',$this->data);
    }

    function timeSheetEdit(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
        $this->data['year'] = getYear();
        $this->data['month'] = getMonth();
        $this->my_model->setNormalized('holiday_type','id');
        $this->my_model->setSelectFields(array('id','holiday_type'));
        $this->data['holiday'] = $this->my_model->getInfo('tbl_day_type');
        $this->data['holiday'][''] = '-';

        ksort($this->data['holiday']);

        $this->my_model->setNormalized('type','id');
        $this->my_model->setSelectFields(array('id','type'));
        $this->data['leave_type'] = $this->my_model->getInfo('tbl_leave_type');
        $this->data['leave_type'][''] = '-';

        ksort($this->data['leave_type']);

        $this->data['dtr'] = array();
        $this->data['job_assign'] = array();
        $this->data['job'] = '';

        if(isset($_POST['search'])){
            $this->data['thisYear'] = $_POST['year'];
            $this->data['thisMonth'] = $_POST['month'];
            $this->data['thisWeek'] = $_POST['week'];
            $this->session->set_userdata(array(
                'year' => $_POST['year'],
                'month' => $_POST['month'],
                'week' => $_POST['week']
            ));
            $_date = new DateTime();
            $_week = $_date->format('W');

            if(isset($_POST['submit_week_pay']) || (int)$_POST['week'] <= $_week){
                $this_week = getWeekDateInMonth($this->data['thisYear'],$this->data['thisMonth']);
                $_this_date    = date('Y-m-d', strtotime($this_week[$this->data['thisWeek']]));
                $whatVal       = array(
                    $this->data['thisWeek'],
                    $_this_date
                );
                $whatFld       = array('week_num', 'date');
                $week_pay_data = $this->my_model->getInfo('tbl_week_pay_period', $whatVal, $whatFld);
                if (count($week_pay_data) > 0) {
                    foreach ($week_pay_data as $val) {
                        $post = array(
                            'week_num' => $this->data['thisWeek'],
                            'date' => $_this_date
                        );

                        $this->my_model->update('tbl_week_pay_period', $post, $val->id);
                    }
                } else {
                    $post = array(
                        'week_num' => $this->data['thisWeek'],
                        'date' => $_this_date
                    );
                    $this->my_model->insert('tbl_week_pay_period', $post);
                }
            }

            redirect('timeSheetEdit');
        }

        $date_ = new DateTime();
        $default_week = date('N') >= 4 ? $date_->format('W') : $date_->format('W') - 1;
        $this->data['thisYear'] = $this->session->userdata('year') != '' ? $this->session->userdata('year') : date('Y');
        $this->data['thisMonth'] = $this->session->userdata('month') != '' ? $this->session->userdata('month') : date('m');
        $this->data['thisWeek'] = $this->session->userdata('week') != '' ? $this->session->userdata('week') : $default_week;

        $this->data['week'] = getWeeksNumberInMonth($this->data['thisYear'],$this->data['thisMonth']);
        $this_week = getWeekDateInMonth($this->data['thisYear'],$this->data['thisMonth']);
        $staff_data = new Staff_Helper();
        $this->data['leave_data_approved'] = $staff_data->staff_leave_application(array(1),array('tbl_leave.decision'));
        $this->data['leave_data_pending'] = $staff_data->staff_leave_application(array(0),array('tbl_leave.decision'));

        $this->my_model->setJoin(array(
            'table' => array('tbl_registration','tbl_client'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_job_assign.job_id','tbl_registration.client_id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(
            array(
                'tbl_job_assign.id','tbl_job_assign.staff_id','tbl_job_assign.date',
                'CONCAT(tbl_client.client_code,LPAD(tbl_job_assign.job_id, 5,"0")) as job_ref'
            )
        );
        $job_assign = $this->my_model->getinfo('tbl_job_assign');

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_registration.client_id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array('tbl_registration.id','CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref'));
        $job = $this->my_model->getInfo('tbl_registration');

        //$first_day_of_week = $this_week[$this->data['thisWeek']];
        //$last_day_of_week = date('Y-m-d',strtotime('+6 days '.$first_day_of_week));
        $whatVal = 'project_id = "1"';
        //$whatVal = 'project_id = "1" AND (date_employed != "0000-00-00" AND status_id = "3") ';
        //$whatVal .= 'OR (last_week_pay >= "' . $this->data['thisWeek'] . '")';
        //$whatVal .= ' AND date_last_pay = "0000-00-00"';
        $whatFld = '';
        $staff = $this->my_model->getinfo('tbl_staff',$whatVal,$whatFld);
        $this->data['staff'] = array();
        $employment_data = $staff_data->staff_employment();
        $staff_id = array();
        if(count($staff) > 0){
            foreach($staff as $ev){
                $week_value = $this_week[$this->data['thisWeek']];
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
                $last_week_pay = $ev->last_week_pay && $ev->last_week_pay >= $this->data['thisWeek'];
                $has_hours = $this->getTotalHours($week_value,$ev->id);

                if(($ev->date_employed != "0000-00-00" && $date_employed && $ev->status_id == 3)
                    || ($last_week_pay && $last_pay && $date_employed)
                    || ($date_employed && $last_pay)
                    || ($date_employed && $has_hours > 0 && $ev->status_id == 2)
                ){
                    $this->data['staff'][] = $ev;
                    $staff_id[] = $ev->id;
                }
            }
        }
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_day_type as holiday_tbl',
                'tbl_day_type as sick_leave_tbl',
                'tbl_day_type as day_type',
                'tbl_leave_type'
            ),
            'join_field' => array('id','id','id','id'),
            'source_field' => array(
                'tbl_login_sheet.holiday_type_id',
                'tbl_login_sheet.sick_leave_type_id',
                'tbl_login_sheet.day_type_id',
                'tbl_login_sheet.leave_type_id'
            ),
            'type' => 'left',
            'join_append' => array(
                'holiday_tbl','sick_leave_tbl','day_type','tbl_leave_type'
            )
        ));
        $this->my_model->setSelectFields(array(
            'TIMESTAMPDIFF(SECOND, time_in, time_out) as hours',
            'time_in','time_out','staff_id','date',
            'tbl_login_sheet.id as dtr_id','working_type_id','holiday_type_id','sick_leave_type_id',
            'tbl_login_sheet.leave_type_id','tbl_login_sheet.day_type_id',
            'holiday_tbl.hours as holiday_hours','sick_leave_tbl.hours as sick_hours',
            'day_type.hours as day_hours','tbl_leave_type.type'
        ));
        $dtr = $this->my_model->getinfo('tbl_login_sheet');
        if(count($job)>0){
            foreach($job as $jv){
                $this->data['job'] = $jv->job_ref;
            }
        }


        $whatVal = array(
            $this->data['thisWeek'],
            $this_week[$this->data['thisWeek']]
        );
        $whatFld = array('week_num','date');
        $this->my_model->setShift();
        $this->data['pay_period'] = (Object)$this->my_model->getInfo('tbl_week_pay_period',$whatVal,$whatFld);

        $dt = new DateTime();

        if(isset($_POST['submit'])) {
            for ($whatDay = 1; $whatDay <= 8; $whatDay++) {
                $getDate = $dt->setISODate($_POST['year'], $_POST['week'], $whatDay)->format('Y-m-d');
                $d       = date('j', strtotime($getDate));
                if (count($this->data['staff']) > 0) {
                    foreach ($this->data['staff'] as $staff) {

                        $whatValue = array($staff->id, $getDate);
                        $whatField = array('staff_id', 'date');

                        $hasVal = $this->my_model->getinfo('tbl_login_sheet', $whatValue, $whatField);

                        $this->my_model->setLastId('id');
                        $id      = (int)$this->my_model->getinfo('tbl_login_sheet', $whatValue, $whatField);
                        $user_id = $this->session->userdata('user_id');
                        $this_id = '';

                        if(
                            (
                            @$_POST['leave_type_id'][$staff->id][$getDate] ||
                            @$_POST['day_type_id'][$staff->id][$getDate]
                            )
                        ){

                            $__data['day_type_id'][$staff->id][$getDate] = $_POST['day_type_id'][$staff->id][$getDate];
                            $__data['leave_type_id'][$staff->id][$getDate] = $_POST['leave_type_id'][$staff->id][$getDate];

                            $this->session->set_userdata(array('day_type_selected' => $__data['day_type_id']));
                            $this->session->set_userdata(array('leave_type_selected' => $__data['leave_type_id']));
                        }

                        if (
                            @$_POST['leave_type_id'][$staff->id][$getDate] &&
                            @$_POST['day_type_id'][$staff->id][$getDate]
                        ) {
                            $post = array(
                                'staff_id' => $staff->id,
                                'date' => $getDate,
                                'working_type_id' => 2,
                                'user_id' => $user_id,
                                'leave_type_id' => @$_POST['leave_type_id'][$staff->id][$getDate],
                                'day_type_id' => @$_POST['day_type_id'][$staff->id][$getDate]
                            );

                            $has_pending = @$this->data['leave_data_pending'][$staff->id][$getDate];
                            if(count($has_pending) > 0){
                                unset($post['leave_type_id']);
                                unset($post['day_type_id']);
                            }

                            if (count($hasVal) > 0) {
                                $this->my_model->update('tbl_login_sheet', $post, $id);
                            } else {
                                $this_id = $this->my_model->insert('tbl_login_sheet', $post);
                            }

                        }
                        else{
                            $mysql_str = 'UPDATE tbl_login_sheet SET  day_type_id = NULL, leave_type_id = NULL WHERE  id ='. $id ;
                            $this->db->query($mysql_str);
                        }
                        if (isset($_POST['time_in_' . $staff->id])) {
                            if (@$_POST['time_in_' . $staff->id][$d] != '') {
                                $str_hours    = str_split(@$_POST['time_in_' . $staff->id][$d], 2);
                                $str_hours[0] = $str_hours[0] > 23 ? '00' : $str_hours[0];
                                $str_hours[1] = $str_hours[1] > 59 ? '00' : $str_hours[1];
                                $post         = array(
                                    'staff_id' => $staff->id,
                                    'date' => $getDate,
                                    'time_in' => $getDate . ' ' . $str_hours[0] . ':' . $str_hours[1] . ':00',
                                    'working_type_id' => 1
                                );

                                if (count($hasVal) > 0) {
                                    $this->my_model->update('tbl_login_sheet', $post, $id);
                                }
                                else {
                                    $this_id = $this->my_model->insert('tbl_login_sheet', $post);
                                }

                            }
                            else{
                                //$mysql_str = 'UPDATE tbl_login_sheet SET  time_in = NULL WHERE  id ='. $id ;
                                //$this->db->query($mysql_str);
                            }
                            if (@$_POST['time_out_' . $staff->id][$d] != '') {
                                $str_hours    = str_split(@$_POST['time_out_' . $staff->id][$d], 2);
                                $str_hours[0] = $str_hours[0] ? ($str_hours[0] > 23 ? '00' : $str_hours[0]) : '00';
                                $str_hours[1] = $str_hours[1] ? ($str_hours[1] > 59 ? '00' : $str_hours[1]) : '00';

                                $post         = array(
                                    'time_out' => $getDate . ' ' . $str_hours[0] . ':' . $str_hours[1] . ':00'
                                );

                                $thisId       = count($hasVal) > 0 ? $id : $this_id;
                                $this->my_model->update('tbl_login_sheet', $post, $thisId);
                            }
                            else{
                                /*$post         = array(
                                    'time_out' => ''
                                );

                                $thisId       = count($hasVal) > 0 ? $id : $this_id;
                                $this->my_model->update('tbl_login_sheet', $post, $thisId);*/
                            }

                            $post = array(
                                'is_preview' => 0
                            );

                            $whatVal = array($this->data['thisWeek'],$this_week[$this->data['thisWeek']]);
                            $whatFld = array('week_num','date');

                            $this->my_model->update('tbl_week_pay_period',$post,$whatVal,$whatFld);
                        }
                    }
                }
            }
            redirect('timeSheetEdit');
        }
        //region Foreman Hours Submit
        if (isset($_POST['hours_submit'])) {
            $_this_date    = date('Y-m-d', strtotime($this_week[$this->data['thisWeek']]));
            $whatVal       = array(
                $this->data['thisWeek'],
                $_this_date
            );
            $whatFld       = array('week_num', 'date');
            $week_pay_data = $this->my_model->getInfo('tbl_week_pay_period', $whatVal, $whatFld);

            if (count($week_pay_data) > 0) {
                foreach ($week_pay_data as $val) {
                    $post = array(
                        'week_num' => $this->data['thisWeek'],
                        'date' => $_this_date,
                        'is_submitted' => 1
                    );

                    $this->my_model->update('tbl_week_pay_period', $post, $val->id);
                }
            }
            else {
                $post = array(
                    'week_num' => $this->data['thisWeek'],
                    'date' => $_this_date,
                    'is_submitted' => 1
                );
                $this->my_model->insert('tbl_week_pay_period', $post);
            }
        }
        //endregion
        //region Commit
        if(isset($_POST['commit'])){
            $post = array(
                'is_locked' => 1
            );
            $this->my_model->update('tbl_week_pay_period', $post, $this->data['pay_period']->id);

            $post = array(
                'status_id' => 2
            );
            $whatVal = array(true,3,$this->data['thisWeek']);
            $whatFld = array('has_final_pay','status_id','last_week_pay');

            $this->my_model->update('tbl_staff', $post, $whatVal,$whatFld);
        }
        //endregion
        //region Sending Email
        if (isset($_POST['send_mail'])) {

            $msg = 'Good day. <br/>Here is the attach file for Pay Period Summary Report for Week ' . $this->data['thisWeek'] . ' from ';
            $msg .= '<strong>' . date('j F Y', strtotime($this_week[$this->data['thisWeek']])) . ' to ' . date('j F Y', strtotime('+6 days ' . $this_week[$this->data['thisWeek']])) . '</strong>.';

            $this_date  = $this_week[$this->data['thisWeek']];
            $this_range = date('Y/F', mktime(0, 0, 0, $this->data['thisMonth'], 1, $this->data['thisYear']));

            $result = $this->payPeriodSentEmail($this_date, $this_range, $msg,false);

            $post = array(
                'user_id' => $this->session->userdata('user_id'),
                'week_number' => $this->data['thisWeek'],
                'pay_period' => $this_week[$this->data['thisWeek']],
                'message' => json_encode($result['mail_settings']),
                'email_type_id' => 1,
                'type' => $result['result']->type,
                'debug' => $result['result']->debug,
                'date' => date('Y-m-d H:i:s')
            );

            $this->my_model->insert('tbl_email_export_log', $post, false);

            $_this_date    = date('Y-m-d', strtotime($this_week[$this->data['thisWeek']]));
            $whatVal       = array(
                $this->data['thisWeek'],
                $_this_date
            );
            $whatFld       = array('week_num', 'date');
            $week_pay_data = $this->my_model->getInfo('tbl_week_pay_period', $whatVal, $whatFld);

            $_data = $this->getTotalStaffData($_this_date);
            if (count($week_pay_data) > 0) {
                foreach ($week_pay_data as $val) {
                    $post = array(
                        'staff_count' => $_data['staff_count'],
                        'total_wage' => $_data['wage_total'],
                        'total_paye' => $_data['paye_total']
                    );
                    $this->my_model->update('tbl_week_pay_period', $post, $val->id);
                }
            } else {
                $post = array(
                    'week_num' => $this->data['thisWeek'],
                    'date' => $_this_date,
                    'staff_count' => $_data['staff_count'],
                    'total_wage' => $_data['wage_total'],
                    'total_paye' => $_data['paye_total']
                );
                $this->my_model->insert('tbl_week_pay_period', $post);
            }

            $this->sentAllStaffPaySlip($this->data['thisWeek'],$this_date,false,$staff_id);
            redirect('timeSheetEdit');
        }
        //endregion

        if(count($job_assign) > 0){
            foreach($job_assign as $jv){
                $this->data['job_assign'][$jv->staff_id][$jv->date] = array(
                    'job_ref' => $jv->job_ref,
                    'job_id' => $jv->id
                );
            }
        }

        $this->data['working'] = array();
        if(count($dtr) > 0){
            foreach($dtr as $v){
                $hoursValue = number_format(($v->hours/3600),2);
                $hours = $hoursValue;

                $this->data['dtr'][$v->staff_id][$v->date] = array(
                    'time_in' => $v->time_in != '' ? date('Hi',strtotime($v->time_in)) : '',
                    'time_out' => $v->time_out != '' ? date('Hi',strtotime($v->time_out)) : '',
                    'hours' => $hours != '00.00' ? $hours : '&nbsp;',
                    'seconds' => $v->hours,
                    'sick_leave_type_id' => $v->sick_leave_type_id,
                    'holiday_type_id' => $v->holiday_type_id,
                    'leave_type_id' => $v->leave_type_id,
                    'day_type_id' => $v->day_type_id,
                    'sick_hours' => $v->sick_hours,
                    'holiday_hours' => $v->holiday_hours,
                    'day_hours' => $v->day_hours,
                    'dtr_id' => $v->dtr_id,
                    'date' => $v->date
                );

                $this->data['working'][$v->date] = $v->working_type_id;
            }
        }

        $this->my_model->setNormalized('working_type','id');
        $this->my_model->setSelectFields(array('working_type','id'));
        $this->data['working_type'] = $this->my_model->getInfo('tbl_working_type');
        $this->data['working_type'][0] = '-';

        //region DTR Submit Leave Request
        if(isset($_POST['submit_request'])){
            unset($_POST['submit_request']);
            $staff_id = $this->uri->segment(2);

            $this->my_model->setShift();
            $userInfo = (Object)$this->my_model->getInfo('tbl_staff', $staff_id,'id');

            $leave_start = date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $_POST['leave_start'])));
            $leave_end = date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $_POST['leave_end'])));

            $holiday = $this->subbie_date_helper->holidays;
            $leave_duration = $this->getLeaveDaysCount($leave_start, $leave_end, $holiday);
            $day_type = $_POST['leave_range'];
            $post = array(
                'date_requested' => date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $_POST['date_requested']))),
                'leave_start' => $leave_start,
                'leave_end' => $leave_end,
                'leave_range' => $_POST['leave_range'],
                'user_id' => $staff_id,
                'type' => $_POST['type'],
                'reason_request' => nl2br($_POST['reason_request']),
                'decision' => '',
                'reason_decision' => '',
                'actioned_by' => '',
                'submitted_by' => $this->session->userdata('user_id')
            );
            $this->my_model->insert('tbl_leave',$post,false);
            //region Audit Log
            $log = array(
                'update_type' => 1,
                'user_id' => $this->session->userdata('user_id'),
                'to' => json_encode($post)
            );
            $this->my_model->insert('tbl_leave_audit_log', $log, false);
            //endregion

            $this->my_model->setSelectFields(array('tbl_leave_emails.emails'));
            $this->my_model->setNormalized('emails');
            $what_val = 'emails != ""';
            $emails = $this->my_model->getInfo('tbl_leave_emails',$what_val,'');

            if(count($emails) > 0) {
                $name = $userInfo->fname . " " . $userInfo->lname;
                $subject = "Leave Application from " . $name;

                $this->my_model->setLastId('holiday_type');
                $day = $this->my_model->getInfo('tbl_day_type',$day_type);

                $msg = "<strong>" . $name . "</strong> has requested ";
                $msg .= $day_type != 1 ? "a " . $day ."'s Leave, " : $leave_duration . " days leave, ";
                $msg .= "from <strong>" . date('l d/m/Y h:i a', strtotime($leave_start)) . "</strong> to <strong>" . date('l d/m/Y h:i a', strtotime($leave_end)) . "</strong>." .
                    "<br /><br />" .
                    "Please check the online Leave Application facility to <strong>Approve</strong> or <del>Decline</del> this request<br />" .
                    "(by no later than < 2 days before event if under 1 week away, or if greater than 1 week away then put 7 days before event due>)";
                $sendMailSetting = array(
                    'subject' => $subject,
                    'to' => $emails,
                    'debug_type' => 2,
                    'debug' => true
                );
                $send_mail = new Send_Email_Controller();
                $debugResult = $send_mail->sendingEmail($msg, $sendMailSetting);
                $sendMailSetting['body'] = $msg;
                $post = array(
                    'date' => date('Y-m-d H:i:s'),
                    'user_id' => $this->session->userdata('user_id'),
                    'staff_id' => $staff_id,
                    'type' => $debugResult->type,
                    'message' => json_encode($sendMailSetting),
                    'debug' => $debugResult->debug
                );
                $this->my_model->insert('tbl_leave_email_log', $post, false);
                redirect('timeSheetEdit');
            }
        }
        //endregion
        //region Request Leave
        if(isset($_GET['req']) && $_GET['req'] == 1){
            $staff_id = $this->uri->segment(2);
            $date = $this->uri->segment(3);

            $this->data['leave_data'] = @$_POST['leave_type_id'][$staff_id][$date];
            $this->data['day_data'] = @$_POST['day_type_id'][$staff_id][$date];

            $this->my_model->setShift();
            $date_selected = (Object)$this->my_model->getInfo('tbl_day_type',$this->data['day_data']);

            $this->data['date_start'] = $date.' '.$date_selected->start_hours;
            $this->data['date_end'] = $date.' '.$date_selected->end_hours;
            $this->data['staff_id'] = $staff_id;

            if(isset($_POST['request'])){
                $__data['day_type_id'][$staff_id][$date] = '';
                $__data['leave_type_id'][$staff_id][$date] = '';

                $this->session->set_userdata(array('day_type_selected' => $__data['day_type_id']));
                $this->session->set_userdata(array('leave_type_selected' => $__data['leave_type_id']));
            }
            else if(isset($_GET['type']))
            {
                $day_data = isset($_POST['leave_range']) && $_POST['leave_range'] ?  $_POST['leave_range'] : $this->data['day_data'];
                $leave_data = isset($_POST['type']) && $_POST['type'] ? $_POST['type'] : $this->data['leave_data'];

                $date_start = isset($_POST['leave_start']) && $_POST['leave_start'] ? date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $_POST['leave_start']))) : $this->data['date_start'];
                $date_end =  isset($_POST['leave_end']) && $_POST['leave_end'] ? date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $_POST['leave_end']))) : $this->data['date_end'];

                $leave_pay = $this->calculateTotalLeavePay(
                    $staff_id,$leave_data,
                    $date_start,$date_end,
                    $day_data,
                    $this->subbie_date_helper->holidays
                );
                $this->data['pay_data'] = @$leave_pay[$staff_id];

                $this->my_model->setLastId('type');
                $this->data['leave_type'] = $this->my_model->getInfo('tbl_leave_type',$leave_data);
                $this->load->view('backend/dtr/leave_request_report_view',$this->data);
            }
            else{
                $this->load->view('backend/dtr/dtr_leave_request_view',$this->data);
            }
        }
        //endregion
        else{
            $staff_data = new Staff_Helper();
            $rate = $staff_data->staff_rate();
            //$stat_holiday = $staff_data->stat_holiday($this->data['thisYear'],$this->data['thisMonth']);
            $stat_holiday = $this->stat_holiday_pay($this->data['thisYear'],$this->data['thisMonth'],$this->data['thisWeek']);
            //$stat_holiday = $this->getLeaveWeeklyPay(8,'2015-11-02');
            $week_start = StartWeekNumber($this->data['thisWeek'],$this->data['thisYear']);
            $_start_day = $week_start['start_day'];
            $_end_day = $week_start['end_day'];
            $calculate_acc_leave = $this->calculateTotalAccLeave('',$this->data['thisYear'],$this->data['thisMonth'],$this->data['thisWeek'],false);

            for ($whatDay = $_start_day; $whatDay <= $_end_day; $whatDay++){
                $getDate = $dt->setISODate($this->data['thisYear'], $this->data['thisWeek'], $whatDay)->format('Y-m-d');

                if(count($this->data['staff']) > 0){
                    foreach($this->data['staff'] as $sv){
                        $sv->rate_cost = 0;
                        $rate_ = @$rate[$sv->id];
                        if(count($rate_) > 0){
                            foreach($rate_ as $start_use=>$rv){
                                if(strtotime($start_use) <= strtotime($getDate)){
                                    $sv->rate_cost = $this->is_decimal($rv->rate_cost) ? number_format($rv->rate_cost,2,'.','') : $rv->rate_cost;
                                }
                            }
                        }
                    }
                }
            }
            $this->data['stat_holiday'] = $stat_holiday;
            $this->data['leave_pay'] = $calculate_acc_leave;
            $this->data['page_load'] = 'backend/dtr/new_edit_dtr_view';
            $this->load->view('main_view',$this->data);
        }
    }

    function timeSheetDefault(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }

        $this->data['year'] = getYear();
        if(isset($_POST['search'])){
            $this->session->set_userdata(array(
                'year_data'=> $_POST['year']
            ));
            redirect('timeSheetDefault');
        }
        $year_session = $this->session->userdata('year_data');
        $this->data['thisYear'] = $year_session ? $year_session : date('Y');

        if(isset($_GET['json']) && $_GET['json'] == 1){
            ini_set("memory_limit","512M");
            set_time_limit(90000);
            header("Content-type: application/json");

            $week_in_year = getWeekInYear($this->data['thisYear']);
            $json = array();
            $ref = 1;
            $today = new DateTime();
            $this_week = date('N') >= 4 ? $today->format('W') : $today->format('W') - 1;
            //$this_week = $today->format('W');
            if(count($week_in_year) > 0){
                foreach($week_in_year as $week=>$date){
                    $pay_period = date('M d/y',strtotime($date));
                    $pay_period .= '-' . date('M d/y',strtotime('+6 days '.$date));
                    $whatVal = array($week,$date);
                    $whatFld = array('week_num','date');
                    $this->my_model->setGroupBy('date');
                    $has_week_pay = $this->my_model->getInfo('tbl_week_pay_period',$whatVal,$whatFld);
                    $_week = explode('-',$week);
                    $week_has_passed = $_week[1] == $today->format('Y') && $_week[0] <= $this_week ? 1 : 0;
                    $_added_days = $_week[0] == 30 && $_week[1] == 2015 ? '+5 days ': '+6 days ';
                    if(count($has_week_pay) > 0){
                        foreach($has_week_pay as $val){
                            $json[] = array(
                                'id' => $ref,
                                'week_num' => str_pad($_week[0],2,'0',STR_PAD_LEFT),
                                'week_num_orig' => $_week[0],
                                'is_this_week' => $_week[1] == $today->format('Y') && $_week[0] == $this_week ? 1 : 0,
                                'week_has_passed' => $week_has_passed,
                                'pay_period' => $pay_period,
                                'no_employee' => $val->staff_count,
                                'locked' => $val->is_locked ? 'Yes' : 'No',
                                'year' => date('Y',strtotime($val->date)),
                                'month' => date('m',strtotime($_added_days . $val->date)),
                                'is_locked' => $val->is_locked ? 1 : 0,
                                'total_pay' => '$'.number_format($val->total_wage,2),
                                'total_paye' => '$'.number_format($val->total_paye,2),
                            );
                        }
                    }else{
                        $json[] = array(
                            'id' => $ref,
                            'week_num' => str_pad($_week[0],2,'0',STR_PAD_LEFT),
                            'week_num_orig' => $_week[0],
                            'is_this_week' => $_week[1] == $today->format('Y') && $_week[0] == $this_week ? 1 : 0,
                            'week_has_passed' => $week_has_passed,
                            'pay_period' => $pay_period,
                            'no_employee' => 0,
                            'locked' => 'No',
                            'year' => date('Y',strtotime($date)),
                            'month' => date('m',strtotime($_added_days . $date)),
                            'is_locked' => 0,
                            'total_pay' => '$0.00',
                            'total_paye' => '$0.00',
                        );
                    }

                    $ref++;
                }
            }
            echo json_encode($json);
        }
        else {
            $this->data['page_load'] = 'backend/dtr/time_sheet_view';
            $this->load->view('main_view',$this->data);
        }
    }

    function absentReason(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }
        $staff_id = $this->uri->segment(2);
        $date = $this->uri->segment(3);

        if(!$staff_id && !$date){
            exit;
        }

        $this->my_model->setNormalized('type','id');
        $this->my_model->setSelectFields(array('id','type'));
        $this->data['absent_type'] = $this->my_model->getinfo('tbl_absent_type',true,'is_not_view != ');

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            $_POST['staff_id'] = $staff_id;
            $_POST['start_date'] = $date;

            $this->my_model->insert('tbl_absent_reason',$_POST);
            redirect('timeSheet');
        }

        $this->load->view('backend/dtr/absent_reason_view',$this->data);
    }

    function assignJob(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }

        $type = $this->uri->segment(2);
        $id = $this->uri->segment(3);
        $date = $this->uri->segment(4);

        if(!$type && !$id && $date){
            exit;
        }

        switch($type){
            case 'json':
                $this->my_model->setJoin(array(
                    'table' => array('tbl_registration','tbl_client'),
                    'join_field' => array('id','id'),
                    'source_field' => array('tbl_job_assign.job_id','tbl_registration.client_id'),
                    'type' => 'left'
                ));

                $fields = ArrayWalk(array('id','staff_id','hours','date','job_id'),'tbl_job_assign.');
                $fields[] = 'tbl_registration.job_name';
                $fields[] = 'CONCAT(tbl_client.client_code, LPAD(tbl_registration.id,5,"0") ) as job_code';
                $this->my_model->setSelectFields($fields);
                $this->my_model->setOrder('date','DESC');
                $job_assigned = $this->my_model->getInfo('tbl_job_assign',array($id,$date),array('staff_id','date'));

                echo json_encode($job_assigned);
                break;
            case 'split':
                $this->my_model->setJoin(array(
                    'table' => array('tbl_registration','tbl_client'),
                    'join_field' => array('id','id'),
                    'source_field' => array('tbl_job_assign.job_id','tbl_registration.client_id'),
                    'type' => 'left'
                ));

                $fields = ArrayWalk(array('id','staff_id','hours','date','job_id'),'tbl_job_assign.');
                $fields[] = 'tbl_registration.job_name';
                $fields[] = 'CONCAT(tbl_client.client_code, LPAD(tbl_registration.id,5,"0") ) as job_code';
                $this->my_model->setSelectFields($fields);
                $this->my_model->setOrder('date','DESC');
                $this->data['job_assign'] = $this->my_model->getInfo('tbl_job_assign',array($id,$date),array('staff_id','date'));

                $this->load->view('backend/dtr/split_time_view',$this->data);
                break;
            case 'delete':
                if(isset($_POST['id'])){
                    $this->my_model->delete('tbl_job_assign',$_POST['id']);

                    redirect('assignJob/json/' . $id . '/' . $date);
                }
                break;
            default:
                if(isset($_POST['hour'])){
                    $post = array(
                        'date' => $date,
                        'staff_id' => $id,
                        'hours' => $_POST['hour'],
                        'job_id' => $_POST['job']
                    );

                    $this->my_model->insert('tbl_job_assign',$post);

                    redirect('assignJob/json/' . $id . '/' . $date);
                }
                break;
        }
    }

    function setJobAssign(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }
        $id = $this->uri->segment(2);
        $date = $this->uri->segment(3);
        $redirect = $this->uri->segment(4);
        $page = $this->uri->segment(5);
        if(!$id && !$date && !$redirect && !$page){
            exit;
        }

        $this->data['job_list'] = array();

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_registration.client_id'),
            'type' => 'left'
        ));
        //$this->my_model->setNormalized('job_ref','id');
        $this->my_model->setSelectFields(array
            (
                'tbl_registration.id',
                'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref',
                'tbl_registration.job_name'
            )
        );
        $job_list = $this->my_model->getinfo('tbl_registration');
        if(count($job_list) >0){
            foreach($job_list as $jv){
                $this->data['job_list'][$jv->id] = $jv->job_ref.' ('.$jv->job_name.')';
            }
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            switch($page){
                case 'add':
                    $_POST['staff_id'] = $id;
                    $_POST['start_date'] = $date;
                    $this->my_model->insert('tbl_job_assign',$_POST);
                    break;
                default:
                    $this->my_model->update('tbl_job_assign',$_POST,$id);
                    break;
            }


            $redirect == 'main' ? redirect('timeSheet') : redirect('timeSheetEdit');
            //redirect('timeSheet');
        }

        $this->load->view('backend/dtr/set_job_view',$this->data);
    }

    function timeSheetLog(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }

        $action = $this->uri->segment(2);
        $id = $this->uri->segment(3);

        if(!$id && !$action){
            exit;
        }

        switch($action){
            case 'in':
                $post = array(
                    'staff_id' => $id,
                    'time_in' => date('Y-m-d H:i:s'),
                    'date' => date('Y-m-d')
                );
                $this->my_model->insert('tbl_login_sheet',$post);
                break;
            default:
                $dtr_id = $this->uri->segment(4);
                if(!$dtr_id){
                    exit;
                }
                $post = array(
                    'staff_id' => $id,
                    'time_out' => date('Y-m-d H:i:s'),
                    'date' => date('Y-m-d')
                );
                $this->my_model->update('tbl_login_sheet',$post,$dtr_id);
                break;
        }

        redirect('timeSheet');
    }

    //endregion

    function manageClient(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }

        $action = $this->uri->segment(2);

        if(!$action){
            exit;
        }

        $id = '';
        switch($action){
            case 'add':
                $this->load->view('backend/client/add_client_view',$this->data);
                break;
            case 'edit':
                $id = $this->uri->segment(3);
                if(!$id){
                    exit;
                }
                $this->data['client_data'] = $this->my_model->getinfo('tbl_client',$id);

                $this->my_model->setShift();
                $this->data['client_email'] = (Object)$this->my_model->getinfo('tbl_client_email_return',$id,'client_id');

                $this->load->view('backend/client/edit_client_view',$this->data);
                break;
            case 'delete':
                $id = $this->uri->segment(3);
                if(!$id){
                    exit;
                }
                $this->my_model->update('tbl_client',array('is_exclude' => 1),$id);
                redirect('clientList');
                break;
            default:
                break;
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            switch($action){
                case 'add':
                    $client = $this->my_model->getFields('tbl_client',array('id','is_exclude','date_registered'));
                    $client_post = array();
                    if(count($client) > 0){
                        foreach($client as $cv){
                            $client_post[$cv] = $_POST[$cv];
                        }
                    }
                    $client_post['date_registered'] = date('Y-m-d');
                    $client_id = $this->my_model->insert('tbl_client',$client_post,false);


                    //region Take-off Return Information

                    $post = array(
                        'client_id' => $client_id,
                        'via_subbie' => isset($_POST['via_subbie']) ? 1 : 0,
                        'via_subbie_only' => isset($_POST['via_subbie_only']) ? 1 : 0,
                        'via_email' => isset($_POST['via_email']) ? 1 : 0,
                        'take_off_merchant_name' => isset($_POST['take_off_merchant_name']) ? $_POST['take_off_merchant_name'] : '',
                        'take_off_merchant_email' => isset($_POST['take_off_merchant_email']) ? $_POST['take_off_merchant_email'] : '',
                        'take_off_merchant_cc' => isset($_POST['via_email']) ? json_encode($_POST['take_off_merchant_cc']) : '[]',
                        'take_off_franchise_email' => isset($_POST['take_off_franchise_email']) ? $_POST['take_off_franchise_email'] : '',
                        'via_ftp' => isset($_POST['via_ftp']) ? 1 : 0
                    );
                    $this->my_model->insert('tbl_client_email_return', $post, false);
                    //endregion
                    break;
                default:
                    $client = $this->my_model->getFields('tbl_client',array('id','is_exclude','date_registered'));
                    $client_post = array();
                    if(count($client) > 0){
                        foreach($client as $cv){
                            $client_post[$cv] = $_POST[$cv];
                        }
                    }

                    $this->my_model->update('tbl_client',$client_post,$id);
                    //region Take-off Return Information
                    $is_exist = $this->my_model->getInfo('tbl_client_email_return',$id,'client_id');
                    $post = array(
                        'client_id' => $id,
                        'via_subbie' => isset($_POST['via_subbie']) ? 1 : 0,
                        'via_subbie_only' => isset($_POST['via_subbie_only']) ? 1 : 0,
                        'via_email' => isset($_POST['via_email']) ? 1 : 0,
                        'take_off_merchant_name' => isset($_POST['take_off_merchant_name']) ? $_POST['take_off_merchant_name'] : '',
                        'take_off_merchant_email' => isset($_POST['take_off_merchant_email']) ? $_POST['take_off_merchant_email'] : '',
                        'take_off_merchant_cc' => isset($_POST['via_email']) ? json_encode($_POST['take_off_merchant_cc']) : '[]',
                        'take_off_franchise_email' => isset($_POST['take_off_franchise_email']) ? $_POST['take_off_franchise_email'] : '',
                        'via_ftp' => isset($_POST['via_ftp']) ? 1 : 0
                    );

                    if(count($is_exist) > 0){
                        $this->my_model->update('tbl_client_email_return', $post, $id,'client_id',false);
                    }else{
                        $this->my_model->insert('tbl_client_email_return', $post, false);
                    }

                    break;
            }
            redirect('clientList');
        }
    }

    function orderBook(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }

        $id = $this->uri->segment(2);
        $order_id = $this->uri->segment(3);

        if(!$id){
            exit;
        }

        $this->getOrderData($id,$order_id);

        $job_id = '';
        if(count($this->data['order_list']) >0){
            foreach($this->data['order_list'] as $v){
                $this->data['job_num'] = $v->job_ref;
                $job_id = $v->job_id;
            }
        }

        $this->data['page_name'] = $this->data['page_name'].' '.$this->data['order_num'];
        if(isset($_POST['send'])){
            $post = array(
                'is_order' => true,
                'order_ref' => $this->data['order_num'],
            );
            $this->my_model->update('tbl_order_book',$post,array(true,$id),array('is_order !=','supplier_id'));
            $post = array(
                'job_id' => $job_id,
                'supplier_id' => $id,
                'order_ref' => $this->data['order_num'],
                'date' => date('Y-m-d')
            );
            $this->my_model->insert('tbl_order_send',$post);
            redirect('orderSentList');
        }
        $this->data['page_load'] = 'backend/order_book/order_book_view';
        $this->load->view('main_view',$this->data);
    }

    function manageOrder(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }
        $id = $this->uri->segment(2);
        $supplier = $this->uri->segment(3);

        if(!$id && !$supplier){
            exit;
        }
        $this->my_model->setNormalized('product_name','id');
        $this->my_model->setSelectFields(array('id','product_name'));
        $this->data['product_list'] = $this->my_model->getInfo('tbl_product_list',$supplier,'supplier_id');

        $this->data['order'] = $this->my_model->getInfo('tbl_order_book',$id);

        if(isset($_POST['save'])){
            unset($_POST['save']);
            $this->my_model->update('tbl_order_book',$_POST,$id);
            redirect('orderBook/'.$supplier);
        }

        $this->load->view('backend/order_book/order_book_edit_view',$this->data);
    }

    function orderBookInput(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_client','tbl_tracking_log'),
            'join_field' => array('id','job_id'),
            'source_field' => array('tbl_registration.client_id','tbl_registration.id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','address','client_id','is_invoice','job_name'),'tbl_registration.');
        $fields[] = 'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref';
        $this->my_model->setSelectFields($fields);
        $job = $this->my_model->getInfo('tbl_registration',4,'status_id');
        $this->data['job'] = array();
        if(count($job) >0){
            foreach($job as $v){
                $address = (object)json_decode($v->address);
                $this->data['job'][$v->id] = $v->job_ref.' ('.$v->job_name.')';
            }
        }else{
            $this->data['job'][''] = '-';
        }

        $this->my_model->setNormalized('supplier_name','id');
        $this->my_model->setSelectFields(array('id','supplier_name'));
        $this->data['supplier'] = $this->my_model->getInfo('tbl_supplier');

        if(isset($_POST['save'])){
            unset($_POST['save']);
            $product = $this->my_model->getInfo('tbl_product_list');
            if(count($product) >0){
                foreach($product as $pv){
                    if(@$_POST['quantity'][$pv->id] != ''){
                        $post = array(
                            'quantity' => @$_POST['quantity'][$pv->id],
                            'job_id' => $_POST['job_id'],
                            'supplier_id' => $_POST['supplier_id'],
                            'product_id' => $pv->id,
                            'date' => date('Y-m-d')
                        );
                        $this->my_model->insert('tbl_order_book',$post);
                    }
                }
            }
            redirect('orderBook/'.$_POST['supplier_id']);
        }

        $this->load->view('backend/order_book/order_book_input_view',$this->data);
    }

    function printOrder(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }
        $id = $this->uri->segment(2);
        if(!$id){
            exit;
        }
        $this->getOrderData($id);
        if(count($this->data['order_list']) >0){
            foreach($this->data['order_list'] as $v){
                $this->data['job_num'] = $v->job_ref;
            }
        }
        $this->load->view('backend/order_book/print_pdf_order',$this->data);
    }

    function productTableLoad(){
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }

        $id = $this->uri->segment(2);
        $select_product = $this->uri->segment(3);
        if(!$id){
            exit;
        }
        $this->my_model->setLastId('job_name');
        $this->data['job_name'] = $this->my_model->getInfo('tbl_registration',$id);

        $this->my_model->setJoin(array(
            'table' => array('tbl_supplier','tbl_product_list'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_order_book.supplier_id','tbl_order_book.product_id'),
            'type' => 'left'
        ));
        $fld = ArrayWalk($this->my_model->getFields('tbl_order_book'),'tbl_order_book.');
        $fld[] = 'tbl_supplier.supplier_name';
        $fld[] = 'tbl_product_list.product_name';
        $fld[] = 'tbl_product_list.price';

        $this->my_model->setSelectFields($fld);
        $this->data['product_list'] = $this->my_model->getInfo('tbl_order_book',$id,'job_id');

        $this->my_model->setNormalized('supplier_name','id');
        $this->my_model->setSelectFields(array('id','supplier_name'));
        $this->data['supplier'] = $this->my_model->getInfo('tbl_supplier');
        $this->data['supplier'][''] = 'All Supplier';

        ksort($this->data['supplier']);

        if(isset($_POST['submit'])){
            $this->session->set_userdata(array(
                'job_id' => $_POST['job_id']
            ));
        }

        if($select_product && $select_product == 'select'){
            $this->load->view('backend/order_book/order_search_material',$this->data);
        }
        else if(isset($_GET['search']) && $_GET['search'] == 1)
        {
            $whatVal = '';
            $whatFld = '';

            if(isset($_POST['data_search'])){
                $whatVal = 'product_name LIKE "%'.$_POST['input'].'%"';
                $whatVal .= $_POST['supplier_id'] ? ' AND supplier_id ="'.$_POST['supplier_id'].'"' : '';
                $whatFld = '';
            }

            $this->my_model->setJoin(array(
                'table' => array('tbl_supplier'),
                'join_field' => array('id'),
                'source_field' => array('tbl_product_list.supplier_id'),
                'type' => 'left'
            ));
            $fld = ArrayWalk($this->my_model->getFields('tbl_product_list'),'tbl_product_list.');
            $fld[] = 'tbl_supplier.supplier_name';

            $this->my_model->setSelectFields($fld);
            $this->data['product_list'] = $this->my_model->getInfo('tbl_product_list',$whatVal,$whatFld);

            $this->load->view('backend/order_book/search_product_list_view',$this->data);
        }
        else{
            $this->load->view('backend/order_book/product_table_load_list_view',$this->data);
        }
    }

    function invoiceCreate(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
        $this->my_model->setNormalized('client_name','id');
        $this->my_model->setSelectFields(array('id','client_name'));
        $this->data['client'] = $this->my_model->getInfo('tbl_client',true,'is_exclude !=');

        $this->my_model->setNormalized('title','value');
        $this->my_model->setSelectFields(array('title','value'));
        $this->data['template_text'] = $this->my_model->getInfo('tbl_template');
        $this->data['template_text'][''] = '-';

        $this->my_model->setNormalized('trade','id');
        $this->my_model->setSelectFields(array('trade','id'));
        $this->data['trade'] = $this->my_model->getInfo('tbl_trade');

        $this->my_model->setNormalized('invoice_type','id');
        $this->my_model->setSelectFields(array('invoice_type','id'));
        $this->data['invoice_type'] = $this->my_model->getInfo('tbl_invoice_type');

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_registration.client_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','address','client_id','is_invoice','job_name'),'tbl_registration.');
        $fields[] = 'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref';
        $this->my_model->setSelectFields($fields);
        $job = $this->my_model->getInfo('tbl_registration',true,'tbl_registration.is_invoice !=');
        $this->data['job'] = array();
        $client = $this->my_model->getInfo('tbl_client');
        if(count($client) >0){
            foreach($client as $cv){
                $this->data['job'][$cv->id][0] = 'Not in Tracking Log';
            }
        }
        if(count($job) >0){
            foreach($job as $jv){
                $address = (object)json_decode($jv->address);
                $this_add = $address->number.' '.$address->name.', '.$address->suburb.', '.$address->city;
                $this->data['job'][$jv->client_id][$jv->id] = $jv->job_ref.' ('.$jv->job_name.')';
            }
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            unset($_POST['template_text']);

            $_POST['date'] = date('Y-m-d');
            $_POST['is_new'] = $this->data['account_type'] == 3 ? 1 : 0;

            $id = $this->my_model->insert('tbl_invoice',$_POST,false);
            $this->my_model->update('tbl_registration',array('is_invoice' => true),$_POST['job_id'],'id',false);

            redirect('jobInvoice/'.$_POST['client_id'].'/'.$id);
        }
        $this->load->view('backend/invoice/create_invoice_view',$this->data);
    }

    function supplierList(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        /*if(isset($_GET['export'])){
            $year = getWeekBetweenDates('2015-03-31','2015-11-02');
            //DisplayArray($year);exit;
            if(count($year) > 0){
                foreach($year as $key=>$val){
                    $_key = explode('-',$key);
                    $this->exportPayValues($_key[0],date('m',strtotime($val)),date('Y',strtotime($val)));
                }
            }
        }*/
        $this->data['supplier'] = $this->my_model->getInfo('tbl_supplier');
        $this->data['page_load'] = 'backend/supplier/supplier_list_view';
        $this->load->view('main_view',$this->data);
    }

    function manageSupplier(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $action = $this->uri->segment(2);

        if(!$action){
            exit;
        }

        $id = '';
        switch($action){
            case 'add':
                $this->load->view('backend/supplier/add_supplier_view',$this->data);
                break;
            case 'edit':
                $id = $this->uri->segment(3);
                if(!$id){
                    exit;
                }
                $this->data['supplier_data'] = $this->my_model->getinfo('tbl_supplier',$id);
                $this->load->view('backend/supplier/edit_supplier_view',$this->data);
                break;
            default:
                break;
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            switch($action){
                case 'add':
                    $this->my_model->insert('tbl_supplier',$_POST,false);
                    break;
                default:
                    $this->my_model->update('tbl_supplier',$_POST,$id);
                    break;
            }
            redirect('supplierList');
        }

    }

    function productListTable(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $id = $this->uri->segment(2);
        if(!$id){
            exit;
        }

        $fields = ArrayWalk(array('id','product_name','quantity','type','price','supplier_id'),'tbl_product_list.');
        $this->my_model->setSelectFields($fields);
        $this->my_model->setOrder('product_name');
        $this->data['product_list'] = $this->my_model->getInfo('tbl_product_list',$id,'supplier_id');

        $this->my_model->setLastId('supplier_name');
        $supplier_name = $this->my_model->getInfo('tbl_supplier',$id);

        $this->data['page_name'] = $supplier_name.' '.$this->data['page_name'];
        $this->data['page_load'] = 'backend/supplier/product_list_view';
        $this->load->view('main_view',$this->data);
    }

    function productManage(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $supplier = $this->uri->segment(2);
        $type = $this->uri->segment(3);
        $id = $this->uri->segment(4);

        if(!$type){
            exit;
        }

        switch($type){
            case 'add':
                $this->load->view('backend/supplier/add_product_view',$this->data);
                break;
            case 'edit':
                $this->data['product'] = $this->my_model->getInfo('tbl_product_list',$id);
                $this->load->view('backend/supplier/edit_product_view',$this->data);
                break;
            default:
                break;
        }

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            switch($type){
                case 'add':
                    $_POST['supplier_id'] = $supplier;
                    $this->my_model->insert('tbl_product_list',$_POST);
                    break;
                case 'edit':
                   $this->my_model->update('tbl_product_list',$_POST,$id);
                    break;
                default:
                    break;
            }
            redirect('productListTable/'.$supplier);
        }
    }

    function orderSentList(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_registration',
                'tbl_supplier',
                'tbl_client'
            ),
            'join_field' => array('id','id','id'),
            'source_field' => array(
                'tbl_order_send.job_id',
                'tbl_order_send.supplier_id',
                'tbl_registration.client_id'
            ),
            'type' => 'left'
        ));

        $fields = ArrayWalk(array('job_id','supplier_id','order_ref','date'),'tbl_order_send.');
        $fields[] = 'IF(tbl_order_send.date != "0000-00-00",DATE_FORMAT(tbl_order_send.date,"%d/%m/%Y"), "") as date';
        $fields[] = 'tbl_registration.address';
        $fields[] = 'tbl_registration.job_name';
        $fields[] = 'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref';
        $fields[] = 'tbl_supplier.supplier_name';

        $this->my_model->setSelectFields($fields);
        //$this->my_model->setOrder('order_ref');
        $this->data['order_list'] = $this->my_model->getInfo('tbl_order_send');

        if(count($this->data['order_list']) >0){
            foreach($this->data['order_list'] as $v){
                $v->subTotal = 0;
                $v->quantity = 0;
                $this->my_model->setJoin(array(
                    'table' => array(
                        'tbl_product_list'
                    ),
                    'join_field' => array('id'),
                    'source_field' => array(
                        'tbl_order_book.product_id'
                    ),
                    'type' => 'left'
                ));
                $fields = ArrayWalk(array('product_id','quantity'),'tbl_order_book.');
                $fields[] = 'tbl_product_list.price';

                $this->my_model->setSelectFields($fields);
                $order_book = $this->my_model->getInfo('tbl_order_book',$v->order_ref,'order_ref');

                if(count($order_book) >0){
                    foreach($order_book as $ov){
                        $v->subTotal += $ov->price;
                        $v->quantity += $ov->quantity;
                    }
                }
            }
        }
        $this->data['page_load'] = 'backend/order_book/order_list_view';
        $this->load->view('main_view',$this->data);
    }

    function archiveInvoice(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $this->data['invoice_list'] = array();
        $this->data['year'] = getYear();
        $this->data['month'] = getMonth();

        $this->my_model->setNormalized('client_name','id');
        $this->my_model->setSelectFields(array('id','client_name'));
        $this->data['client'] = $this->my_model->getInfo('tbl_client',true,'is_exclude !=');
        $this->data['client'][''] = 'Select All';

        ksort($this->data['client']);

        if(isset($_POST['submit'])){
            $this->session->set_userdata(array(
                'whatYear' => $_POST['year'],
                'whatMonth' => $_POST['month'],
                'client_key' => $_POST['client'],
                'type' => $_POST['type']
            ));
            redirect('archiveInvoice');
        }

        $this->data['whatYear'] = $this->session->userdata('whatYear') ? $this->session->userdata('whatYear') : date('Y');
        $this->data['whatMonth'] = $this->session->userdata('whatMonth') ? $this->session->userdata('whatMonth') : date('m');
        $this->data['client_key'] = $this->session->userdata('client_key') ? $this->session->userdata('client_key') : '';
        $this->data['type'] = $this->session->userdata('type') ? $this->session->userdata('type') : 2;

        $date = $this->data['whatYear'].'-'.$this->data['whatMonth'];

        if($this->data['type'] == 1){
            $whatVal = 'tbl_pdf_archive.type = "invoice" AND tbl_pdf_archive.date LIKE "%'.$this->data['whatYear'].'%"';
        }else{
            $whatVal = 'tbl_pdf_archive.type = "invoice" AND tbl_pdf_archive.date LIKE "%'.$date.'%"';
        }
        if($this->data['client_key'] != ''){
            $whatVal .= ' AND tbl_pdf_archive.client_id ="'.$this->data['client_key'].'"';
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_pdf_archive.client_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','file_name','client_id','type','date','download'),'tbl_pdf_archive.');
        $fields[] = 'DATE_FORMAT(tbl_pdf_archive.date,"%d/%m/%Y") as archive_date';
        $fields[] = 'tbl_client.client_name';
        $fields[] = 'tbl_client.client_code';

        $this->my_model->setSelectFields($fields);
        $this->my_model->setGroupBy('inv_ref');
        $invoice_list = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,'');

        if(count($invoice_list) > 0 ){
            foreach($invoice_list as $v){
                $file_name = explode(' ',$v->file_name);
                $this->my_model->setShift();
                $getAmount = @(Object)$this->my_model->getInfo('tbl_statement',array($file_name[0],'INVOICE'),array('reference','type'));

                $this->my_model->setShift();
                $invoice = @(Object)$this->my_model->getInfo('tbl_invoice',array($file_name[0],$v->client_id),array('inv_ref','client_id'));
                $job_name_ = @$invoice->job_name ? explode("\n",@$invoice->job_name) : array();
                $this->data['invoice_list'][date('m/Y',strtotime($v->date))][] = (object)array(
                    'id' => $v->id,
                    'file_name' => $v->file_name,
                    'date' => $v->date,
                    'archive_date' => $v->archive_date,
                    'job_name' => count($job_name_) > 0 ? $job_name_[0] : '',
                    'amount' => @number_format(@$getAmount->debits,2),
                    'original_amount' => @$getAmount->debits,
                    'client_name' => $v->client_name,
                    'client_code' => $v->client_code,
                    'client_id' => $v->client_id
                );
            }
        }
        $this->data['page_load'] = 'backend/invoice/archive_invoice_view';
        $this->load->view('main_view',$this->data);
    }

    function archiveQuote(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $this->data['quote_list'] = array();
        $this->data['year'] = getYear();
        $this->data['month'] = getMonth();
        $this->data['whatYear'] = date('Y');
        $this->data['whatMonth'] = date('m');

        $date = $this->data['whatYear'].'-'.$this->data['whatMonth'];
        $whatVal = '(tbl_pdf_archive.type = "quote" AND tbl_pdf_archive.date LIKE "%'.$date.'%")';

        if(isset($_POST['submit'])){
            $this->data['whatYear'] = $_POST['year'];
            $this->data['whatMonth'] = $_POST['month'];

            $date = $_POST['year'].'-'.$_POST['month'];
            if($_POST['type'] == 1){
                $whatVal = '(tbl_pdf_archive.type = "quote" AND tbl_pdf_archive.date LIKE "%'.$_POST['year'].'%")';
            }else{
                $whatVal = '(tbl_pdf_archive.type = "quote" AND tbl_pdf_archive.date LIKE "%'.$date.'%")';
            }
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_pdf_archive.client_id'),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','file_name','client_id','type','date','download'),'tbl_pdf_archive.');
        $fields[] = 'DATE_FORMAT(tbl_pdf_archive.date,"%d/%m/%Y") as archive_date';
        $fields[] = 'tbl_client.client_name';
        $fields[] = 'tbl_client.client_code';

        $this->my_model->setSelectFields($fields);
        $quote_list = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,'');

        if(count($quote_list) > 0 ){
            foreach($quote_list as $v){
                $this->data['quote_list'][date('m/Y',strtotime($v->date))][] = (object)array(
                    'file_name' => $v->file_name,
                    'date' => $v->date,
                    'archive_date' => $v->archive_date,
                    'client_name' => $v->client_name,
                    'client_code' => $v->client_code,
                    'client_id' => $v->client_id
                );
            }
        }
        $this->data['page_load'] = 'backend/quote/archive_quote_view';
        $this->load->view('main_view',$this->data);
    }

    function outstandingBalance(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
        $this->data['client'] = array();
        $this->data['unpaid_inv'] = array();
        $this->my_model->setOrder('client_name');
        $client = $this->my_model->getInfo('tbl_client',true,'is_exclude !=');

        if(count($client) >0){
            foreach($client as $v){
                $statement = $this->my_model->getInfo('tbl_statement',array($v->id,false),array('client_id','is_archive'));
                $fields = array('max(date) as date','(sum(debits) - sum(credits)) as outstanding');
                $this->my_model->setSelectFields($fields,false);
                $archive_statement = $this->my_model->getInfo('tbl_statement',array($v->id,true),array('client_id','is_archive'));
                $balance = 0;
                $date = date('d-M-Y');
                if(count($archive_statement)>0){
                    foreach($archive_statement as $av){
                        $balance += $av->outstanding;
                        $date = $av->date;
                    }
                }
                if(count($statement)>0){
                    foreach($statement as $sv){
                        $balance += $sv->debits;
                        $balance -= $sv->credits;
                        $date = $sv->date;
                    }
                }
                $gst = $balance * 0.15;
                $gross = $gst + $balance;
                $statement_data = $this->my_model->getInfo('tbl_statement',array($v->id,'INVOICE',false),array('client_id','type','is_payed'));

                if(count($statement_data) > 0){
                    foreach($statement_data as $sv){
                        $this->data['unpaid_inv'][$v->id][] = $sv->reference.': $'.($sv->debits ? number_format($sv->debits,2) : '0.00');
                    }
                }

                $this->data['client'][] = (object)array(
                    'id' => $v->id,
                    'name' => $v->client_name,
                    'code' => $v->client_code,
                    'date' => $date ? date('d-M-Y',strtotime($date)) : '',
                    'net' => $balance,
                    'balance' => $balance,
                    'gst' => $gst,
                    'gross' => $gross
                );
            }
        }

        /*$this->displayarray($this->data['paid_inv']);exit;*/
        if(isset($_GET['print'])){
            $this->data['dir'] = 'pdf/outstanding/'.date('Y').'/'.date('F');
            if(!is_dir($this->data['dir'])){
                mkdir($this->data['dir'], 0777, TRUE);
            }

            $whatVal = array('Outstanding for '.date('d-F-y').'.pdf','outstanding');
            $whatFld = array('file_name','type');
            $has_data = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,$whatFld);

            $post = array(
                'file_name' => 'Outstanding for '.date('d-F-y').'.pdf',
                'type' => 'outstanding',
                'date' => date('Y-m-d')
            );

            if(count($has_data) > 0){
                foreach($has_data as $v){
                    $this->my_model->update('tbl_pdf_archive',$post,$v->id);
                }
            }else{
                $this->my_model->insert('tbl_pdf_archive',$post);
            }
            $this->load->view('backend/summary/print_outstanding_view',$this->data);
        }else{
            $this->data['page_load'] = 'backend/summary/outstanding_balance_view';
            $this->load->view('main_view',$this->data);
        }
    }

    function textTemplate(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
        $this->data['template'] = $this->my_model->getInfo('tbl_template');
        $this->data['page_load'] = 'backend/template/text_template_view';
        $this->load->view('main_view',$this->data);
    }

    function manageTextTemplate(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $action = $this->uri->segment(2);

        if(!$action){
            exit;
        }

        switch($action){
            case 'add':
                $this->load->view('backend/template/add_new_text_template_view',$this->data);

                if(isset($_POST['submit'])){
                    unset($_POST['submit']);

                    $this->my_model->insert('tbl_template',$_POST);
                    redirect('textTemplate');
                }
                break;
            case 'edit':
                $id = $this->uri->segment(3);
                if(!$id){
                    exit;
                }
                $this->data['text'] = $this->my_model->getInfo('tbl_template',$id);
                $this->load->view('backend/template/edit_new_text_template_view',$this->data);

                if(isset($_POST['submit'])){
                    unset($_POST['submit']);

                    $this->my_model->update('tbl_template',$_POST,$id);
                    redirect('textTemplate');
                }
                break;
            default:
                break;
        }
    }

    function userList(){

        $this->my_model->setNormalized('account_type','account_type');
        $this->my_model->setSelectFields(array('id','account_type'));
        $this->data['account_type'] = $this->my_model->getInfo('tbl_account_type');
        $this->data['account_type'][''] = 'All Account Type';

        ksort($this->data['account_type']);

        $this->data['status'][''] = 'Show All';
        $this->data['status']['1'] = 'Active';
        $this->data['status']['0'] = 'InActive';

        ksort($this->data['status']);

        $this->my_model->setSelectFields(
            array(
                'tbl_user.id',
                'tbl_user.name',
                'tbl_user.username',
                'tbl_user.email',
                'IF(tbl_user.date_registered != "0000-00-00",DATE_FORMAT(tbl_user.date_registered,"%d-%m-%Y"),"") as date_registered',
                'tbl_user.is_active',
                'tbl_user.account_type as account_type_id',
                'tbl_user.alias',
                'tbl_account_type.account_type'
            )
        );
        $this->my_model->setJoin(array(
            'table' => array('tbl_account_type'),
            'join_field' => array('id'),
            'source_field' => array('tbl_user.account_type'),
            'type' => 'left'
        ));

        $users = $this->my_model->getInfo('tbl_user');
        $this->data['users'] = json_encode($users);

        $this->data['page_load'] = 'backend/user/user_management_view';
        $this->load->view('main_view',$this->data);
    }

    function manageUser(){

        $page = $this->uri->segment(2);

        if(!$page){
            exit;
        }

        $this->my_model->setNormalized('account_type','id');
        $this->my_model->setSelectFields(array('id','account_type'));
        $this->data['account_type'] = $this->my_model->getInfo('tbl_account_type');

        ksort($this->data['account_type']);

        switch($page){
            case 'add':
                if(isset($_POST['submit'])){
                    unset($_POST['submit']);

                    $_POST['date_registered'] = date('Y-m-d');
                    $_POST['password'] = $this->encrypt->encode($_POST['password']);

                    $this->my_model->insert('tbl_user',$_POST);
                    
                    redirect('userList');
                }

                $this->load->view('backend/user/add_user_view',$this->data);
                break;
            case 'edit':
                $id = $this->uri->segment(3);

                if(!$id){
                    exit;
                }

                $this->data['user'] = $this->my_model->getInfo('tbl_user',$id);
                $this->load->view('backend/user/edit_user_view',$this->data);
                if(isset($_POST['submit'])){
                    unset($_POST['submit']);

                    $_POST['password'] = $this->encrypt->encode($_POST['password']);

                    $this->my_model->update('tbl_user',$_POST,$id);
                    redirect('userList');
                }
                break;
            case 'delete':
                $id = $this->uri->segment(3);

                if(!$id){
                    exit;
                }

                if(isset($_POST['submit'])){
                    unset($_POST['submit']);
                    $this->my_model->delete('tbl_user',$id);

                    redirect('userList');
                }
                $this->load->view('backend/user/delete_user_view',$this->data);
                break;
            default:
                break;
        }
    }

    function downloadForm(){

        $page = $this->uri->segment(2);

        $this->my_model->setNormalized('account_type','id');
        $this->my_model->setSelectFields(array('id','account_type'));
        $this->data['account_type'] = $this->my_model->getInfo('tbl_account_type');

        $this->my_model->setNormalized('menu_name','id');
        $this->my_model->setSelectFields(array('id','menu_name'));
        $this->data['downloadable_form'] = $this->my_model->getInfo('tbl_downloadable_form');

        if($page){
            switch($page){
                case 'upload':
                    if(isset($_POST['submit'])){
                        if(!empty($_FILES)) {
                            $uploadDir = realpath(APPPATH . '../uploads/form');
                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0777, TRUE);
                            }
                            $file_name = $_FILES['file_attachment']['name'];
                            $file      = $uploadDir . '/' . $file_name;

                            $is_exist = $this->my_model->getInfo('tbl_downloadable_form',$file_name,'file_name');

                            if (move_uploaded_file($_FILES['file_attachment']['tmp_name'], $file)) {
                                $post = array(
                                    'file_name' => $file_name,
                                    'menu_name' => $_POST['menu_name'],
                                    'uploader_id' => $this->session->userdata('user_id'),
                                    'date' => date('Y-m-d')
                                );

                                if(count($is_exist) > 0){
                                    foreach($is_exist as $v){
                                        $this->my_model->update('tbl_downloadable_form',$post,$v->id,'id',false);
                                    }
                                }else{
                                    $this->my_model->insert('tbl_downloadable_form',$post,false);
                                }
                            }
                        }

                        redirect('downloadForm');
                    }
                    $this->load->view('backend/download_form/upload_download_form_view',$this->data);
                    break;
                case 'new':
                    if(isset($_POST['submit'])){

                        if(count($_POST['form_id']) > 0){
                            foreach($_POST['form_id'] as $row){

                                if(count($_POST['account_type_id']) > 0){
                                    foreach($_POST['account_type_id'] as $val){
                                        $post = array(
                                            'account_type_id' => $val,
                                            'form_id' => $row,
                                            'date' => date('Y-m-d'),
                                            'user_id' => $this->session->userdata('user_id'),
                                            'is_used' => true
                                        );
                                        $whatVal = array($val,$row);                                        $whatFld = array('account_type_id','form_id');
                                        $is_exist = $this->my_model->getInfo('tbl_user_download_form',$whatVal,$whatFld);

                                        if(count($is_exist) > 0){
                                            foreach($is_exist as $v){
                                                $this->my_model->update('tbl_user_download_form',$post,$v->id,'id',false);
                                            }
                                        }else{
                                            $this->my_model->insert('tbl_user_download_form',$post,false);
                                        }
                                    }
                                }

                            }
                        }

                        redirect('downloadForm');

                    }

                    $this->load->view('backend/download_form/set_user_download_form_view',$this->data);
                    break;
                case 'menu':
                    header('Content-Type: application/json');

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

                    $this->my_model->setGroupBy('account_type_id');
                    $download_form = $this->my_model->getInfo('tbl_user_download_form',1,'is_used');

                    echo json_encode($download_form);
                    break;
                case 'form':
                    header('Content-Type: application/json');

                    $this->my_model->setGroupBy('menu_name');
                    $download_form = $this->my_model->getInfo('tbl_downloadable_form');

                    echo json_encode($download_form);
                    break;
                case 'edit_menu':
                    $user_menu = $this->my_model->getInfo('tbl_user_download_form',1,'is_used');
                    $this->data['account_ids'] = array();
                    $this->data['menu_ids'] = array();

                    if(count($user_menu) > 0){
                        foreach($user_menu as $row){
                            $this->data['account_ids'][$row->account_type_id] = $row->account_type_id;
                            $this->data['menu_ids'][$row->form_id] = $row->form_id;
                        }
                    }
                    if(isset($_POST['submit'])){

                        if(count($_POST['form_id']) > 0){
                            foreach($_POST['form_id'] as $row){
                                if(count($user_menu) > 0){
                                    foreach($user_menu as $v){
                                        $post = array(
                                            'is_used' => false
                                        );
                                        $this->my_model->update('tbl_user_download_form',$post,$v->id,'id',false);
                                    }
                                }
                                if(count($_POST['account_type_id']) > 0){
                                    foreach($_POST['account_type_id'] as $val){
                                        $post = array(
                                            'account_type_id' => $val,
                                            'form_id' => $row,
                                            'date' => date('Y-m-d'),
                                            'user_id' => $this->session->userdata('user_id'),
                                            'is_used' => true
                                        );
                                        $whatVal = array($val,$row);
                                        $whatFld = array('account_type_id','form_id');
                                        $is_exist = $this->my_model->getInfo('tbl_user_download_form',$whatVal,$whatFld);

                                        if(count($is_exist) > 0){
                                            foreach($is_exist as $v){
                                                $this->my_model->update('tbl_user_download_form',$post,$v->id,'id',false);
                                            }
                                        }else{
                                            $this->my_model->insert('tbl_user_download_form',$post,false);
                                        }
                                    }
                                }

                            }
                        }

                        redirect('downloadForm');

                    }

                    $this->load->view('backend/download_form/edit_all_user_download_form_view',$this->data);
                    break;
                case 'edit_form':
                    $id = $this->uri->segment(3);
                    if(!$id){
                        exit;
                    }

                    $this->my_model->setShift();
                    $this->data['form'] = (Object)$this->my_model->getInfo('tbl_downloadable_form',$id);

                    if(isset($_POST['submit'])){
                        if(!empty($_FILES)) {
                            $uploadDir = realpath(APPPATH . '../uploads/form');
                            if (!is_dir($uploadDir)) {
                                mkdir($uploadDir, 0777, TRUE);
                            }
                            $file_name = $_FILES['file_attachment']['name'];
                            $file      = $uploadDir . '/' . $file_name;

                            if($file_name){
                                if (move_uploaded_file($_FILES['file_attachment']['tmp_name'], $file)) {
                                    $post = array(
                                        'file_name' => $file_name,
                                        'menu_name' => $_POST['menu_name'],
                                        'uploader_id' => $this->session->userdata('user_id'),
                                        'date' => date('Y-m-d')
                                    );

                                    $this->my_model->update('tbl_downloadable_form',$post,$id,'id',false);
                                }
                            }else{
                                $post = array(
                                    'menu_name' => $_POST['menu_name'],
                                    'uploader_id' => $this->session->userdata('user_id')
                                );

                                $this->my_model->update('tbl_downloadable_form',$post,$id,'id',false);
                            }
                        }

                        redirect('downloadForm');
                    }
                    $this->load->view('backend/download_form/edit_upload_download_form_view',$this->data);
                    break;
                default:
                    break;
            }
        }else{
            $this->my_model->setSelectFields(array('id','account_type'));
            $account_type = $this->my_model->getInfo('tbl_account_type');
            $account_type_array = array();

            if(count($account_type) > 0){
                foreach($account_type as $row){
                    $account_type_array[] = array(
                        'id' => $row->id,
                        'account_type' => $row->account_type
                    );
                }
            }

            $this->data['account_type_json'] = json_encode($account_type_array);
            $this->data['page_load'] = 'backend/download_form/user_download_form_view';
            $this->load->view('main_view',$this->data);
        }
    }

    function sendEmailLogReview(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        if (isset($_POST['send'])) {

            $email_log = $this->my_model->getInfo('tbl_email_export_log',$_POST['id']);
            if(count($email_log) > 0){
                foreach($email_log as $data){

                    if(!$data->staff_id) {
                        $msg = 'Good day. <br/>Here is the attach file for Pay Period Summary Report from ';
                        $msg .= '<strong>' . date('j F Y', strtotime($data->pay_period)) . ' to ' . date('j F Y', strtotime('+6 days ' . $data->pay_period)) . '</strong>.';

                        $this_range = date('Y/F', mktime(0, 0, 0, date('m', strtotime($data->pay_period)), 1, date('Y', strtotime($data->pay_period))));

                        $result = $this->payPeriodSentEmail($data->pay_period, $this_range, $msg);

                        $post = array(
                            'user_id' => $this->session->userdata('user_id'),
                            'message' => json_encode($result['mail_settings']),
                            'email_type_id' => $result['is_send'] ? 1 : 8,
                            'type' => $result['result']->type,
                            'debug' => $result['result']->debug,
                            'date' => date('Y-m-d H:i:s')
                        );

                        $this->my_model->update('tbl_email_export_log', $post, $data->id,'id',false);
                        $whatVal       = array(
                            $data->week_number,
                            $data->pay_period
                        );
                        $whatFld       = array('week_num', 'date');
                        $week_pay_data = $this->my_model->getInfo('tbl_week_pay_period', $whatVal, $whatFld);

                        $_data = $this->getTotalStaffData($data->pay_period);
                        if (count($week_pay_data) > 0) {
                            foreach ($week_pay_data as $val) {
                                $post = array(
                                    'staff_count' => $_data['staff_count'],
                                    'total_wage' => $_data['wage_total'],
                                    'total_paye' => $_data['paye_total']
                                );
                                $this->my_model->update('tbl_week_pay_period', $post, $val->id);
                            }
                        } else {
                            $post = array(
                                'week_num' => $this->data['thisWeek'],
                                'date' => $data->pay_period,
                                'staff_count' => $_data['staff_count'],
                                'total_wage' => $_data['wage_total'],
                                'total_paye' => $_data['paye_total']
                            );
                            $this->my_model->insert('tbl_week_pay_period', $post);
                        }
                    }
                    else{
                        $this->sentAllStaffPaySlip($data->week_number,$data->pay_period,true,$data->staff_id,$_POST['id']);
                    }
                    redirect('emailLog');
                }
            }
        }
        if(isset($_POST['cancel'])){
            $this->my_model->update('tbl_email_export_log',array('type'=>3),$_POST['id']);
        }
    }

    function payPeriodSettings(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $this->data['year'] = getYear();
        $this->data['month'] = getMonth();

        $this->my_model->setNormalized('project_name','id');
        $this->my_model->setSelectFields(array('id','project_name'));
        $this->data['project_type'] = $this->my_model->getinfo('tbl_project_type');

        ksort($this->data['project_type']);

        if(isset($_POST['search'])){
            $this->session->set_userdata(array(
                '_year_session' => $_POST['year'],
                '_month_session' => $_POST['month'],
                '_week_session' => $_POST['week'],
                '_project_session' => $_POST['project_type']
            ));
            redirect('payPeriodSettings');
        }

        $year_session = $this->session->userdata('_year_session');
        $month_session = $this->session->userdata('_month_session');
        $week_session = $this->session->userdata('_week_session');
        $project_session = $this->session->userdata('_project_session');

        $_this_date = new DateTime();
        $this->data['thisYear'] = $year_session != '' ? $year_session : date('Y');
        $this->data['thisMonth'] = $month_session != '' ? $month_session : date('m');
        $this->data['thisWeek'] = $week_session != '' ? $week_session : $_this_date->format('W');
        $this->data['thisProject'] = $project_session != '' ? $project_session : 1;
        $this->data['week'] = getWeeksNumberInMonth($this->data['thisYear'],$this->data['thisMonth']);
        $week_data = getWeekDateInMonth($this->data['thisYear'],$this->data['thisMonth']);
        $week_date = $week_data[$this->data['thisWeek']];

        $id = $this->uri->segment(2);
        if($id){
            $this->my_model->setLastId('name');
            $this->my_model->setSelectFields(array('CONCAT(fname," ",lname) as name'));
            $staff_name = $this->my_model->getInfo('tbl_staff',$id);

            $this->data['page_name'] .= ' for '.$staff_name;
            $whatVal = 'tbl_staff.id ="'.$id.'"';
            $this->data['page_load'] = 'backend/pay_period/pay_period_settings_employee_view';
        }
        else{
            $whatVal = 'project_id = "'.$this->data['thisProject'].'" AND (date_employed != "0000-00-00" AND (status_id = "3" OR status_id="2"))';
            $whatVal .= ' OR (last_week_pay >= "' . $this->data['thisWeek'] . '")';
            $this->data['page_load'] = 'backend/pay_period/pay_period_settings_view';
        }

        $staff = new Staff_Helper();
        $staff_data = $staff->staff_details($whatVal,'');
        $hourly_rate = $staff->staff_hourly_rate();
        $employment_data = $staff->staff_employment(true);
        $kiwi_data = $staff->staff_kiwi();
        $rate = $staff->staff_rate();
        $data_ = array();
        $rate_cost = array();
        $rate_week_start = array();
        $staff_name = '';
        if(count($staff_data) > 0){
            foreach($staff_data as $ev){

                $staff_name = $ev->name;
                $ev->rate_name = '';
                $ev->rate_cost = 0;
                $ev->start_use = '';
                $salary_type = explode(' ',$ev->description);
                $ev->description = end($salary_type).' ('.$ev->salary_code.')';

                if($id){
                    $ref = 1;
                    $week_in_year = array();
                    if(count(@$employment_data[$id]) > 0){
                        foreach(@$employment_data[$id] as $used_date=>$val){
                            $ev->date_employed = $val->date_employed;
                            $ev->date_last_pay = $val->date_last_pay;
                            $ev->last_week_pay = $val->last_week_pay;
                            $ev->has_final_pay = $val->has_final_pay;

                            $end = $ev->date_last_pay ? $ev->date_last_pay : date('Y-m-d');
                            $week_in_year[] = getWeekBetweenDates($ev->date_employed,$end);
                        }
                    }
                    $data_array = array();
                    if(count($week_in_year) > 0){
                        foreach($week_in_year as $key=>$data){
                            if(count($data) > 0){
                                foreach($data as $k=>$v){
                                    $data_array[$k] = $v;
                                }
                            }
                        }
                    }
                    ksort($data_array);
                    if(count($data_array) > 0){
                        foreach($data_array as $year_week=>$date){
                            $week_year = explode('-',$year_week);
                            if (count(@$rate[$ev->id]) > 0) {
                                foreach (@$rate[$ev->id] as $used_date => $val) {
                                    if (strtotime($used_date) <= strtotime($date) ||
                                        strtotime($used_date) <= strtotime(date('Y-m-d', strtotime('+6 days ' . $date)))) {
                                        $rate_name = explode(' ',$val->rate_name);
                                        //$ev->rate_name = $val->rate_name;
                                        $val->rate_cost = $this->is_decimal($val->rate_cost) ? number_format($val->rate_cost,2,'.','') : $val->rate_cost;
                                        $ev->rate_name = end($rate_name).' ($ '.$val->rate_cost.')';
                                        //$ev->rate_cost = $val->rate;
                                        $ev->start_use = $val->start_use;
                                        $start_week = new DateTime($used_date);
                                        $start_use_week = $start_week->format('W');
                                        $year_ = $start_week->format('Y');
                                        $ev->rate_cost = $val->rate_cost;
                                        $rate_cost[$val->rate_cost] =  $val->rate_cost;
                                        $rate_week_start[$val->rate_cost] = $start_use_week.'-'.$year_;
                                    }
                                }
                            }

                            if(count(@$kiwi_data[$ev->id]) > 0){
                                foreach(@$kiwi_data[$ev->id] as $start_use=>$val){
                                    if(strtotime($start_use) <= strtotime($date) ||
                                        strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$date)))){
                                        $ev->kiwi = $val->kiwi;
                                        $ev->employer_kiwi = $val->employer_kiwi;
                                        $ev->cec_name = $val->cec_name;
                                        $ev->field_name = $val->field_name;
                                        $ev->esct_rate = $val->esct_rate;
                                    }
                                }
                            }

                            $ev->esct_rate = $ev->esct_rate ? ($this->is_decimal($ev->esct_rate) ? number_format($ev->esct_rate, 2) : $ev->esct_rate ) : '';
                            $ev->kiwi      = $ev->kiwi ? $ev->kiwi  : '';
                            $ev->emp_kiwi  = $ev->emp_kiwi ? $ev->emp_kiwi  : '';

                            $ev->hourly_rate = 0;
                            if (count(@$hourly_rate[$ev->id]) > 0) {
                                foreach (@$hourly_rate[$ev->id] as $used_date => $val) {
                                    if (strtotime($used_date) <= strtotime($date) ||
                                        strtotime($used_date) <= strtotime(date('Y-m-d', strtotime('+6 days ' . $date)))) {
                                        $ev->hourly_rate = $val->hourly_rate;
                                    }
                                }
                            }
                            $previous_rate = end(array_slice($rate_cost, - (count($rate_cost) - 2),1));
                            $current_rate = end($rate_cost);
                            $rate_value = '';
                            if(count($rate_cost) > 1){
                                $rate_value = '$ '.number_format($current_rate - $previous_rate,2);
                            }
                            $rate_week_year = @$rate_week_start[$ev->rate_cost];

                            if($ev->rate_name) {
                                $data_[] = (object)array(
                                    'id' => $ref,
                                    'rate_name' => $ev->rate_name,
                                    'year' => $week_year[1],
                                    'week' => $week_year[0],
                                    'week_ending' => $week_year[0] == 30 && $week_year[1] == 2015 ? date('d-M-Y',strtotime('+5 days '.$date)) : date('d-M-Y',strtotime('+6 days '.$date)),
                                    'rate_cost' => $ev->rate_cost,
                                    'description' => $ev->description,
                                    'hourly_rate' => $ev->hourly_rate ? '$'.number_format($ev->hourly_rate,2) : '',
                                    'pay_increase' =>  $rate_week_year == $year_week ? $rate_value : '',
                                    'has_pay_increase' =>  count($rate_cost) > 1 && $rate_week_year == $year_week ? 1 : 0,
                                    'esct_rate' => $ev->esct_rate ? $ev->esct_rate .'%' : '',
                                    'kiwi' => $ev->kiwi ? $ev->kiwi .'%' : '',
                                    'emp_kiwi' => $ev->emp_kiwi ? $ev->emp_kiwi .'%' : '',
                                    'name' => $ev->name,
                                    'frequency' => $ev->frequency,
                                    'staff_status' => $ev->staff_status,
                                    'tax_code' => $ev->tax_code,
                                    'color' => $ev->color
                                );
                                $ref++;
                            }
                        }
                    }
                }
                else{
                    if(count(@$employment_data[$ev->id]) > 0){
                        foreach(@$employment_data[$ev->id] as $used_date=>$val){
                            if(
                                strtotime($used_date) <= strtotime($week_date) ||
                                strtotime($used_date) <= strtotime(date('Y-m-d',strtotime('+6 days '.$week_date)))){
                                $ev->date_employed = $val->date_employed;
                                $ev->date_last_pay = $val->date_last_pay;
                                $ev->last_week_pay = $val->last_week_pay;
                                $ev->has_final_pay = $val->has_final_pay;
                            }
                        }
                    }

                    if (count(@$rate[$ev->id]) > 0) {
                        foreach (@$rate[$ev->id] as $used_date => $val) {
                            if (strtotime($used_date) <= strtotime($week_date) ||
                                strtotime($used_date) <= strtotime(date('Y-m-d', strtotime('+6 days ' . $week_date)))) {
                                $rate_name = explode(' ',$val->rate_name);
                                //$ev->rate_name = $val->rate_name;
                                $val->rate_cost = $this->is_decimal($val->rate_cost) ? number_format($val->rate_cost,2) : $val->rate_cost;
                                $ev->rate_name = end($rate_name).' ($ '.$val->rate_cost.')';
                                $ev->rate_cost = $val->rate;
                                $ev->start_use = $val->start_use;
                            }
                        }
                    }

                    if(count(@$kiwi_data[$ev->id]) > 0){
                        foreach(@$kiwi_data[$ev->id] as $start_use=>$val){
                            if(strtotime($start_use) <= strtotime($week_date) ||
                                strtotime($start_use) <= strtotime(date('Y-m-d',strtotime('+6 days '.$week_date)))){
                                $ev->kiwi = $val->kiwi;
                                $ev->employer_kiwi = $val->employer_kiwi;
                                $ev->cec_name = $val->cec_name;
                                $ev->field_name = $val->field_name;
                                $ev->esct_rate = $val->esct_rate;
                            }
                        }
                    }

                    $ev->esct_rate = $ev->esct_rate ? ($this->is_decimal($ev->esct_rate) ? number_format($ev->esct_rate, 2) : $ev->esct_rate) : '';
                    $ev->kiwi      = $ev->kiwi ? $ev->kiwi  : '';
                    $ev->emp_kiwi  = $ev->emp_kiwi ? $ev->emp_kiwi : '';

                    $ev->hourly_rate = 0;
                    if (count(@$hourly_rate[$ev->id]) > 0) {
                        foreach (@$hourly_rate[$ev->id] as $used_date => $val) {
                            if (strtotime($used_date) <= strtotime($week_date) ||
                                strtotime($used_date) <= strtotime(date('Y-m-d', strtotime('+6 days ' . $week_date)))) {
                                $ev->hourly_rate = $val->hourly_rate;
                            }
                        }
                    }

                    if($ev->rate_name){
                        $data_[] = (object)array(
                            'id' => $ev->id,
                            'rate_name' => $ev->rate_name,
                            'rate_cost' => $ev->rate_cost,
                            'description' => $ev->description,
                            'hourly_rate' => $ev->hourly_rate,
                            'esct_rate' => $ev->esct_rate ? $ev->esct_rate.'%' : '',
                            'kiwi' => $ev->kiwi ? $ev->kiwi.'%' : '',
                            'emp_kiwi' => $ev->emp_kiwi ? $ev->emp_kiwi.'%' : '',
                            'name' => $ev->name,
                            'frequency' => $ev->frequency,
                            'staff_status' => $ev->staff_status,
                            'tax_code' => $ev->tax_code,
                            'color' => $ev->color
                        );
                    }
                }
            }
        }

        if(isset($_GET['p']) && $_GET['p'] == 1 && $id){
            $dir = realpath(APPPATH.'../pdf');
            $real_path = $dir.'/pay period/settings';
            if (!is_dir($real_path)) {
                mkdir($real_path, 0777, TRUE);
            }
            $this->data['dir'] = $real_path;
            $this->data['staff_data'] = $data_;
            $this->data['file_name'] = date('Ymd').'_Pay_Period_Settings_'.str_replace(' ','',$staff_name);
            $this->data['page_name'] = 'Pay Period Settings for '.$staff_name;
            $this->load->view('backend/pay_period/print_pay_period_settings_employee_view',$this->data);
        }else{
            $this->data['staff_data'] = $id ? json_encode($data_) : $data_;
            $this->load->view('main_view',$this->data);
        }
    }

    function payAdjustment(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
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

        $this->my_model->setNormalized('adjustment_type','id');
        $this->my_model->setSelectFields(array('id','adjustment_type'));
        $this->data['adjustment_type'] = $this->my_model->getInfo('tbl_adjustment_type');
        $this->data['adjustment_type'][''] = 'Select Type';

        ksort($this->data['adjustment_type']);

        $whatVal = array($staff_id,$week,$this->data['date']);
        $whatFld = array('staff_id','week_number','date');
        $this->data['adjustment'] = $this->my_model->getInfo('tbl_adjustment',$whatVal,$whatFld);
        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            if(
                count($_POST['amount']) > 0
                || count($_POST['adjustment_type_id']) > 0
                || count($_POST['notes']) > 0
            ){
                foreach($_POST['amount'] as $k=>$v){
                    if($v){
                        $ref = $k + 1;
                        $post = array(
                            'amount' => $v,
                            'staff_id' => $_POST['staff_id'],
                            'date' => $_POST['date'],
                            'reference' => $k + 1,
                            'adjustment_type_id' => $_POST['adjustment_type_id'][$k],
                            'notes' => $_POST['notes'][$k],
                            'week_number' => $_POST['week_number'],
                        );

                        $whatVal = array($_POST['staff_id'],$_POST['week_number'],$_POST['date'],$ref);
                        $whatFld = array('staff_id','week_number','date','reference');

                        $record_exist = $this->my_model->getInfo('tbl_adjustment',$whatVal,$whatFld);

                        if(count($record_exist) > 0){
                            foreach($record_exist as $val){
                                $this->my_model->update('tbl_adjustment',$post,$val->id);
                            }
                        }
                        else{
                            $this->my_model->insert('tbl_adjustment',$post,false);
                        }
                    }
                }
            }

        }else{
            $this->load->view('backend/dtr/adjustment_view',$this->data);
        }
    }

    function adjustmentsReport(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

        $this->data['year'] = getYear();
        $this->data['year'][''] = 'Year';
        $this->data['month'] = getMonth();
        $this->data['month'][''] = 'Select Month';
        $this->data['period'] = payPeriodDropdown();
        $this->data['period'][''] = 'Select Period';

        if(isset($_POST['submit'])){
            unset($_POST['submit']);
            $this->session->set_userdata(array('filter_data' => $_POST));
            redirect('adjustmentsReport');
        }
        $filter_data = (Object)$this->session->userdata('filter_data');

        $whatVal = date('Y');
        $whatFld = 'YEAR(date) =';
        if(count($filter_data) > 0){
            if(@$filter_data->type && (@$filter_data->_period || @$filter_data->_year || @$filter_data->_month)){
                switch($filter_data->type){
                    case 3:
                        $this_month_year = date('Ym');
                        if(array_key_exists('_period',$filter_data)){
                            if($this_month_year != $filter_data->_period){
                                $whatVal = 'EXTRACT(YEAR_MONTH FROM date) <=' . $this_month_year;
                                $whatVal .= ' AND EXTRACT(YEAR_MONTH FROM date) >=' . $filter_data->_period;
                            }
                            else{
                                $whatVal = 'EXTRACT(YEAR_MONTH FROM date) =' . $filter_data->_period;
                            }
                        }
                        $whatFld = '';
                        break;
                    default:
                        $whatVal = 'YEAR(date) =' . $filter_data->_year;
                        $whatFld = '';
                        if(@$filter_data->_month){
                            $whatVal .= ' AND MONTH(date) =' . $filter_data->_month;
                        }

                        if(@$filter_data->filter){
                            $whatVal .= ' AND CONCAT(fname, " ", lname) LIKE "%' . @$filter_data->filter.'%"';
                        }
                        break;
                }
            }
        }

        $this->my_model->setOrder('date');
        $this->my_model->setJoin(array(
            'table' => array('tbl_staff','tbl_adjustment_type'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_adjustment.staff_id','tbl_adjustment.adjustment_type_id'),
            'type' => 'left'
        ));
        $fld = ArrayWalk($this->my_model->getFields('tbl_adjustment'),'tbl_adjustment.');
        $fld[] = 'CONCAT(tbl_staff.fname," ",tbl_staff.lname) as name';
        $fld[] = 'tbl_staff.fname';
        $fld[] = 'tbl_staff.lname';
        $fld[] = 'tbl_adjustment_type.adjustment_type';
        $fld[] = 'tbl_adjustment_type.adjustment_code';
        $this->my_model->setSelectFields($fld);
        $adjustment = $this->my_model->getInfo('tbl_adjustment',$whatVal,$whatFld);
        $data = array();
        if(count($adjustment) > 0){
            foreach($adjustment as $val){
                $year = date('Y',strtotime($val->date));

                $_added_days = $val->week_number == 30 && date('Y',strtotime($val->date)) == 2015 ? '+5 days ': '+6 days ';
                $val->date_period = date('M d/y',strtotime($val->date));
                $val->date_period .= '-' . date('M d/y',strtotime($_added_days . $val->date));
                $val->date_period .= ' [Week ' . $val->week_number . ']';
                $data[$year][$val->date_period][$val->name][] = $val;
            }
        }

        $this->data['adjustment'] = $data;
        $this->data['filter_data'] = $filter_data;
        $this->data['page_load'] = 'backend/adjustment/adjustment_report_view';
        $this->load->view('main_view',$this->data);
    }
}