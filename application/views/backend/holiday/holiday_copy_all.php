<div class="pull-right" style="margin-bottom: 10px;">
    <button type="button" class="btn btn-primary selectAllBtn" data-click="0">
        <span class="glyphicon glyphicon-ok" aria-hidden="true"></span>
        <span class="txt">Select All</span>
    </button>
</div>
<br style="clear: both;" />

<div class="list-group">
    <?php
    echo form_open('', 'class="holidayForm"');
    echo '<div class="list-group-item active">Copy to Year ' . $year . '</div>';
    if(count($holiday_copy) > 0){
        foreach($holiday_copy as $v){
            ?>
            <div class="list-group-item">
                <div class="form-group">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="holiday_id[]" class="holiday_id" value="<?php echo $v->id; ?>"> <?php echo $v->holiday; ?>
                        </label>
                        <span class="glyphicon glyphicon-pencil editCopyBtn pull-right" data-click="0" aria-hidden="true"></span>
                    </div>
                    <div class="dateArea">
                        <div class="form-inline">
                            <div class="form-group">
                                <label>Date:</label>
                                <div class='input-group date' style="width: 300px;">
                                    <input type='text' name="date" class="required form-control" value="<?php echo date('d/m/', strtotime($v->date)) . $year; ?>" readonly/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>To:</label>
                                <div class='input-group date_to' style="width: 300px;">
                                    <input type='text' name="date_to" class="form-control" value="<?php
                                        if($v->date_to){
                                            echo date('d/m/', strtotime($v->date_to));
                                            echo date('Y', strtotime($v->date_to)) == date('Y', strtotime($v->date)) ?
                                                $year : $year + 1;
                                        }
                                        ?>" readonly/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    echo form_close();
    ?>
</div>

<style>
    .editCopyBtn{
        cursor: pointer;
    }
    .dateArea{
        margin: 0 20px;
        display: none;
    }
</style>
<script>
    $(function(e){
        var noHoliday = <?php echo count($holiday_copy) == 0 ? 1 : 0; ?>;
        if(noHoliday){
            $('.saveHolidayBtn').attr('disabled', 'disabled');
        }

        var selectAllBtn = $('.selectAllBtn');
        selectAllBtn.click(function(e){
            var isClick = $(this).data('click');
            $(this).data('click', isClick ? 0 : 1);
            if(!isClick){
                $('.holiday_id').attr('checked', true);
                $(this).find('.glyphicon')
                    .removeClass('glyphicon-ok')
                    .addClass('glyphicon-remove');
                $(this).find('.txt').html('Deselect All');
            }
            else{
                $('.holiday_id').attr('checked', false);
                $(this).find('.glyphicon')
                    .removeClass('glyphicon-remove')
                    .addClass('glyphicon-ok');
                $(this).find('.txt').html('Select All');
            }
        });

        var editCopyBtn = $('.editCopyBtn');
        editCopyBtn.click(function(e){
            var dateArea = $(this).parent('').parent('').find('.dateArea');
            var isClick = $(this).data('click');
            $(this).data('click', isClick ? 0 : 1);
            dateArea.css({
                display: isClick ? 'none' : 'block'
            });

        });
    });
</script>