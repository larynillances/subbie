<?php

class Notification_Controller extends CI_Controller{


    public function submitNotification($client_id,$receiver,$title = '',$notification='',$type = 'update'){
        $post = array(
            'date' => date('Y-m-d H:i:s'),
            'client_id' => $client_id,
            'author_id' => $this->session->userdata('user_id'),
            'receiver_id' => $receiver,
            'title' => $title,
            'notification' => $notification,
            'type' => $type,
            'is_new' => 1
        );

        $id = $this->my_model->insert('tbl_notification',$post,false);

        if($id){
            return true;
        }else{
            return false;
        }
    }
}