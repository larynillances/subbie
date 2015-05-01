<?php
include('subbie.php');

class Admin_Controller extends Subbie{

    //region quote
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

        $fields = $this->arrayWalk(array('id','client_id','job_address','is_accepted','is_archive'),'tbl_quotation.');
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
        $fields = $this->arrayWalk(array('id','address','client_id','job_name'),'tbl_registration.');
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
        if($this->session->userdata('is_logged_in') == false){
            redirect('');
        }

        $this->data['dtr'] = array();
        $this->data['absent'] = array();
        $this->data['job_assign'] = array();
        $this->my_model->setJoin(array(
            'table' => array('tbl_absent_type'),
            'join_field' => array('id'),
            'source_field' => array('tbl_absent_reason.absent_type_id'),
            'type' => 'left'
        ));

        $fields = $this->arrayWalk(array('id','staff_id','start_date','end_date'),'tbl_absent_reason.');
        $fields[] = 'tbl_absent_type.string';

        $this->my_model->setSelectFields($fields);
        $absent = $this->my_model->getinfo('tbl_absent_reason');

        $this->my_model->setJoin(array(
            'table' => array('tbl_registration','tbl_client'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_job_assign.job_id','tbl_registration.client_id'),
            'type' => 'left'
        ));
        $this->my_model->setSelectFields(array('tbl_job_assign.staff_id','tbl_job_assign.start_date','CONCAT(tbl_client.client_code,LPAD(tbl_job_assign.job_id, 5,"0")) as job_ref'));
        $job_assign = $this->my_model->getinfo('tbl_job_assign');

        $this->my_model->setSelectFields(array(
            'TIMESTAMPDIFF(SECOND, time_in, time_out) as hours',
            'time_in','time_out','staff_id','date',
            'id as dtr_id'
        ));
        $dtr = $this->my_model->getinfo('tbl_login_sheet');

        if(count($absent) > 0){
            foreach($absent as $av){
                $this->data['absent'][$av->staff_id][$av->start_date] = $av->string;
            }
        }
        if(count($job_assign) > 0){
            foreach($job_assign as $jv){
                $this->data['job_assign'][$jv->staff_id][$jv->start_date] = $jv->job_ref;
            }
        }
        if(count($dtr) > 0){
            foreach($dtr as $v){
                $minutes = (int)($v->hours/60);
                $hoursValue = (int)($minutes/60);
                $minutesValue = $minutes - ($hoursValue * 60);
                //$secondsValue = $v->hours - (($hoursValue * 3600) + ($minutesValue * 60));
                $hours = str_pad($hoursValue, 2, '0', STR_PAD_LEFT) . "." . str_pad($minutesValue, 2, '0', STR_PAD_LEFT);

                $this->data['dtr'][$v->staff_id][$v->date] = array(
                    'time_in' => $v->time_in != '' ? date('H:i',strtotime($v->time_in)) : '',
                    'time_out' => $v->time_out != '' ? date('H:i',strtotime($v->time_out)) : '',
                    'hours' => $hours != '00.00' ? $hours : '&nbsp;',
                    'seconds' => $v->hours,
                    'dtr_id' => $v->dtr_id,
                    'date' => $v->date
                );
            }
        }

        //$this->displayarray($this->data['job_assign']);
        $this->data['staff'] = $this->my_model->getinfo('tbl_staff');

        $this->data['page_load'] = 'backend/dtr/dtr_view';
        $this->load->view('main_view',$this->data);
    }

    function timeSheetEdit(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();

        $this->data['dtr'] = array();
        $this->data['job_assign'] = array();
        $this->data['job'] = '';
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
        $this->data['staff'] = $this->my_model->getinfo('tbl_staff');

        $this->my_model->setSelectFields(array(
            'TIMESTAMPDIFF(SECOND, time_in, time_out) as hours',
            'time_in','time_out','staff_id','date',
            'id as dtr_id','working_type_id'
        ));
        $dtr = $this->my_model->getinfo('tbl_login_sheet');
        if(count($job)>0){
            foreach($job as $jv){
                $this->data['job'] = $jv->job_ref;
            }
        }

        $tuesday = date('d',strtotime('last Tuesday'));
        $thisDay = date('N') == 2 ? date('d') : $tuesday;
        if(isset($_POST['search'])){
            $this->data['thisYear'] = $_POST['year'];
            $this->data['thisMonth'] = $_POST['month'];
            $this->data['thisDay'] = date('d',strtotime($_POST['days']));
            $this->session->set_userdata(array(
                'year' => $_POST['year'],
                'month' => $_POST['month'],
                'day' => $this->data['thisDay']
            ));
            //$this->displayarray($this->session->userdata('year'));exit;
        }
        $this->data['thisYear'] = $this->session->userdata('year') != '' ? $this->session->userdata('year') : date('Y');
        $this->data['thisMonth'] = $this->session->userdata('month') != '' ? $this->session->userdata('month') : date('m');
        $this->data['thisDay'] = $this->session->userdata('day') != '' ? $this->session->userdata('day') : $thisDay;

        $this->getNumberOfWeeks($this->data['thisYear'],$this->data['thisMonth']);
        //$this->displayarray($this->data['days']);exit;

        $date = mktime(0, 0, 0, $this->data['thisMonth'],$this->data['thisDay'],$this->data['thisYear']);
        $this->getWeekDays($this->data['thisMonth'],$this->data['thisDay'],$this->data['thisYear']);
        //$this->displayarray($this->data['days_of_week']);

        $this->data['week_number'] = (int)date('W', $date);

        if(isset($_POST['submit'])){
            $date = mktime(0, 0, 0, $_POST['month'],date('d',strtotime($_POST['days'])),$_POST['year']);
            //$str_date = $_POST['year'] . "-" . $_POST['month'] . "-" . $_POST['days'];
            $week = (int)date('W', $date);
            $dt = new DateTime();

            for($whatDay=2; $whatDay<=8; $whatDay++){
                $getDate =  $dt->setISODate($_POST['year'], $week , $whatDay)->format('Y-m-d');
                $d = date('j',strtotime($getDate));
                if(count($this->data['staff']) >0){
                    foreach($this->data['staff'] as $staff){
                        $hasVal = $this->my_model->getinfo('tbl_login_sheet',array($staff->id,$getDate),array('staff_id','date'));

                        $this->my_model->setLastId('id');
                        $id = (int)$this->my_model->getinfo('tbl_login_sheet',array($staff->id,$getDate),array('staff_id','date'));

                        $exclude_day = array(6,7);
                        if($_POST['working_type'][$getDate] == 2){
                            $whatSpecificDay = date('N',strtotime($getDate));
                            if($staff->id != 4){
                                if(in_array($whatSpecificDay,$exclude_day)){
                                    $post = array(
                                        'staff_id' => $staff->id,
                                        'date' => $getDate,
                                        'time_in' => $getDate.' 00:00:00',
                                        'time_out' => $getDate.' 00:00:00',
                                        'working_type_id' => $_POST['working_type'][$getDate]
                                    );
                                }else{
                                    $post = array(
                                        'staff_id' => $staff->id,
                                        'date' => $getDate,
                                        'time_in' => $getDate.' 08:00:00',
                                        'time_out' => $getDate.' 12:00:00',
                                        'working_type_id' => $_POST['working_type'][$getDate]
                                    );
                                }


                                if(count($hasVal) >0){
                                    $this->my_model->update('tbl_login_sheet',$post,$id);
                                }else{
                                    $this_id = $this->my_model->insert('tbl_login_sheet',$post);
                                }
                            }
                        }

                        if(isset($_POST['time_in_'.$staff->id])){
                            //echo $id;exit;
                            if(@$_POST['time_in_'.$staff->id][$d] != ''){
                                $str_hours = str_split(@$_POST['time_in_'.$staff->id][$d],2);
                                $str_hours[0] = $str_hours[0] > 23 ? '00' : $str_hours[0];
                                $str_hours[1] = $str_hours[1] > 59 ? '00' : $str_hours[1];
                                $post = array(
                                    'staff_id' => $staff->id,
                                    'date' => $getDate,
                                    'time_in' => $getDate.' '.$str_hours[0].':'.$str_hours[1].':00',
                                    'working_type_id' => $_POST['working_type'][$getDate] ? $_POST['working_type'][$getDate] : 1
                                );
                                if(count($hasVal) >0){
                                    $this->my_model->update('tbl_login_sheet',$post,$id);
                                }else{
                                    $this_id = $this->my_model->insert('tbl_login_sheet',$post);
                                }
                            }
                            if(@$_POST['time_out_'.$staff->id][$d] != ''){
                                $str_hours = str_split(@$_POST['time_out_'.$staff->id][$d],2);
                                $str_hours[0] = $str_hours[0] > 23 ? '00' : $str_hours[0];
                                $str_hours[1] = $str_hours[1] > 59 ? '00' : $str_hours[1];
                                $post = array(
                                    'time_out' => $getDate.' '.$str_hours[0].':'.$str_hours[1].':00'
                                );
                                $thisId = count($hasVal) > 0 ? $id : $this_id;
                                $this->my_model->update('tbl_login_sheet',$post,$thisId);
                            }
                        }
                    }
                }
            }
            redirect('timeSheetEdit');
        }

        if(count($job_assign) > 0){
            foreach($job_assign as $jv){
                $this->data['job_assign'][$jv->staff_id][$jv->date] = array(
                    'job_ref' => $jv->job_ref,
                    'job_id' => $jv->id
                );
            }
        }
        //$this->displayarray($this->data['job_assign']);
        $this->data['working'] = array();
        if(count($dtr) > 0){
            foreach($dtr as $v){
                //$minutes = (int)($v->hours/60);
                $hoursValue = number_format(($v->hours/3600),2);
                //$minutesValue = $minutes - ($hoursValue * 60);
                //$secondsValue = $v->hours - (($hoursValue * 3600) + ($minutesValue * 60));
                $hours = $hoursValue;

                $this->data['dtr'][$v->staff_id][$v->date] = array(
                    'time_in' => $v->time_in != '' ? date('Hi',strtotime($v->time_in)) : '',
                    'time_out' => $v->time_out != '' ? date('Hi',strtotime($v->time_out)) : '',
                    'hours' => $hours != '00.00' ? $hours : '&nbsp;',
                    'seconds' => $v->hours,
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

        $this->data['page_load'] = 'backend/dtr/edit_dtr_view';
        $this->load->view('main_view',$this->data);
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

                $fields = $this->arrayWalk(array('id','staff_id','hours','date','job_id'),'tbl_job_assign.');
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

                $fields = $this->arrayWalk(array('id','staff_id','hours','date','job_id'),'tbl_job_assign.');
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
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_registration.client_id'),
            'type' => 'left'
        ));
        $fields = $this->arrayWalk(array('id','address','client_id','is_invoice','job_name'),'tbl_registration.');
        $fields[] = 'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref';
        $this->my_model->setSelectFields($fields);
        $job = $this->my_model->getInfo('tbl_registration');
        $this->data['job'] = array();
        if(count($job) >0){
            foreach($job as $v){
                $address = (object)json_decode($v->address);
                $this->data['job'][$v->id] = $v->job_ref.' ('.$v->job_name.')';
            }
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
        if(!$id){
            exit;
        }
        $this->my_model->setOrder('product_name');
        $this->data['product_list'] = $this->my_model->getInfo('tbl_product_list',$id,'supplier_id');

        $this->load->view('backend/order_book/product_table_load_list_view',$this->data);
    }

    function invoiceCreate(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }
        $this->my_model->setNormalized('client_name','id');
        $this->my_model->setSelectFields(array('id','client_name'));
        $this->data['client'] = $this->my_model->getInfo('tbl_client');

        $this->my_model->setNormalized('title','value');
        $this->my_model->setSelectFields(array('title','value'));
        $this->data['template_text'] = $this->my_model->getInfo('tbl_template');
        $this->data['template_text'][''] = '-';

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_registration.client_id'),
            'type' => 'left'
        ));
        $fields = $this->arrayWalk(array('id','address','client_id','is_invoice','job_name'),'tbl_registration.');
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
            $this->my_model->insert('tbl_invoice',$_POST,false);
            $this->my_model->update('tbl_registration',array('is_invoice' => true),$_POST['job_id'],'id',false);
            redirect('jobInvoice/'.$_POST['client_id']);
        }
        $this->load->view('backend/invoice/create_invoice_view',$this->data);
    }

    function supplierList(){
        if($this->session->userdata('is_logged_in') === false){
            redirect('');
        }

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

        $fields = $this->arrayWalk(array('id','product_name','quantity','type','price','supplier_id'),'tbl_product_list.');
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

        $fields = $this->arrayWalk(array('job_id','supplier_id','order_ref','date'),'tbl_order_send.');
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
                $fields = $this->arrayWalk(array('product_id','quantity'),'tbl_order_book.');
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
        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();
        $this->data['whatYear'] = date('Y');
        $this->data['whatMonth'] = date('m');

        $date = $this->data['whatYear'].'-'.$this->data['whatMonth'];
        $whatVal = '(tbl_pdf_archive.type = "invoice" AND tbl_pdf_archive.date LIKE "%'.$date.'%")';

        if(isset($_POST['submit'])){
            $this->data['whatYear'] = $_POST['year'];
            $this->data['whatMonth'] = $_POST['month'];

            $date = $_POST['year'].'-'.$_POST['month'];
            if($_POST['type'] == 1){
                $whatVal = '(tbl_pdf_archive.type = "invoice" AND tbl_pdf_archive.date LIKE "%'.$_POST['year'].'%")';
            }else{
                $whatVal = '(tbl_pdf_archive.type = "invoice" AND tbl_pdf_archive.date LIKE "%'.$date.'%")';
            }
        }

        $this->my_model->setJoin(array(
            'table' => array('tbl_client'),
            'join_field' => array('id'),
            'source_field' => array('tbl_pdf_archive.client_id'),
            'type' => 'left'
        ));
        $fields = $this->arrayWalk(array('id','file_name','client_id','type','date','download'),'tbl_pdf_archive.');
        $fields[] = 'DATE_FORMAT(tbl_pdf_archive.date,"%d/%m/%Y") as archive_date';
        $fields[] = 'tbl_client.client_name';
        $fields[] = 'tbl_client.client_code';

        $this->my_model->setSelectFields($fields);
        $invoice_list = $this->my_model->getInfo('tbl_pdf_archive',$whatVal,'');

        if(count($invoice_list) > 0 ){
            foreach($invoice_list as $v){
                $file_name = explode(' ',$v->file_name);
                $this->my_model->setShift();
                $getAmount = @(Object)$this->my_model->getInfo('tbl_statement',array($file_name[0],'INVOICE'),array('reference','type'));
                $this->data['invoice_list'][date('m/Y',strtotime($v->date))][] = (object)array(
                    'id' => $v->id,
                    'file_name' => $v->file_name,
                    'date' => $v->date,
                    'archive_date' => $v->archive_date,
                    'amount' => @number_format(@$getAmount->debits,2),
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
        $this->data['year'] = $this->getYear();
        $this->data['month'] = $this->getMonth();
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
        $fields = $this->arrayWalk(array('id','file_name','client_id','type','date','download'),'tbl_pdf_archive.');
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
                        $this->data['unpaid_inv'][$v->id][] = $sv->reference.': $'.number_format($sv->debits,2);
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

}