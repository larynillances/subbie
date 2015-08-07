<style>
    .print-table{
        margin: 0 auto;
        border-collapse: collapse;
        width: 100%;
        font-size: 13px;
    }
    .print-table > thead > tr > th{
        text-align: center;
    }
    .print-table > tbody > tr > td{
        padding: 5px 15px;
        border: 1px solid #000000;
    }
    .bold-text{
        font-weight: bold;
    }
    .deduction-table{
        border-collapse: collapse;
        width: 100%;
        font-size: 13px;
    }
    .deduction-table tr td:last-child{
        text-align: right;
    }
    .inner-table{
        border-collapse: collapse;
        width: 100%;
        font-size: 12px!important;
    }
    .inner-table > thead > tr > th{
        border-bottom: 1px solid #000000!important;
        text-align: center;
    }
    .inner-table > thead > tr > th:nth-child(1),
    .inner-table > tbody > tr > td:nth-child(1){
        padding: 3px;
        border-right: 1px solid #000000;
    }
    .inner-table > tbody > tr > td{
        padding: 3px;
        text-align: right!important;
        vertical-align: top;
        height: 50px;
    }
    .warning-msg{
        padding: 5px 15px!important;
    }
    .row div{
        padding: 2px;
    }
</style>
<div id="content" style="width: 850px;margin: 0 auto;">
    <div class="row">
        <div class="col-sm-2">
            <button type="button" class="btn btn-sm btn-success send-payslip" <?php echo $has_email != 1 ? 'disabled' : '';?>><i class="glyphicon glyphicon-send"></i> Send</button>
        </div>
        <div class="col-sm-9">
            <?php
            $_warning_no_email = '<div class="alert alert-danger warning-msg" role="alert"><strong>Warning!</strong>This person has no email address.</div>';
            $_warning_manual_send = '<div class="alert alert-danger warning-msg" role="alert"><strong>Warning!</strong>This person has an email address but prefer to send manually.</div>';
            echo !$has_email ? $_warning_no_email : ($has_email == 2 ? $_warning_manual_send : '');
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <iframe src="<?php echo base_url('printPaySlip/'.$this->uri->segment(2).'/'.$this->uri->segment(3))?>" style="width: 100%;height: 1000px;"></iframe>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.send-payslip').click(function(){
            $(this).modifiedModal({
                url: bu + 'sendStaffPaySlip<?php echo '/'.$this->uri->segment(2).'/'.$this->uri->segment(3)?>',
                title: 'Send Pay Slip',
                type: 'small'
            });
        });
        jQuery(window).load(function () {
            $(this).newForm.removeLoadingForm();
        });
    });
</script>