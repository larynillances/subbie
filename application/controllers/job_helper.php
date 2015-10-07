<?php

class Job_Helper extends CI_Controller{

    function job_details($whatVal, $whatFld){
        $this->my_model->setJoin(array(
            'table' => array(
                'tbl_client',
                'tbl_quotation',
                'tbl_invoice',
                'tbl_job_allocated',
                'tbl_tracking_log',
                'tbl_job_status'
            ),
            'join_field' => array(
                'id','job_id','job_id','job_id','job_id','id'
            ),
            'source_field' => array(
                'tbl_registration.client_id',
                'tbl_registration.id',
                'tbl_registration.id',
                'tbl_registration.id',
                'tbl_registration.id',
                'tbl_tracking_log.status_id'
            ),
            'type' => 'left'
        ));
        $fields = ArrayWalk(array('id','client_id','address'),'tbl_registration.');
        $fields[] = 'IF(tbl_registration.start_date != "0000-00-00",
                        DATE_FORMAT(tbl_registration.start_date,"%d/%m/%y"),
                         "") as start_date';
        $fields[] = 'IF(tbl_registration.tender_date != "0000-00-00",
                        DATE_FORMAT(tbl_registration.tender_date,"%d/%m/%y"),
                         "") as tender_date';
        $fields[] ='IF(tbl_registration.date_added != "0000-00-00",
                        DATE_FORMAT(tbl_registration.date_added,"%d/%m/%y"),
                         "") as date_added';
        $fields[] ='IF(tbl_tracking_log.completed_date != "0000-00-00",
                        DATE_FORMAT(tbl_tracking_log.completed_date,"%d/%m/%y"),
                         "") as completed_date';
        $fields[] = 'CONCAT(tbl_client.client_code,LPAD(tbl_registration.id, 5,"0")) as job_ref';
        $fields[] = 'tbl_job_status.code as job_code';
        $fields[] = 'tbl_job_status.status';
        $fields[] = 'tbl_client.client_code';
        $fields[] = 'IF(tbl_quotation.accepted_date, DATE_FORMAT(tbl_quotation.accepted_date,"%d/%m/%y"),"") as accepted_date';
        $fields[] = 'tbl_registration.is_invoice';
        $fields[] = 'tbl_job_allocated.duration';
        $fields[] = 'tbl_registration.job_name';
        $fields[] = 'tbl_tracking_log.meter';
        $fields[] = 'tbl_tracking_log.job_rating';
        $fields[] = 'tbl_tracking_log.team';
        $fields[] = 'tbl_tracking_log.status_id';
        $fields[] = 'tbl_tracking_log.notes';

        $this->my_model->setSelectFields($fields,false);
        $this->my_model->setGroupBy('id');
        $job_data = $this->my_model->getinfo('tbl_registration',$whatVal,$whatFld);

        return $job_data;
    }
}