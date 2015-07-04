<?php
$uri2 = $this->uri->segment(2);
$uri3 = $this->uri->segment(3);
echo form_open('');
?>
<div class="modal-body">
    <div class="form-group">
        <label class="control-label col-sm-1" for="email">Email:</label>
        <input type="email" name="email" class="form-control input-sm" id="email" value="<?php echo $email;?>" placeholder="Email">
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