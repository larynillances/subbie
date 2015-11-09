<?php
$header_links = array();
switch($this->session->userdata('account_type')){
    case 1:
        $header_links = array(
            'trackingLog' => 'Tracking',
            'Admin' => array(
                /*'jobQuoting' => 'Job Quoting',*/
                'taxTable' => 'Tax Table',
                'workFlowCalendar' => 'Work Flow Calendar',
                'emailLog' => 'Email Log',
                'paySetup' => 'Pay Setup',
                'supplierList' => 'Supplier List',
                'clientList' => 'Client List',
                'invoiceList' => 'Invoice List',
                'payIntegrityCheck' => 'Pay Integrity Check',
                'staffHoliday' => 'Holiday',
            ),
            'New' => array(
                'newJobRequestForm' => 'Job Request',
                'quotation' => 'Quote',
                'orderBookInput' => 'Order',
                'invoiceCreate' => 'Invoice Entry',
                'workOrder' => 'Work Order'
            ),
            /*'List' => array(
                'invoiceList' => 'Invoice List',
                'quoteList' => 'Quote List',
                'orderSentList' => 'Order Sent List',
                'jobList' => 'Job List'
            ),*/
            'timeSheetDefault' => 'DTR',
            'Manage' => array(
                'textTemplate' => 'Template'
            ),
            'Wage' => array(
                'wageManage' => 'Staff Details',
                'staffList' => 'Staff List',
                'staffLeave' => 'Staff Leave',
                'payRatePeriods' => 'Pay Rate Periods',
                'payPeriodSettings' => 'Pay Period Settings',
                'payPeriodSummaryReport' => 'Pay Period Summary Report',
                'wageTable' => 'Monthly Wage Summary',
                'monthlyTotalPay' => 'Monthly Pay',
                'employerMonthlySched' => 'Employer Monthly Schedule',
                'yearToDateReport' => 'YTD Report',
                'employeeFinalPay' => 'Final Pay'
            ),
            'outstandingBalance' => 'Outstanding',
            'PDF Archive' => array(
                'archiveQuote' => 'Quote',
                'archiveInvoice' => 'Invoice',
                'archiveCreditNote' => 'Credit Note',
                'archiveStatement' => 'Statement',
                'pdfSummaryArchive' => 'Summary'
            )
        );
        break;
    case 2:
        $header_links = array(
            'trackingLog' => 'Tracking',
            'Admin' => array(
                'clientList' => 'Client List',
                'workFlowCalendar' => 'Work Flow Calendar'
            ),
            'New' => array(
                'newJobRequestForm' => 'Job Request',
                'quotation' => 'Quote',
                'orderBookInput' => 'Order',
            ),
           /* 'List' => array(
                'quoteList' => 'Quote List',
                'clientList' => 'Client List',
                'orderSentList' => 'Order Sent List'
            ),*/
            'timeSheetDefault' => 'DTR'
        );
        break;
    case 3:
        $header_links = array(
            'trackingLog' => 'Tracking',
            'Admin' => array(
                'clientList' => 'Client List',
                'workFlowCalendar' => 'Work Flow Calendar'
            ),
            'New' => array(
                'invoiceCreate' => 'Invoice Draft',
                'newJobRequestForm' => 'Job Request',
                'quotation' => 'Quote',
                'orderBookInput' => 'Order',
            ),
            /*'List' => array(
                'quoteList' => 'Quote List',
                'clientList' => 'Client List',
                'orderSentList' => 'Order Sent List'
            ),*/
            'timeSheetDefault' => 'DTR'
        );
        break;
    case 4:
        $header_links = array(
            'trackingLog' => 'Tracking',
            'Admin' => array(
                'userList' => 'User List',
                'taxTable' => 'Tax Table',
                'paySetup' => 'Pay Setup',
                'clientList' => 'Client List',
                'supplierList' => 'Supplier List',
                'downloadForm' => 'Download Forms',
                'payIntegrityCheck' => 'Pay Integrity Check'
            ),
            'New' => array(
                'newJobRequestForm' => 'Job Request',
                'quotation' => 'Quote',
                'orderBookInput' => 'Order',
                'invoiceCreate' => 'Invoice Entry',
            ),
            /*'List' => array(
                'invoiceList' => 'Invoice List',
                'quoteList' => 'Quote List',
                'clientList' => 'Client List',
                'orderSentList' => 'Order Sent List',
                'jobList' => 'Job List',
                'supplierList' => 'Supplier List'
            ),*/
            'timeSheetDefault' => 'DTR',
            'Manage' => array(
                'textTemplate' => 'Template'
            ),
            'Wage' => array(
                'wageManage' => 'Staff Details'
            )
        );
        break;
    default:
        break;
}

if(count($form_links) > 0){
    $title = count($form_links) > 1 ? 'Download Forms' : 'Download Form';
    $header_links[$title] = $form_links;
}

?>
<ul class="nav navbar-nav navbar-right">
    <?php
    if(count($header_links)>0){
        foreach($header_links as $url=>$title){
            $this_url = explode('/',$url);
            $active = $this->uri->segment(1) == $this_url[0] ? 'active' : '';
            if(is_array($title)){
                subLink($url, $title, $this->uri->segment(1) ? $this->uri->segment(1) : '');
            }else{
                ?>
                <li class="<?php echo $active;?>">
                    <a href="<?php echo base_url() . $url;?>" class="<?php echo $url.'Btn'?>"><?php echo $title;?></a>
                </li>
            <?php
            }
        }
    }
    $username = explode(' ',$user->name);
    if($account_type != 3):
    ?>
        <li class="dropdown">
            <a class="dropdown-toggle msg-btn" data-toggle="dropdown" aria-labelledby="dLabel" href="#">
                <?php echo $count_msg > 0 ? '<span class="badge">'.$count_msg.'</span>' : ''?>
                <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-messages pull-right" style="font-size: 12px;width: 350px;">
                <li style="background: #cfcfcf;padding: 5px;">
                    <table style="width: 100%">
                        <tr>
                            <td><span class="msg-content"><?php echo 'New Message: <strong class="count-msg">' . $count_msg .'</strong>';?></span></td>
                            <td><?php echo form_dropdown('filter_msg',$notification_dp,2,'class="form-control input-sm filter_msg"');?></td>
                        </tr>
                    </table>
                </li>
                <li style="overflow: auto;max-height: 300px;padding: 5px;" class="notification-class"></li>
                <li class="read-all-message" style="display: none;">
                    <a class="text-left read-all-msg" href="<?php echo base_url('updateNotification?read_all=1')?>">
                        <strong>Read All Messages</strong>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            </ul>
            <!-- /.dropdown-messages -->
        </li>
    <?php
    endif;
    ?>
    <li class="dropdown pull-right">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
            <i class="fa fa-user fa-fw"></i> <?php echo $username[0];?> <i class="fa fa-caret-down"></i>
        </a>
        <ul class="dropdown-menu dropdown-user">
            <li>
                <a href="<?php echo base_url() .'logout';?>" class="logout-btn"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
            </li>
        </ul>
        <!-- /.dropdown-user -->
    </li>
</ul>

<?php
function subLink($url, $title, $uri,$level = 1){
    $active = array_value_recursive($uri, $title);
    $multi_level = $level != 1 ? 'dropdown-submenu' : '';
    $active_class = $active ? 'active' : '';
    //$active = array_key_exists($uri, $title);
    ?>
    <li <?php echo 'class="'.$multi_level.' '.$active_class.'"';?>>
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <?php
            echo " " . $url;
            echo $level == 1 ? ' <b class="caret"></b>' : '';
            ?>
        </a>
        <ul class="dropdown-menu multi-level">
            <?php
            foreach($title as $subUrl=>$subTitle){
                if(is_array($subTitle)){
                    subLink($subUrl, $subTitle, $uri ? $uri : '',2);
                }else{
                    ?>
                    <li class="<?php echo $subUrl == $uri ? 'active' : '';?>">
                        <a href="<?php echo $subUrl && !is_numeric($subUrl) ? base_url() . $subUrl : '#';?>" class="<?php echo $subUrl.'Btn'?> link-btn-class" id="<?php echo $subUrl?>"><?php echo $subTitle;?></a>
                    </li>
                <?php
                }
            }
            ?>
        </ul>
    </li>
<?php
}

function array_value_recursive($key, array $arr){
    $val = array();
    array_walk_recursive($arr, function($v, $k) use($key, &$val){
        if($k == $key) array_push($val, $v);
    });
    return count($val) > 1 ? $val : array_pop($val);
}
?>