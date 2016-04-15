<?php

class Send_Email_Controller extends CI_Controller{

    function sendingEmail($message, $options = array()){
        //NOTE:
        /*
        If using XAMPP as your localhost things to do:
            In php.ini file
            1. smtp = 'ssl://stmp.googlemail.com'
            2. smtp_port= '465'
            3. enable/add extension=php_openssl.dll (the most import that thing to send email via localhost)
        */

        $default = array(
            'to' => 'dummymailthedraftingzone@gmail.com',
            'from' => 'no-reply@subbiesolutions.co.nz',
            'name' => 'Subbie Solutions Administrator',
            'subject' => 'Notification from Subbie Solutions',
            'to_alias' => '',
            'cc' => '',
            'cc_alias' => '',
            'bcc' => '',
            'bcc_alias' => '',
            'url' => '',
            'disposition' => 'attachment',
            'file_names' => NULL,
            'debug_type' => 0,
            'debug_return' => 0,
            'debug' => false
        );

        $option = count($options) > 0 ? array_merge($default, $options) : $default;
        $option = (Object)$option;

        $this->load->library('email');

        //region compatible sitehost only if not sending to own DOMAIN Emails
        /*//
        $config['protocol'] = 'sendmail';
        $config['mailpath'] = '/usr/sbin/sendmail';
        $config['charset'] = 'iso-8859-1';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'html';
        //*/
        //endregion

        //region localhost SMTP
        /*//
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
        $config['smtp_port'] = 465;
        $config['mailtype'] = 'html';
        $config['smtp_user'] = 'dummymailthedraftingzone@gmail.com';
        $config['smtp_pass'] = 'dummyp@ssw0rd';
        //*/
        //endregion

        //region sitehost SMTP
       /* $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'ssl://mx1.sitehost.co.nz';
        $config['smtp_port'] = 465;
        $config['mailtype'] = 'html';
        $config['smtp_user'] = 'donotreply@theestimator.co.nz';
        $config['smtp_pass'] = 'apple1';*/
        //endregion

        //region EstimIT Host
        $config['protocol'] = 'smtp';
        $config['smtp_host'] = 'mail.estimit.net';
        $config['smtp_crypto'] = 'tls';
        $config['smtp_port'] = 587;
        $config['mailtype'] = 'html';
        $config['smtp_user'] = 'no-reply@subbiesolutions.co.nz';
        $config['smtp_pass'] = 'M4?xdOg|64f76ZP6';
        //endregion

        $this->email->set_newline("\r\n");
        $this->email->initialize($config);

        $this->email->clear(TRUE);

        $this->email->from($option->from, $option->name);
        $this->email->_to_alias_array = $option->to_alias;
        $this->email->to($option->to);
        $this->email->_cc_alias_array = $option->cc_alias;
        $this->email->cc($option->cc);
        $this->email->_bcc_alias_array = $option->bcc_alias;
        $this->email->bcc($option->bcc);

        $this->email->subject($option->subject);
        $this->email->message($message);

        if($option->url){
            $this->sendEmailAddAttachment($option->url, $option->disposition, $option->file_names);
        }

        $isSuccess = 0;
        if($this->email->send()){
            $isSuccess = 1;
        }

        if($option->debug){
            $resultString = "";
            switch($option->debug_type){
                case 2:
                    $resultString = (Object)array(
                        'type' => $isSuccess,
                        'debug' => $this->email->print_debugger()
                    );
                    break;
                case 1:
                    $resultString = $this->email->print_debugger();
                    break;
                default:
                    $resultString = $isSuccess;
            }

            switch($option->debug_return){
                case 1:
                    echo $resultString;
                    break;
                default:
                    return $resultString;
            }
        }
    }

    function sendEmailAddAttachment($url, $disposition = "", $file_names = NULL){
        if(is_array($url) || is_object($url)){
            foreach($url as $key=>$file){
                $this->sendEmailAddAttachment(
                    $file,
                    array_key_exists($key, $disposition) ? $disposition[$key] : "attachment",
                    (is_array($url) || is_object($url)) ? (array_key_exists($key, $file_names) ? $file_names[$key] : NULL) : $file_names
                );
            }
        }

        if(is_dir($url)){
            if ($prints = opendir($url)) {
                while (false !== ($file = readdir($prints))) {
                    if($file !== "." && $file !== ".."){
                        $this->email->attach(
                            $url . $file,
                            is_string($disposition) ? $disposition : 'attachment',
                            is_string($file_names) ? $file_names : NULL
                        );
                    }
                }
            }
        }
        if(is_file($url)){
            $this->email->attach(
                $url,
                is_string($disposition) ? $disposition : 'attachment',
                is_string($file_names) ? $file_names : NULL
            );
        }
    }
}