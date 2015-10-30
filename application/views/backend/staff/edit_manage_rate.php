<?php
echo form_open('','class="form-horizontal" role="form"');
    if(count($rate)>0):
        foreach($rate as $v):
            ?>
            <div class="modal-body">
                <div class="form-group">
                    <label class="col-sm-4 control-label">Description:</label>
                    <div class="col-sm-8">
                        <input type="text" name="rate_name" class="form-control input-sm number description-class" value="<?php echo $v->rate_name;?>">
                        <div class="msg-error" style="position: absolute;padding: 5px;white-space:nowrap;margin: -30px 170px;"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Cost:</label>
                    <div class="col-sm-8">
                        <input type="text" name="rate_cost" class="form-control input-sm number" value="<?php echo $v->rate_cost;?>">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-sm btn-primary submit-btn" name="submit">Submit</button>
                <button type="button" class="btn btn-sm btn-default cancel-btn" data-dismiss="modal">Cancel</button>
            </div>
        <?php
        endforeach;
    endif;
echo form_close();
?>
<script>
    $(function(){
        $('.description-class').focusout(function(e){
            e.preventDefault();
            var data = $(this).val();
            var msg_error = $('.msg-error');
            var submit_btn = $('.submit-btn');
            msg_error
                .html('')
                .removeClass('alert-danger');
            $.post(bu + 'rateManage?search=1',
                {search_:1,data:data},
                function(data){
                    if(data != 0){
                        msg_error
                            .html(data)
                            .addClass('alert-danger');
                        submit_btn.attr('disabled','disabled');
                    }else{
                        msg_error
                            .html('')
                            .removeClass('alert-danger');
                        submit_btn.removeAttr('disabled');
                    }
                }
            );
        });
    })
</script>