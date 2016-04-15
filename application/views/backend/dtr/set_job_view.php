<?php
echo form_open('','class="form-horizontal" role="form"');
?>
<div class="modal-body">
    <div class="form-group">
        <label class="col-sm-1 control-label" style="white-space: nowrap;">Job Ref.</label>
        <div class="col-sm-6">
            <?php echo form_dropdown('job_id',$job_list,'','class="form-control required job_id input-sm"');?>
        </div>
        <div class="col-sm-3">
            <input type="text" class="form-control input-sm required" id="hours" name="hours_value" placeholder="Hours">
        </div>
        <div class="col-sm-1" style="margin-left: -20px;">
            <a href="#" class="add-btn"><strong style="font-size: 20px;"><i class="glyphicon glyphicon-plus"></i></strong></a>
        </div>
    </div>
    <div class="form-group">
        <table class="table table-colored-header">
            <thead>
            <tr>
                <th width="120">Job Code</th>
                <th>Job Name</th>
                <th width="80">Hours</th>
                <th width="50"></th>
            </tr>
            </thead>
            <tbody class="table-body">
            <tr class="empty-col">
                <td colspan="4">No Data has found.</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <!--<button type="submit" class="btn btn-primary submit-btn" name="submit">Submit</button>-->
    <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
</div>
<?php
echo form_close();
?>
<script>
    $(function(e){
        var staff_id = '<?php echo $this->uri->segment(2);?>';
        var date = '<?php echo $this->uri->segment(3);?>';
        var hours = $('#hours');
        var empty_col = $('.empty-col');
        var table_body = $('.table-body');

        hours.numberOnly({
            hasMaxChar: true,
            maxCharLen: 4
        });

        $.getJSON(bu + 'assignJob/json/' + staff_id + '/' + date, function(data) {
            if(data.length > 0){
                var html = '';

                empty_col.css({'display':'none'});

                $.each(data, function(idx, obj) {
                    html += '<tr>';
                        html += '<td>' + obj.job_code + '</td>';
                        html += '<td>' + obj.job_name + '</td>';
                        html += '<td>' + obj.hours + '</td>';
                        html += '<td><a href="#" class="remove-btn" id="'+ obj.id +'"><i class="glyphicon glyphicon-remove"></i></a></td>';
                    html += '</tr>';
                });
                table_body.html(html);
            }
        });

        $('.add-btn').click(function(e){
            var hasEmpty = false;
            var hours = $('#hours');
            var job = $('.job_id');

            $('.required').each(function(e){
                if(!$(this).val()){
                    hasEmpty = true;
                    $(this).css({
                        border:'1px solid #a94442'
                    });
                }
            });
            if(hasEmpty){
                e.preventDefault();
            }else{
                $.post(bu + 'assignJob/assign/' + staff_id + '/' + date, {hour:hours.val(),job:job.val()},
                    function(data){
                        var json = JSON.parse(data);
                        var html = '';

                        empty_col.css({'display':'none'});

                        $.each(json, function(idx, obj) {
                            html += '<tr>';
                                html += '<td>' + obj.job_code + '</td>';
                                html += '<td>' + obj.job_name + '</td>';
                                html += '<td>' + obj.hours + '</td>';
                                html += '<td><a href="#" class="remove-btn" id="'+ obj.id +'"><i class="glyphicon glyphicon-remove"></i></a></td>';
                            html += '</tr>';
                        });
                        table_body.html(html);

                        hours.val('');
                        job.val(1);
                    }
                );
            }
        });

        $('.remove-btn').live('click',function(e){
            $.post(bu + 'assignJob/delete/' + staff_id + '/' + date, {id:this.id},
                function(data){
                    var html = '';
                    var json = JSON.parse(data);
                    if(json.length > 0){
                        empty_col.css({'display':'none'});

                        $.each(json, function(idx, obj) {
                            html += '<tr>';
                                html += '<td>' + obj.job_code + '</td>';
                                html += '<td>' + obj.job_name + '</td>';
                                html += '<td>' + obj.hours + '</td>';
                                html += '<td><a href="#" class="remove-btn" id="'+ obj.id +'"><i class="glyphicon glyphicon-remove"></i></a></td>';
                            html += '</tr>';
                        });
                        table_body.html(html);
                    }else{
                        html += '<tr>';
                            html += '<td colspan="4">No Data has found.</td>';
                        html += '</tr>';
                        table_body.html(html);
                    }
                }
            );
        });

        $('.cancel-btn').click(function(e){
           location.reload();
        });
        /*$('.fade').click(function(e){
           location.reload();
        });*/
    });
</script>