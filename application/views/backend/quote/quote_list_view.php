<table class="table table-responsive table-colored-header">
    <thead>
    <tr>
        <th style="width: 8%;">Date</th>
        <th style="width: 5%;">Quote #</th>
        <th style="width: 15%;">Client Name</th>
        <th>Job Name</th>
        <th style="width: 15%;">Closure Date</th>
        <th style="width: 15%;">Announcement Date</th>
        <th style="width: 10%;">Status</th>
        <th style="width: 8%;"></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(count($quote_list) > 0):
        foreach($quote_list as $v):
            ?>
            <tr>
                <td><?php echo $v->date_requested;?></td>
                <td><?php echo $v->quote_num;?></td>
                <td><?php echo $v->client_name;?></td>
                <td><?php echo $v->job_name;?></td>
                <td><?php echo $v->closure_date;?></td>
                <td><?php echo $v->announce_date;?></td>
                <td>
                    <span <?php echo $v->accepted !='' ? 'class="tooltip-class" data-toggle="tooltip" data-placement="top" title="'.$v->accepted.'"' : ''?>>
                       <?php echo $v->status;?>
                    </span>
                </td>
                <td>
                    <a href="<?php echo base_url().'quoteRequested/'. $v->client_id .'/'.$v->id.'/quoteList'?>" class="edit-btn">view</a>
                    <?php if(!$v->is_accepted):?>&nbsp;
                    <a href="#" class="accept-btn" id="<?php echo $v->id;?>">accept</a>
                    <?php endif;?>
                </td>
            </tr>
            <?php
        endforeach;
    else:
    ?>
        <tr>
            <td colspan="8">No data has been found.</td>
        </tr>
    <?php
    endif;
    ?>
    </tbody>
</table>
<script>
    $(function(e){
        $('.accept-btn').click(function(e){
            e.preventDefault();
            var thisId = this.id;
            $(this).newForm.formDeleteQuery({
                title:"Accept Quote",
                msg:"Are you sure you want to accept this quote?",
                superClass:"btn btn-success"
            });

            $('body')
                .on('click','.yesBtn',function(e){
                    $.post(
                        '<?php echo base_url();?>acceptQuote/' + thisId,
                        {

                        },
                        function(e){
                            location.reload();
                        }
                    );
                })
                .on('click','.noBtn',function(e){
                    $(this).newForm.forceClose();

                })
        });
    });
</script>