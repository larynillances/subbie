<div style="padding: 5px">
    <a href="#" class="btn btn-success add-client"><span class="glyphicon glyphicon-plus-sign"></span> add client</a>
   <!-- <input type="button" name="addClient" class="btn btn-success add-client" value="add client">-->
</div>
<table class="table table-colored-header">
    <thead>
    <tr>
        <th style="width: 25%">Client Name</th>
        <th>Code</th>
        <th>Contact Name</th>
        <th>Phone No.</th>
        <th>Mobile No.</th>
        <th style="width: 25%;">Address</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if(count($client)>0):
        foreach($client as $v):
        ?>
        <tr>
            <td style="text-align: left;"><?php echo $v->client_name?></td>
            <td><?php echo $v->client_code?></td>
            <td><?php echo $v->contact_name?></td>
            <td><?php echo $v->phone?></td>
            <td><?php echo $v->mobile?></td>
            <td style="text-align: left;"><?php echo str_replace("\n",'<br/>',$v->address)?></td>
            <td style="white-space: nowrap!important;vertical-align: middle;">
                <a href="<?php echo base_url().'quotation/request/'.$v->id?>" class="requestBtn tooltip-class" data-toggle="tooltip" data-placement="top" title="Request Quote">
                    <span class="glyphicon glyphicon-file"></span>
                </a>&nbsp;
                <a href="#" id="<?php echo $v->id;?>" class="edit-client tooltip-class" data-toggle="tooltip" data-placement="top" title="Edit">
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>&nbsp;
                <a href="<?php echo base_url().'manageClient/delete/'.$v->id;?>" class="delete-client tooltip-class" data-toggle="tooltip" data-placement="top" title="Delete">
                    <span class="glyphicon glyphicon-remove"></span>
                </a>
            </td>
        </tr>
        <?php
        endforeach;
    else:
    ?>
        <tr>
            <td colspan="8">No data has found.</td>
        </tr>
    <?php
    endif;
    ?>
    </tbody>
</table>
<script>
    $(function(){
        var page = $('.load-page');
        var title = $('.my-title');
        var url;
        $('.add-client').click(function(e){
            e.preventDefault();
            $(this).modifiedModal({
                url: bu + 'manageClient/add',
                title: 'Add Client',
                type: 'large'
            });
        });
        $('.edit-client').click(function(e){
            $(this).modifiedModal({
                url: bu + 'manageClient/edit/' + this.id,
                title: 'Edit Client',
                type: 'large'
            });
        });
        $('.requestBtn').click(function(e){
            var href = $(this).attr('href');
            title.html('New Quote');
            e.preventDefault();
            page.load(href);
            $('.my-modal').modal();
        });
    });
</script>