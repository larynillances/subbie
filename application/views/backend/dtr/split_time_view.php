<div class="modal-body">
    <table class="table table-colored-header">
        <thead>
        <tr>
            <th width="120">Job Code</th>
            <th>Job Name</th>
            <th width="80">Hours</th>
        </tr>
        </thead>
        <tbody class="table-body">
        <?php
        if(count($job_assign) > 0){
            foreach($job_assign as $jv){
                ?>
                <tr>
                    <td><?php echo $jv->job_code;?></td>
                    <td><?php echo $jv->job_name;?></td>
                    <td><?php echo $jv->hours;?></td>
                </tr>
            <?php
            }
        }else{
            ?>
            <tr>
                <td colspan="4">No Data has found.</td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default cancel-btn" data-dismiss="modal">Cancel</button>
</div>