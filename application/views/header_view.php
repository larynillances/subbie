<?php
$header_links = array();
switch($this->session->userdata('account_type')){
    case 1:
        $header_links = array(
            'trackingLog' => 'Tracking Log',
            'Admin' => array(
                'jobQuoting' => 'Job Quoting',
                'newJobRequestForm' => 'New Job Request',
                'quotation' => 'New Quote',
                'orderBookInput' => 'New Order',
                'invoiceCreate' => 'New Invoice Entry',
                'taxTable' => 'Tax Table',
                'workFlowCalendar' => 'Work Flow Calendar',
                'emailLog' => 'Email Log',
                'paySetup' => 'Pay Setup'
            ),
            'List' => array(
                'invoiceList' => 'Invoice List',
                'quoteList' => 'Quote List',
                'clientList' => 'Client List',
                'orderSentList' => 'Order Sent List',
                'jobList' => 'Job List',
                'supplierList' => 'Supplier List'
            ),
            'timeSheetDefault' => 'DTR',
            /*'Invoice' => array(
                'invoiceList' => 'List',
                'createNewInvoice' => 'Create New Entry'
            ),*/
            'Manage' => array(
                'textTemplate' => 'Template'
            ),
            'Wage' => array(
                'wageManage' => 'Staff Details',
                'staffList' => 'Staff List',
                'payRatePeriods' => 'Pay Rate Periods',
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
            'trackingLog' => 'Tracking Log',
            'Admin' => array(
                'newJobRequestForm' => 'New Job Request',
                'quotation' => 'New Quote',
                'orderBookInput' => 'New Order',
                'workFlowCalendar' => 'Work Flow Calendar'
            ),
            'List' => array(
                'quoteList' => 'Quote List',
                'clientList' => 'Client List',
                'orderSentList' => 'Order Sent List'
            ),
            'timeSheetDefault' => 'DTR'
        );
        break;
    case 3:
        $header_links = array(
            'trackingLog' => 'Tracking Log',
            'Admin' => array(
                'invoiceCreate' => 'New Invoice Draft',
                'newJobRequestForm' => 'New Job Request',
                'quotation' => 'New Quote',
                'orderBookInput' => 'New Order',
                'workFlowCalendar' => 'Work Flow Calendar'
            ),
            'List' => array(
                'quoteList' => 'Quote List',
                'clientList' => 'Client List',
                'orderSentList' => 'Order Sent List'
            ),
            'timeSheetDefault' => 'DTR'
        );
        break;
    case 4:
        $header_links = array(
            'trackingLog' => 'Tracking Log',
            'Admin' => array(
                'newJobRequestForm' => 'New Job Request',
                'userList' => 'User List',
                'quotation' => 'New Quote',
                'orderBookInput' => 'New Order',
                'invoiceCreate' => 'New Invoice Entry',
                'taxTable' => 'Tax Table',
                'paySetup' => 'Pay Setup',
                'downloadForm' => 'Download Forms'
            ),
            'List' => array(
                'invoiceList' => 'Invoice List',
                'quoteList' => 'Quote List',
                'clientList' => 'Client List',
                'orderSentList' => 'Order Sent List',
                'jobList' => 'Job List',
                'supplierList' => 'Supplier List'
            ),
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
            if(is_array($title)){
                subLink($url, $title, $this->uri->segment(1) ? $this->uri->segment(1) : '');
            }else{
                ?>
                <li class="<?php echo $this->uri->segment(1) == $url ? 'active' : '';?>">
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
                            <td><?php echo form_dropdown('filter_msg',$notification_dp,'','class="form-control input-sm filter_msg"');?></td>
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
                <a href="<?php echo base_url() .'logout';?>"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
            </li>
        </ul>
        <!-- /.dropdown-user -->
    </li>
</ul>

<?php
function subLink($url, $title, $uri){
    $active = array_key_exists($uri, $title);
    ?>
    <li class="dropdown">
        <a href="#" class="dropdown-toggle <?php echo $active ? ' active' : '';?>" data-toggle="dropdown">
            <?php echo " " . $url;?>
            <b class="caret"></b>
        </a>
        <ul class="dropdown-menu">
            <?php
            foreach($title as $subUrl=>$subTitle){
                if(is_array($subTitle)){
                    subLink($subUrl, $subTitle, $uri ? $uri : '');
                }else{
                    ?>
                    <li>
                        <a href="<?php echo $subUrl && !is_numeric($subUrl) ? base_url() . $subUrl : '#';?>" class="<?php echo $subUrl.'Btn'?>"><?php echo $subTitle;?></a>
                    </li>
                <?php
                }
            }
            ?>
        </ul>
    </li>
<?php
}