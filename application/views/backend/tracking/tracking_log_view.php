<table class="table table-responsive table-colored-header">
    <thead>
    <tr>
        <th></th>
        <th style="width: 9%;">Date In</th>
        <th style="width: 5%;">Subbie<br/>Ref</th>
        <th style="width: 20%;">Job Name</th>
        <th style="width: 5%;">Job Status</th>
        <th style="width: 5%;">Accpt. Date</th>
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
                <td>
                    <strong class="tooltip-class" data-toggle="tooltip" data-placement="right" title="<?php echo $status;?>">
                        <?php echo $complete_job;?>
                    </strong>
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
            <td colspan="16">No data has found.</td>
        </tr>
    <?php
    endif;
    ?>
    </tbody>
</table>
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
    });
</script>