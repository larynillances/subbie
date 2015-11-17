<?php

include 'subbie.php';

class Leave_Controller extends Subbie{

    //region Leave
    function staffLeave(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $this->data['page_load'] = 'backend/leave/leave_view';

        $this->staffLeaveDropDowns(1, $this->data['account_type']);

        $this->my_model->setNormalized('name','id');
        $this->my_model->setSelectFields(array('CONCAT(fname," ",lname) as name','id','project_id'));
        $this->data['staff'] = $this->my_model->getInfo('tbl_staff', 3, 'status_id');
        $this->data['staff'][''] = 'Staff';
        ksort( $this->data['staff']);

        $holidays_array = $this->subbie_date_helper->holidays;
        $fields = array(
            'tbl_leave.id',
            'DATE_FORMAT(tbl_leave.date_requested, "%d/%m/%Y") as date',
            'CONCAT(requester.fname, " ", requester.lname) as user',
            'tbl_leave.user_id',
            'tbl_leave.reason_request as reason',
            'CONCAT(DATE_FORMAT(tbl_leave.leave_start, "%d/%m/%Y %H:%i"), " - ", DATE_FORMAT(tbl_leave.leave_end, "%d/%m/%Y %H:%i")) as range_date',
            'tbl_leave_type.type as type',
            'tbl_leave.type as type_id',
            'IF(tbl_leave_decision.id IS NULL, "Pending", tbl_leave_decision.decision) as status',
            'tbl_leave.decision as status_id',
            'IF(tbl_leave.decision != 0, 1, 0) as hasDecision',

            'tbl_leave.leave_start',
            'tbl_leave.reason_decision',
            'tbl_leave.leave_end',

            'actioned.name as actioned_by',
        );

        $whatField = array('');
        $whatVal = array('tbl_leave.id IS NOT NULL');

        $this->my_model->setSelectFields($fields, false);
        $this->my_model->setOrder('tbl_leave.date_requested', 'DESC');
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_staff as requester', 'tbl_user as actioned', 'tbl_leave_type', 'tbl_leave_decision'
            ),
            'join_field' => array(
                'id', 'id', 'id', 'id'
            ),
            'source_field' => array(
                'tbl_leave.user_id', 'tbl_leave.actioned_by','tbl_leave.type', 'tbl_leave.decision'
            ),
            'join_append' => array(
                'requester', 'actioned', 'tbl_leave_type', 'tbl_leave_decision'
            ),
            'type' => 'left'
        ));
        $leave = $this->my_model->getInfo('tbl_leave', $whatVal, $whatField);
        if(count($leave) > 0){
            foreach($leave as $v){
                $days_diff = $this->getLeaveDaysCount($v->leave_start, $v->leave_end, $holidays_array);
                $v->duration = $days_diff;
            }
        }
        $this->data['leave'] = json_encode($leave, JSON_NUMERIC_CHECK);
        $this->load->view('main_view', $this->data);
    }

    function staffLeaveAdd(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $this->data['page_load'] = 'backend/leave/leave_add';

        if(isset($_POST['submit'])){
            $user_id = $_POST['user_id'];
            $leave_start = str_replace("/", "-", $_POST['leave_start']);
            $leave_end = str_replace("/", "-", $_POST['leave_end']);
            $day_type = $_POST['leave_range'];
            $post = array(
                'date_requested' => date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $_POST['date_requested']))),
                'leave_start' => date('Y-m-d H:i:s', strtotime($leave_start)),
                'leave_end' => date('Y-m-d H:i:s', strtotime($leave_end)),
                'leave_range' => $_POST['leave_range'],
                'type' => $_POST['type'],
                'user_id' => $user_id,
                'reason_request' => nl2br($_POST['reason_request']),
                'submitted_by' => $this->session->userdata('user_id')
            );
            if(isset($_POST['decision'])){
                $post['decision'] = $_POST['decision'];
                $post['actioned_by'] = $this->session->userdata('user_id');
                $post['date_decision'] = date('Y-m-d H:i:s');
            }
            if(isset($_POST['reason_decision'])) {
                $post['reason_decision'] = nl2br($_POST['reason_decision']);
            }

            $this->my_model->insert('tbl_leave', $post, false);

            //region Audit Log
            $log = array(
                'update_type' => 1,
                'user_id' => $this->session->userdata('user_id'),
                'to' => json_encode($post)
            );
            $this->my_model->insert('tbl_leave_audit_log', $log, false);
            //endregion

            //region Send Mail
            if(!in_array($this->data['account_type'], array(3))) {
                $this->my_model->setSelectFields(array('tbl_leave_emails.emails'));
                $this->my_model->setNormalized('emails');
                $emails = $this->my_model->getInfo('tbl_leave_emails');

                $this->my_model->setLastId('holiday_type');
                $day = $this->my_model->getInfo('tbl_day_type',$day_type);
                if(count($emails) > 0) {
                    $this->my_model->setShift();
                    $userInfo = (Object)$this->my_model->getInfo('tbl_staff', $user_id);

                    $name = $userInfo->fname . " " . $userInfo->lname;
                    $subject = "Leave Application from " . $name;
                    $holiday = $this->subbie_date_helper->holidays;
                    $leave_duration = $this->getLeaveDaysCount($leave_start, $leave_end, $holiday);
                    //$leave_duration = floor((strtotime($leave_end) - strtotime($leave_start)) / (60 * 60 * 24));
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
                        'staff_id' => $user_id,
                        'type' => $debugResult->type,
                        'message' => json_encode($sendMailSetting),
                        'debug' => $debugResult->debug
                    );
                    $this->my_model->insert('tbl_leave_email_log', $post, false);
                }
            }
            //endregion

            redirect('staffLeave');
        }

        $staffDefault = isset($_GET['staff']) ? $_GET['staff'] : '';
        $fDefault = isset($_GET['fId']) ? $_GET['fId'] : '';
        $this->data['staffDefault'] = $staffDefault;
        $this->data['fDefault'] = $fDefault;

        $this->staffLeaveDropDowns(0, $this->data['account_type']);

        $this->load->view('main_view', $this->data);
    }

    function staffLeaveEdit(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $this->data['page_load'] = 'backend/leave/leave_edit';
        $this->data['page_name'] .= ' - Update';
        $whatId = $this->uri->segment(2);
        if(!$whatId){
            redirect('staffLeave');
        }

        if(isset($_POST['submit'])){
            $this->my_model->setShift();
            $current_leave_info = $this->my_model->getInfo('tbl_leave', $whatId);
            $leave_start = date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $_POST['leave_start'])));
            $leave_end = date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $_POST['leave_end'])));
            $day_type = $_POST['leave_range'];

            $post = array(
                'date_requested' => date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $_POST['date_requested']))),
                'leave_start' => $leave_start,
                'leave_end' => $leave_end,
                'leave_range' => $_POST['leave_range'],
                'user_id' => $_POST['user_id'],
                'type' => $_POST['type'],
                'reason_request' => nl2br($_POST['reason_request']),
                'decision' => $_POST['decision'],
                'reason_decision' => nl2br($_POST['reason_decision']),
                'actioned_by' => ''
            );
            if(isset($_POST['decision'])){
                $post['actioned_by'] = $this->session->userdata('user_id');
                $post['date_decision'] = date('Y-m-d H:i:s');

                //region Send Mail
                if(!in_array($this->data['account_type'], array(3))) {
                    $this->my_model->setShift();
                    $userInfo = (Object)$this->my_model->getInfo('tbl_staff', $post['user_id'],'id');

                    $this->my_model->setLastId('holiday_type');
                    $day = $this->my_model->getInfo('tbl_day_type',$day_type);

                    $name = $userInfo->fname . " " . $userInfo->lname;
                    $subject = $name . " Confirmation for Leave Application";
                    $holidays_array = $this->subbie_date_helper->holidays;
                    $leave_duration = $this->getLeaveDaysCount($leave_start, $leave_end, $holidays_array);

                    $this->my_model->setLastId('decision');
                    $decision = $this->my_model->getInfo('tbl_leave_decision',$post['decision']);
                    $msg = "Good day,<br/><br/>";
                    $msg .= "<strong>Your requested for ";
                    $msg .= $day_type != 1 ? "a " . $day ."'s Leave, " : $leave_duration . " days leave, ";
                    $msg .= "from <strong>" . date('l d/m/Y h:i a', strtotime($leave_start)) . "</strong> to <strong>" . date('l d/m/Y h:i a', strtotime($leave_end)) . "</strong> " .
                        "has been <strong>" . $decision . "</strong> by the Admin.<br/><br/>";
                    $msg .= $post['reason_request'] ? "Reason for Decision:<br/>" . $post['reason_request'] : '';

                    $sendMailSetting = array(
                        'subject' => $subject,
                        'to' => $userInfo->email,
                        'debug_type' => 2,
                        'debug' => true
                    );
                    $send_mail = new Send_Email_Controller();
                    $debugResult = $send_mail->sendingEmail($msg, $sendMailSetting);
                    $sendMailSetting['body'] = $msg;
                    $_post = array(
                        'date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'staff_id' => $whatId,
                        'type' => $debugResult->type,
                        'message' => json_encode($sendMailSetting),
                        'debug' => $debugResult->debug
                    );
                    $this->my_model->insert('tbl_leave_email_log', $_post, false);
                }
                //endregion
            }

            //region Audit Log
            $log = array(
                'update_type' => 2,
                'user_id' => $this->session->userdata('user_id'),
                'from' => json_encode($current_leave_info),
                'to' => json_encode($post)
            );

            $this->my_model->insert('tbl_leave_audit_log', $log, false);
            //endregion

            $this->my_model->update('tbl_leave', $post, $whatId, 'id', false);

            if(isset($_GET['dtr']) && $_GET['dtr'] == 1){
                redirect('timeSheetEdit?r_id=' . $whatId);
            }else{
                redirect('staffLeave');
            }
        }

        $this->staffLeaveDropDowns(0, $this->data['account_type']);

        $this->my_model->setJoin(array(
            'table' => array('tbl_staff'),
            'join_field' => array('id'),
            'source_field' => array('tbl_leave.user_id'),
            'type' => 'left'
        ));

        $fields = ArrayWalk($this->my_model->getFields('tbl_leave'), 'tbl_leave.');
        $fields[] = 'tbl_staff.project_id';

        $this->my_model->setSelectFields($fields);
        $this->my_model->setShift();
        $this->data['leave'] = (Object)$this->my_model->getInfo('tbl_leave', $whatId, 'tbl_leave.id');

        $this->load->view('main_view', $this->data);
    }

    function leaveAuditLog(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $this->data['page_load'] = 'backend/leave/leave_audit_log';

        //region Dropdown
        $this->staffLeaveDropDowns(0, $this->data['account_type']);

        $this->my_model->setSelectFields(array(
            'tbl_staff.id',
            'CONCAT(tbl_staff.fname, " ", tbl_staff.lname) as staff_string'
        ));
        $this->my_model->setNormalized('staff_string', 'id');
        $this->db->_protect_identifiers = false;
        $this->my_model->setNormalized('staff_string', 'id');
        $staff = $this->my_model->getInfo('tbl_staff');

        $this->my_model->setSelectFields(array('id', 'initial'));
        $this->my_model->setNormalized('initial', 'initial');
        $log_type = $this->my_model->getInfo('tbl_system_audit_update_type');
        $this->data['log_type'] = array('' => '');
        $this->data['log_type'] += $log_type;
        //endregion

        $whatField = array();
        $whatVal = array();

        $isPrint = isset($_GET['isPrint']) ? $_GET['isPrint'] : 0;
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $dateStart = isset($_GET['dateStart']) ? $_GET['dateStart'] : '';
        $dateEnd = isset($_GET['dateEnd']) ? $_GET['dateEnd'] : '';
        $name = isset($_GET['name']) ? $_GET['name'] : '';
        $changesSearch = isset($_GET['changes']) ? $_GET['changes'] : '';
        $changesAny = isset($_GET['any']) ? $_GET['any'] : 0;

        if($name){
            $whatField[] = 'CONCAT(tbl_staff.fname, " ", tbl_staff.lname) LIKE';
            $whatVal[] = $name;
        }

        $fields = ArrayWalk($this->my_model->getFields('tbl_leave_audit_log'), 'tbl_leave_audit_log.');
        $fields[] = 'CONCAT(tbl_staff.fname, " ", tbl_staff.lname) as name';
        $fields[] = 'tbl_system_audit_update_type.initial as type';

        $this->my_model->setSelectFields($fields);
        $this->my_model->setOrder('tbl_leave_audit_log.date', 'DESC');
        $this->my_model->setJoin(array(
            'table' => array('tbl_staff', 'tbl_system_audit_update_type'),
            'join_field' => array('id', 'id'),
            'source_field' => array('tbl_leave_audit_log.user_id', 'tbl_leave_audit_log.update_type'),
            'type' => 'left'
        ));
        $log = $this->my_model->getInfo('tbl_leave_audit_log', $whatVal, $whatField);
        $l = array();
        if(count($log) > 0){
            $ref = 1;
            foreach($log as $v){
                $from = json_decode($v->from);
                $to = json_decode($v->to);

                $thisData = (Object)array();
                switch($v->update_type){
                    case 1:
                        $changes = $v->audit_text ? $v->audit_text : 'Leave application of <strong>' . $staff[$to->user_id] . '</strong> has been added';
                        $thisData = (Object)array(
                            'id' => $ref,
                            'date' => date('Ymd-Hi', strtotime($v->date)),
                            'date_time' => strtotime($v->date),
                            'name' => $v->name,
                            'type' => strtoupper($v->type),
                            'no_hover' => $v->audit_text ? 0 : 1,
                            'changes' => $changes
                        );

                        break;
                    case 2:
                        $changes = 'Leave application of <strong>' . $staff[$to->user_id] . '</strong> has been updated: <br />';
                        $thisChangesPrint = 'Leave application of <strong>' . $staff[$to->user_id] . '</strong> has been updated: <br />';
                        if(count($to) > 0){
                            $thisChanges = "";
                            foreach($to as $k=>$t){
                                if($t == ""){
                                    continue;
                                }

                                if($from->$k != $t){
                                    $thisField = str_replace("_", " ", strtoupper($k));
                                    $f = array_key_exists($k, $from) ? $from->$k : '';
                                    $fromValue = $f;
                                    $toValue = $t;

                                    switch($k){
                                        case "leave_range":
                                            $thisField = "Range";

                                            if($f){
                                                $fromValue = $this->data['leave_range'][$f];
                                            }
                                            if($t){
                                                $toValue = $this->data['leave_range'][$t];
                                            }
                                            break;
                                        case "date_requested":
                                        case "date_decision":
                                        case "leave_start":
                                        case "leave_end":
                                            if($f){
                                                $fromValue = date('d/m/Y H:i a', strtotime($f));
                                            }
                                            if($t){
                                                $toValue = date('d/m/Y H:i a', strtotime($t));
                                            }
                                            break;
                                        case "user_id":
                                            if($f){
                                                $fromValue = $staff[$f];
                                            }
                                            if($t){
                                                $toValue = $staff[$t];
                                            }
                                            break;
                                        case "type":
                                            if($f){
                                                $fromValue = $this->data['type'][$f];
                                            }
                                            if($t){
                                                $toValue = $this->data['type'][$t];
                                            }
                                            break;
                                        case "decision":
                                            if($f){
                                                $fromValue = $this->data['decision'][$f];
                                            }
                                            if($t){
                                                $toValue = $this->data['decision'][$t];
                                            }
                                            break;
                                    }

                                    $thisChanges .= "<tr style='vertical-align: top;'><td><strong>" . $thisField . "</strong></td><td>" . $fromValue . "</td><td>" . $toValue . "</td>";
                                    $thisChangesPrint .= $thisField . " - [From] " . $fromValue . " [To] " . $toValue . "<br />";
                                }
                            }

                            if($thisChanges){
                                $changes .=
                                    "<table>" .
                                    "<tr class='headerTr' style='text-align: center;'><td></td><td>From</td><td>To</td></tr>" .
                                    $thisChanges .
                                    "</table>";
                            }
                        }

                        $thisData = (Object)array(
                            'id' => $ref,
                            'date' => date('Ymd-Hi', strtotime($v->date)),
                            'date_time' => strtotime($v->date),
                            'name' => $v->name,
                            'type' => strtoupper($v->type),
                            'changes' => $isPrint ? $thisChangesPrint : $changes
                        );

                        break;
                    case 3:
                        $thisData = (Object)array(
                            'id' => $ref,
                            'date' => date('Ymd-Hi', strtotime($v->date)),
                            'date_time' => strtotime($v->date),
                            'name' => $v->name,
                            'type' => strtoupper($v->type),
                            'no_hover' => 1,
                            'changes' => 'Leave application of <strong>' . $staff[$from->user_id] . '</strong> has been deleted.'
                        );

                        break;
                }

                if($isPrint){
                    if($type && $type != $thisData->type){
                        continue;
                    }
                    if($changesSearch){
                        $hasMatch = $changesAny ? false : true;
                        $search = array_filter(array_unique(explode(" ", strtolower($changesSearch))));
                        $t = preg_replace('/<[^<|>]+?>/', '', $thisData->changes);
                        $t = strtolower(htmlentities($t, ENT_QUOTES, "UTF-8"));

                        if(count($search) > 0){
                            foreach($search as $str){
                                if($changesAny){
                                    if(strpos($t, $str) > -1) {
                                        $hasMatch = true;
                                    }
                                }
                                else{
                                    if(strpos($t, $str) === false) {
                                        $hasMatch = false;
                                        break;
                                    }
                                }
                            }
                        }

                        if(!$hasMatch){
                            continue;
                        }
                    }
                    if($dateStart && $dateEnd){
                        if(!($thisData->date_time >= $dateStart && $thisData->date_time <= $dateEnd)){
                            continue;
                        }
                    }
                }
                $l[] = $thisData;

                $ref ++;
            }
        }

        if($isPrint){
            $this->data['log'] = $l;
            $this->load->view('backend/leave/leave_audit_print', $this->data);
        }
        else{
            $this->data['log'] = json_encode($l);
            $this->load->view('main_view', $this->data);
        }
    }

    function leaveEmailOption(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $this->staffLeaveDropDowns(1, $this->data['account_type']);

        $this->my_model->setSelectFields(array('tbl_leave_emails.type', 'tbl_leave_emails.emails'));
        $this->my_model->setNormalized('emails', 'type');
        $emails = $this->my_model->getInfo('tbl_leave_emails');
        $this->data['emails'] = $emails;

        if(isset($_POST['emailOption'])){
            if(count($_POST['emailOption']) > 0){
                foreach($_POST['emailOption'] as $k=>$v){
                    $post = array(
                        'type' => $k,
                        'emails' => $v
                    );
                    if(array_key_exists($k, $emails)) {
                        $whatField = array('type');
                        $whatVal = array($k);
                        $this->my_model->update('tbl_leave_emails', $post, $whatVal, $whatField);
                    }
                    else{
                        $this->my_model->insert('tbl_leave_emails', $post);
                    }
                }
            }

            redirect('staffLeave');
        }

        $this->load->view('backend/leave/leave_emails', $this->data);
    }

    function leaveEmailLog(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $this->data['page_load'] = 'backend/leave/leave_email_log';

        $fields = ArrayWalk($this->my_model->getFields('tbl_leave_email_log', array('date', 'debug')), 'tbl_leave_email_log.');
        $fields[] = 'DATE_FORMAT(tbl_leave_email_log.date, "%Y%m%d %H%i") as date';
        $fields[] = 'IF(tbl_leave_email_log.type = 1, "Success", "Failed") as status';
        $fields[] = 'CONCAT(tbl_staff.fname, " ", tbl_staff.lname) as staff';
        $fields[] = 'tbl_user.name as user';

        $this->my_model->setSelectFields($fields, false);
        $this->my_model->setJoin(array(
            'table' => array('tbl_user','tbl_staff'),
            'join_field' => array('id','id'),
            'source_field' => array('tbl_leave_email_log.user_id','tbl_leave_email_log.staff_id'),
            'type' => 'left'
        ));
        $this->my_model->setOrder('tbl_leave_email_log.date', 'DESC');
        $log = $this->my_model->getInfo('tbl_leave_email_log');
        if(count($log) > 0){
            foreach($log as $v){
                $v->message = json_decode($v->message);
            }
        }
        $this->data['log'] = json_encode($log);

        $this->load->view('main_view', $this->data);
    }

    private function staffLeaveDropDowns($hasEmpty = 0, $fId = array()){
        $this->db->_protect_identifiers = false;
        $this->my_model->setSelectFields(array('CONCAT(fname," ",lname) as name','id','project_id'));
        $staff = $this->my_model->getInfo('tbl_staff', 3, 'status_id');
        $this->data['staff'] = array();
        if(count($staff) > 0){
            foreach($staff as $v){
                if($hasEmpty) {
                    $this->data['staff'][$v->project_id][0][''] = 'Staff';
                }
                $this->data['staff'][$v->project_id][][$v->id] = $v->name;
            }
        }

        $this->my_model->setNormalized('project_name','id');
        $this->my_model->setSelectFields(array('project_name','id'));
        $this->data['project'] = $this->my_model->getInfo('tbl_project_type');
        ksort($this->data['project']);

        $this->my_model->setSelectFields(array('type', 'id'));
        $this->my_model->setNormalized('type', 'id');
        $type = $this->my_model->getInfo('tbl_leave_type');
        $this->data['type'] = $hasEmpty ? array('' => 'Type') : array();
        $this->data['type'] += $type;

        $this->my_model->setSelectFields(array('decision', 'id'));
        $this->my_model->setNormalized('decision', 'id');
        $decision = $this->my_model->getInfo('tbl_leave_decision');
        $this->data['decision'] = $hasEmpty ? array('' => 'Status', 0 => 'Pending') : array();
        $this->data['decision'] += $decision;

        $this->my_model->setNormalized('holiday_type','id');
        $this->my_model->setSelectFields(array('id','holiday_type'));
        $leave_range = $this->my_model->getInfo('tbl_day_type');
        $this->data['leave_range'] = $hasEmpty ? array('' => 'Leave Range') : array();
        $this->data['leave_range'] += $leave_range;
    }
    //endregion

    //region Holiday
    function staffHoliday(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $this->data['page_load'] = 'backend/holiday/holiday_view';

        $this->holidayDropDown(1);

        $page = isset($_GET['p']) ? $_GET['p'] : 1;
        $isPrint = isset($_GET['isPrint']) ? $_GET['isPrint'] : 0;
        $limit = 20;
        $p = (($page -1) * $limit);

        if(isset($_POST['filter'])){
            $this->data['isFilter'] = 1;
            unset($_POST['filter']);
            $this->session->set_userdata(array(
                'holidayFilter' => $_POST
            ));
        }
        if(isset($_POST['clearFilter'])){
            $this->session->set_userdata(array(
                'holidayFilter' => array(
                    'isExpandAll' => $_POST['isExpandAll']
                )
            ));
        }
        $holidayFilter = $this->session->userdata('holidayFilter') ? $this->session->userdata('holidayFilter') : array('isExpandAll' => 1);
        $holidayFilter = (Object)$holidayFilter;
        $this->data['holidayFilter'] = $holidayFilter;

        $whatField = array('');
        $whatVal = array('tbl_holiday.id IS NOT NULL');
        if(@$holidayFilter->year){
            $whatField[] = 'YEAR(tbl_holiday.date) =';
            $whatVal[] = $holidayFilter->year;
        }
        if(@$holidayFilter->type){
            $whatField[] = 'tbl_holiday.type';
            $whatVal[] = $holidayFilter->type;
        }
        if(@$holidayFilter->holiday){
            $whatField[] = 'tbl_holiday.holiday LIKE';
            $whatVal[] = "%" . $holidayFilter->holiday . "%";
        }
        if(@$holidayFilter->franchise){
            if(!in_array("all", $holidayFilter->franchise)){
                $f = $holidayFilter->franchise;
                $f[] = "all";
                $c = $this->holidayFilterParser('tbl_holiday.franchise_id', $f);
                $whatField[] = '';
                $whatVal[] = $c;
            }
        }

        $field = ArrayWalk($this->my_model->getFields('tbl_holiday', array('type')), 'tbl_holiday.');
        $field[] = 'tbl_holiday_type.type';
        $this->my_model->setSelectFields($field);
        $this->my_model->setOrder('tbl_holiday.date', 'ASC');
        $this->my_model->setJoin(array(
            'table' => array('tbl_holiday_type'),
            'join_field' => array('id'),
            'source_field' => array('tbl_holiday.type')
        ));
        $config = $this->my_model->model_config;
        $h = $this->my_model->getInfo('tbl_holiday', $whatVal, $whatField);
        $h_count = count($h);
        $total_pages = ceil($h_count/$limit);

        $this->my_model->model_config = $config;
        $hasLimit = $isPrint && !isset($_GET['p']) ? 0 : 1;
        if($hasLimit){
            $this->my_model->setConfig($limit, $p,true);
        }
        $holidays = $this->my_model->getInfo('tbl_holiday', $whatVal, $whatField);

        if(count($holidays) > 0){
            foreach($holidays as $v){
                if($v->type == 1){
                    $v->date = date('Y') . "-" . date('m-d', strtotime($v->date));
                }
            }
        }

        $this->my_model->setSelectFields('holiday');
        $this->my_model->setGroupBy('holiday');
        $this->my_model->setNormalized('holiday');
        $title_json = $this->my_model->getInfo('tbl_holiday');
        if(count($title_json) > 0){
            foreach($title_json as $k=>$v){
                $title_json[$k] = html_entity_decode($v);
            }
        }

        $this->data['holidays'] = $holidays;
        $this->data['title_json'] = str_replace("&#039;", "'", json_encode($title_json));
        $this->data['total_pages'] = $total_pages;
        $this->data['page'] = $page;

        if($isPrint){
            $this->load->view('backend/holiday/holiday_view_print', $this->data);
        }
        else{
            $this->load->view('main_view', $this->data);
        }
    }

    private function holidayFilterParser($field, $array){
        $condition = array();
        if(count($array) > 0){
            foreach($array as $v){
                $condition[] = $field . ' LIKE \'%"' . $v . '"%\'';
            }
        }
        $c = count($condition) > 0 ? '(' . implode(" OR ", $condition) . ')' : '';
        return $c;
    }
    private function holidayArrayDataGet($needle, $search, $glue = "<br />"){
        $string = array();
        if(count($needle) > 0){
            foreach($needle as $v){
                if(array_key_exists($v, $search)){
                    $string[] = $search[$v];
                }
            }
        }
        $s = count($string) > 0 ? implode($glue, $string) : '';
        return $s;
    }

    function staffHolidayAdd(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        if(isset($_POST['holiday'])){
            $post = array(
                'date' => date('Y-m-d', strtotime(str_replace("/", "-", $_POST['date']))),
                'holiday' => $_POST['holiday'],
                'description' => nl2br($_POST['description']),
                'type' => $_POST['type'],
                'franchise_id' => json_encode(isset($_POST['franchise_id']) ? $_POST['franchise_id'] : array()),
                'merchant_id' => json_encode(isset($_POST['merchant_id']) ? $_POST['merchant_id'] : array()),
                'branch_id' => json_encode(isset($_POST['branch_id']) ? $_POST['branch_id'] : array())
            );
            if($_POST['date_to']){
                $post['date_to'] = date('Y-m-d', strtotime(str_replace("/", "-", $_POST['date_to'])));
            }
            $this->my_model->insert('tbl_holiday', $post, false);

            redirect('staffHoliday');
        }

        $this->holidayDropDown();

        $this->load->view('backend/holiday/holiday_add', $this->data);
    }

    function staffHolidayCopy(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

        if(isset($_POST['holiday_id'])){
            if(count($_POST['holiday_id']) > 0){
                $field = $this->my_model->getFields('tbl_holiday', array('id'));
                $this->my_model->setSelectFields($field);
                $h = $this->my_model->getInfo('tbl_holiday', $_POST['holiday_id'], 'id');
                if(count($h) > 0){
                    foreach($h as $v){
                        $post = (Array)$v;
                        $post['date'] = $year . '-' . date('m-d', strtotime($post['date']));
                        $itExist = count($this->my_model->getInfo('tbl_holiday', $post['date'], 'date')) > 0;
                        if(!$itExist){
                            $this->my_model->insert('tbl_holiday', $post, false);
                        }
                    }
                }
            }

            redirect('staffHoliday');
        }

        if($id){
            $whatField = array('id');
            $whatVal = array($id);
            $this->my_model->setOrder('tbl_holiday.date', 'ASC');
            $this->my_model->setShift();
            $holiday_copy = (Object)$this->my_model->getInfo('tbl_holiday', $whatVal, $whatField);
        }
        else{
            $holidayFilter =  (Object)$this->session->userdata('holidayFilter');

            $whatField = array('YEAR(date) !=', '');
            $whatVal = array(
                $year,
                '(SELECT COUNT(h.id) FROM tbl_holiday h WHERE h.date = tbl_holiday.date) = 1'
            );
            if(@$holidayFilter->year){
                $whatField[] = 'YEAR(tbl_holiday.date) =';
                $whatVal[] = $holidayFilter->year;
            }
            if(@$holidayFilter->type){
                $whatField[] = 'tbl_holiday.type';
                $whatVal[] = $holidayFilter->type;
            }
            if(@$holidayFilter->holiday){
                $whatField[] = 'tbl_holiday.holiday LIKE';
                $whatVal[] = "%" . $holidayFilter->holiday . "%";
            }
            $this->my_model->setSelectFields(array('id', 'holiday', 'date', 'date_to'));
            $this->my_model->setOrder('tbl_holiday.date', 'ASC');
            $holiday_copy = $this->my_model->getInfo('tbl_holiday', $whatVal, $whatField);
        }
        $this->data['holiday_copy'] = $holiday_copy;
        $this->data['year'] = $year;

        if($id){
            $this->holidayDropDown();
            $this->load->view('backend/holiday/holiday_copy', $this->data);
        }
        else{
            $this->load->view('backend/holiday/holiday_copy_all', $this->data);
        }
    }

    function staffHolidayEdit(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $whatId = $this->uri->segment(2);
        if(!$whatId){
            redirect('staffLeave');
        }

        if(isset($_POST['holiday'])){
            $post = array(
                'date' => date('Y-m-d', strtotime(str_replace("/", "-", $_POST['date']))),
                'holiday' => $_POST['holiday'],
                'description' => nl2br($_POST['description']),
                'type' => $_POST['type'],
                'franchise_id' => json_encode(isset($_POST['franchise_id']) ? $_POST['franchise_id'] : array()),
                'merchant_id' => json_encode(isset($_POST['merchant_id']) ? $_POST['merchant_id'] : array()),
                'branch_id' => json_encode(isset($_POST['branch_id']) ? $_POST['branch_id'] : array())
            );
            $this->my_model->update('tbl_holiday', $post, $whatId, 'id', false);
            $this->my_model->mysqlString(
                'UPDATE tbl_holiday SET date_to = ' .
                ($_POST['date_to'] ? '"' . date('Y-m-d H:i:s', strtotime(str_replace("/", "-", $_POST['date_to']))) . '"' : 'NULL') .
                ' WHERE id = ' . $whatId, true
            );

            redirect('staffHoliday');
        }

        $this->holidayDropDown();

        $this->my_model->setShift();
        $this->data['holiday'] = (Object)$this->my_model->getInfo('tbl_holiday', $whatId);

        $this->load->view('backend/holiday/holiday_edit', $this->data);
    }

    private function holidayDropDown($hasEmpty = 0){
        $this->my_model->setSelectFields(array('type', 'id'));
        $this->my_model->setNormalized('type', 'id');
        $type = $this->my_model->getInfo('tbl_holiday_type');
        $this->data['type'] = $hasEmpty ? array('' => 'Type') : array();
        $this->data['type'] += $type;


        $year = array();
        for($i = date('Y', strtotime('+5 years')); $i >= 2012; $i --){
            $year[$i] = $i;
        }
        $this->data['year'] = $hasEmpty ? array('' => 'Year') : array();
        $this->data['year'] += $year;

        $yearAdvance = array();
        for($i = date('Y', strtotime('+5 year')); $i >= 2012; $i --){
            $yearAdvance[$i] = $i;
        }
        $this->data['yearAdvance'] = $hasEmpty ? array('' => 'Year') : array();
        $this->data['yearAdvance'] += $yearAdvance;
    }

    function staffHolidayDelete(){
        if($this->session->userdata('is_logged_in') != true){
            redirect('');
        }

        $whatId = isset($_POST['id']) ? $_POST['id'] : '';
        $r = 0;
        if($whatId){
            $this->my_model->delete('tbl_holiday', $whatId);
            $r = 1;
        }

        echo $r;
    }
//endregion

}