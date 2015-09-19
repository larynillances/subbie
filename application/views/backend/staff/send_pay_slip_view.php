<?php
$id = $this->uri->segment(2);
$date = $this->uri->segment(3);
$week = $this->uri->segment(4);
echo form_open('sendStaffPaySlip/'.$id.'/'.$date.'/'.$week.'?type='.$_GET['type'],'class="form-horizontal"');
?>
<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-2" for="name">To:</label>
        <div class="col-sm-10">
            <input type="text" name="name" class="form-control input-sm" id="name" value="<?php echo @$details['name'];?>" placeholder="Email">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-2" for="email">Email:</label>
        <div class="col-sm-10">
            <input type="email" name="email" class="form-control input-sm" id="email" value="<?php echo @$details['email'];?>" placeholder="Email">
        </div>
    </div>
</div>
<div class="modal-footer">
    <button class="btn btn-sm btn-success send-email" type="submit" name="send_email">Send</button>
    <button class="btn btn-sm btn-default" data-dismiss="modal">Cancel</button>
</div>
<?php echo form_close();?>
<script>
    $(function(e){
        var email = $('#email');
        var sendEmail = $('.send-email');

        var checkEmailValue = function(value){
            sendEmail.removeAttr('disabled');
            if(!value){
                sendEmail.attr('disabled','disabled');
            }
        };

        checkEmailValue(email.val());

        email.keyup(function(){
            checkEmailValue($(this).val());
        });
    });
</script>