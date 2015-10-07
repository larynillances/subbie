<div class="row">
    <div class="col-sm-12">
        <div class="col-sm-2">
            <?php echo form_dropdown('status_id',$job_status,$job_status_id,'class="form-control input-sm filter-selection"')?>
        </div>
        <div class="col-sm-2">
            <?php echo form_dropdown('list_id',$list_type,$list_type_id,'class="form-control input-sm filter-selection"')?>
        </div>
    </div>
</div><br/>
<div class="row">
    <div class="col-sm-12">
        <table class="table table-responsive table-colored-header">
            <thead>
            <tr>
                <th></th>
                <th style="width: 9%;">Date In</th>
                <th style="width: 5%;">Subbie<br/>Ref</th>
                <th style="width: 20%;">Job Name</th>
                <th style="width: 5%;">Job Status</th>
                <th style="width: 5%;">Accpt.<br/>Date</th>
                <th style="width: 5%;">Start ETA</th>
                <th style="width: 5%;">Client Code</th>
                <th>m&sup2;</th>
                <th>hrs</th>
                <th style="width: 5%;">Job Rating</th>
                <th style="width: 5%;">Job Needed by</th>
                <th>Team</th>
                <th>Date Out</th>
                <th>Turn</th>
                <th>Material</th>
                <th>In House Notes</th>
            </tr>
            </thead>
            <tbody>
            <?php
            unset($job_status['']);
            if(count($job_data) > 0):
                foreach($job_data as $v):
                    $inv_value = $v->meter != '' ? $v->meter : $v->hours;
                    $complete_job = $v->status_id == 2 ? '<a href="#" class="set-complete" data-value="'.$inv_value.'" id="'.$v->id.'">'.$v->job_code.'</a>' : $v->job_code;
                    $status = $v->status_id == 3 ? $v->status.' ('.$v->completed_date.')' : $v->status;
                    ?>
                    <tr>
                        <td><a href="<?php echo base_url().'jobEdit/tracking/'.$v->id?>" class="edit-btn">E</a></td>
                        <td><?php echo $v->date_added;?></td>
                        <td><?php echo $v->job_ref;?></td>
                        <td><?php echo $v->job_name;?></td>
                        <td class="status-col" id="<?php echo $v->id?>">
                            <strong class="tooltip-class" data-toggle="tooltip" data-placement="right" title="<?php echo $status;?>">
                                <?php echo $v->job_code;?>
                            </strong>
                            <ul class="list-group status-option" id="<?php echo 'status_col_'.$v->id?>" style="position: absolute;display: none;margin: -50px 30px;">
                                <li class="list-header" style="padding: 2px 20px;background: #000000;color: #ffffff">Select Option</li>
                                <?php
                                if(count($job_status) > 0){
                                    foreach($job_status as $key=>$val){
                                        ?>
                                        <li class="list-group-item" style="padding: 3px;" data-value="<?php echo $v->id?>" id="<?php echo $key?>">
                                            <?php echo $val;?>
                                        </li>
                                    <?php
                                    }
                                }
                                ?>
                            </ul>
                        </td>
                        <td><?php echo $v->accepted_date;?></td>
                        <td><?php echo $v->start_date;?></td>
                        <td><?php echo $v->client_code;?></td>
                        <td><?php echo $v->meter;?></td>
                        <td><?php echo $v->hours;?></td>
                        <td>&nbsp;</td>
                        <td><?php echo $v->tender_date;?></td>
                        <td>
                            <?php
                            $ref = count($v->team_arr);
                            if(count($v->team_arr)>0){
                                foreach($v->team_arr as $team){
                                    echo $team;
                                    echo $ref != 1 ? ',' : '';
                                    $ref--;
                                }
                            }
                            ?>
                        </td>
                        <td><?php echo $v->completed_date;?></td>
                        <td><?php echo $v->duration;?></td>
                        <td>&nbsp;</td>
                        <td><?php echo $v->notes;?></td>
                    </tr>
                <?php
                endforeach;
            else:
                ?>
                <tr>
                    <td colspan="17">No data was found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<style>
    .table-colored-header tr td.status-col{
        cursor: pointer;
    }
    .table-colored-header tr td.status-col:hover,
    .table-colored-header tr td.active-col{
        background: #84b378;
    }
    ul li.list-group-item:hover{
        background: #b3a084;
    }
    ul li{
        list-style: none;
    }
</style>
<script>
    $(function(e){
        var content = $('.page-loader');
        $('.edit-btn').click(function(e){
            e.preventDefault();
            var url = $(this).attr('href');
            $('.modal-title').html('Edit Job');
            content.load(url);
            $('.this-modal').modal();
        });
        $('.set-complete').click(function(e){
            e.preventDefault();
            var url = bu + 'setJobComplete/' + this.id + '/' + $(this).attr('data-value');
            $('.sm-title').html('Job Complete');
            $('.sm-load-page').load(url);
            $('.sm-modal').modal();
        });
        $('.status-col').click(function(e){
            e.preventDefault();
            $('.status-col').removeClass('active-col');
            $(this).addClass('active-col');
            $('.status-option').css({'display':'none'});
            $('#status_col_' + this.id).css({'display':'inline'});
        });
        $('.list-header').live('click',function(){
            $('.status-col').removeClass('active-col');
            $('.status-option').css({'display':'none'});
        });
        $('.list-group-item').live('click',function(){
            $.post(bu + 'trackingLog',
                {
                    change_status: 1,
                    status_id: this.id,
                    job_id: $(this).data('value')
                },
                function(data){
                    console.log(data);
                    location.reload();
                }
            );
        });
        $('.filter-selection').change(function(e){
            var data = {};
            $('.filter-selection').each(function(e){
                var name = $(this).attr('name');

                data[name] = $(this).val();
            });
            data['go'] = 1;
            $.post(bu + 'trackingLog',
                data,
                function(data){
                    location.reload();
                }
            );
        });
    });
</script>