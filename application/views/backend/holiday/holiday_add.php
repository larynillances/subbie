<?php
echo form_open('', 'class="holidayForm"');
    ?>
    <div class="form-inline">
        <div class="form-group">
            <label>Date:</label>
            <div class='input-group date' style="width: 300px;">
                <input type='text' name="date" class="required form-control" readonly/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label>To:</label>
            <div class='input-group date_to' style="width: 300px;">
                <input type='text' name="date_to" class="form-control" readonly/>
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Holiday:</label>
        <input type="text" name="holiday" class="required form-control" />
    </div>
    <div class="form-group">
        <label>Description:</label>
        <textarea name="description" class="form-control" rows="5"></textarea>
    </div>
    <div class="form-group">
        <label>Type:</label>
        <?php
        echo form_dropdown('type', $type, '', 'class="form-control"');
        ?>
    </div>
    <?php
echo form_close();
?>

<link rel="stylesheet" href="<?php echo base_url() . "plugins/css/bootstrap-select.css"; ?>" />
<script src="<?php echo base_url() . "plugins/js/bootstrap-select.js"; ?>"></script>
<style>
    .input-group-addon{
        cursor: pointer;
    }
</style>
<script>
    $(function (e) {
        //region Multi-select Area
        var f = $('.franchise');
        f
            .selectpicker({
                showIcon: 0
            })
            .on('change', function(e){
                fSelect();
            });
        function fSelect(){
            var fOption = $('.franchise option:selected');
            if($.inArray("all", f.val()) != -1){
                fOption = $('.franchise option:not([value="all"])');
                fOption.each(function(e){
                    $(this).attr('disabled', 'disabled');
                });
                f.selectpicker('val', 'all');
                f.selectpicker('refresh');
            }
            else{
                $('.franchise option').each(function(e){
                    $(this).removeAttr('disabled');
                });
                f.selectpicker('refresh');
            }
        }
        //endregion
    });
</script>