<table class="table table-colored-header table-responsive">
    <thead>
    <tr>
        <th style="width: 20%;">Client Name</th>
        <th style="width: 5%;">Job Code</th>
        <th>Job Name</th>
        <th>Job Address</th>
        <th colspan="3" style="width: 25%;">Job Type</th>
        <th style="width: 7%;">Cost Sheet</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(count($client) >0):
        foreach($client as $v):
            $data = @$job_list[$v->id];
            ?>
            <tr>
                <td <?php echo count($data) > 0 ? 'rowspan="'.count($data).'"' : '';?> style="vertical-align: middle"><?php echo $v->client_name?></td>
                <?php
                $ref = 0;
                if(count($data) >0):
                    foreach($data as $val):
                        echo $ref != 0 ? '<tr>' : '';
                        ?>
                            <td><?php echo @$val['job_ref']?></td>
                            <td><?php echo @$val['job_name']?></td>
                            <td><?php echo @$val['address']?></td>
                            <td><?php echo @$val['job_type']?></td>
                            <td><?php echo @$val['story_type']?></td>
                            <td><?php echo @$val['inside_type']?></td>
                            <td><a href="<?php echo base_url().'jobCostSheet/'.$val['job_id']?>" class="tooltip-class" title="View Costsheet"><span class="glyphicon glyphicon-eye-open"></span></a></td>
                            <td><a href="<?php echo base_url().'jobEdit/job_list/'.$val['job_id']?>" class="edit-btn tooltip-class" title="Edit Job"><span class="glyphicon glyphicon-pencil"></a></td>
                        <?php
                        echo $ref != 0 ? '</tr>' : '';
                        $ref++;
                    endforeach;
                else:
                ?>
                        <td colspan="8" class="grey-background">No data has been found.</td>
                    </tr>
                <?php
                endif;
        endforeach;
    else:
    ?>
        <tr>
            <td colspan="8">No data was found.</td>
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
            content.load(url);
            $('.this-modal').modal();
        });
    });
</script>