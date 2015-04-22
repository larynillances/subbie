<?php
$header_links = array();
switch($this->session->userdata('account_type')){
    case 1:
        $header_links = array(
            'Log' => array(
                'trackingLog' => 'Tracking',
            ),
            'Admin' => array(
                'jobQuoting' => 'Job Quoting',
                'newJobRequestForm' => 'New Job Request',
                'quotation' => 'New Quote',
                'orderBookInput' => 'New Order',
                'invoiceCreate' => 'New Invoice Entry',
                'taxTable' => 'Tax Table',
                'workFlowCalendar' => 'Work Flow Calendar'
            ),
            'List' => array(
                'invoiceList' => 'Invoice List',
                'quoteList' => 'Quote List',
                'clientList' => 'Client List',
                'orderSentList' => 'Order Sent List',
                'jobList' => 'Job List',
                'supplierList' => 'Supplier List'
            ),
            'timeSheetEdit' => 'DTR',
            /*'Invoice' => array(
                'invoiceList' => 'List',
                'createNewInvoice' => 'Create New Entry'
            ),*/
            'Manage' => array(
                'textTemplate' => 'Template'
            ),
            'Wage' => array(
                'wageManage' => 'Wage Management',
                'monthlyTotalPay' => 'Monthly Pay',
                'wageTable' => 'Wage Summary',
                'employerMonthlySched' => 'Employer Monthly Schedule',
                'staffList' => 'Staff List'
            ),
            'outstandingBalance' => 'Outstanding',
            'PDF Archive' => array(
                'archiveQuote' => 'Quote',
                'archiveInvoice' => 'Invoice',
                'archiveStatement' => 'Statement',
                'pdfSummaryArchive' => 'Summary'
            ),
            'logout' => 'Logout'
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
            'timeSheet' => 'DTR',
            'logout' => 'Logout'
        );
        break;
    case 3:
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
            'timeSheet' => 'DTR',
            'logout' => 'Logout'
        );
        break;
    default:
        break;
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
    ?>
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