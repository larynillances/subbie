<?php
echo form_open('','class="form-horizontal" data-toggle="validator"');
    ?>
    <div class="form-group">
        <div class="col-sm-12">
            <input type="text" name="emailOption[clerk]" class="form-control email-check" value="<?php echo @$emails['clerk']; ?>" placeholder="Wages Clerk" />
            <span class="error-msg"></span>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input type="text" name="emailOption[spare]" class="form-control email-check" value="<?php echo @$emails['spare']; ?>" placeholder="Spare" />
            <span class="error-msg"></span>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <input type="text" name="emailOption[copy]" class="form-control email-check" value="<?php echo @$emails['copy']; ?>" placeholder="Copy To local Franchise" />
            <span class="error-msg"></span>
        </div>
    </div>
    <div class="pull-right">
        <button type="submit" name="submitEmail" class="btn btn-success submit">Submit</button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">Cancel</button>
    </div>
    <br style="clear: both;" />
    <br />
    <?php
echo form_close();
?>
<style>
    .error-msg{
        color: #cf6e65;
    }
</style>
<script>
    $(function() {
        $('.email-check').focusout(function(e){
            e.preventDefault();

            $(this).parent().find('.error-msg').html('');
            $(this).parent().parent().find('.col-sm-12').removeClass('has-error');

            if($(this).val() && !isValidEmailAddress($(this).val())){

                $(this).parent().parent().find('.col-sm-12').addClass('has-error');
                $(this).parent().find('.error-msg')
                    .html('Please enter a valid email address.');
            }

        });
        $('.submit').click(function(e){
            var hasEmpty = false;
            $('.email-check').each(function(){
                $(this).parent().parent().find('.col-sm-12').removeClass('has-error');

                if($(this).val() && !isValidEmailAddress($(this).val())){
                    $(this).parent().parent().find('.col-sm-12').addClass('has-error');
                    hasEmpty = true;
                }
            });
            if(hasEmpty){
                e.preventDefault();
            }
        });
    });
</script>