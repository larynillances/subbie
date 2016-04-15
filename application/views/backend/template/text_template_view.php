<div class="row">
    <div class="col-lg-8">
        <a href="#" class="btn btn-success add-btn"><span class="glyphicon glyphicon-plus"></span> Add New</a>
    </div>
</div><br/>
<div class="row">
    <div class="col-lg-10">
        <table class="table table-colored-header table-responsive">
            <thead>
            <tr>
                <th>Title</th>
                <th style="text-align: left;">Value</th>
                <th style="width: 7%;"></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($template) > 0):
                foreach($template as $v):
                    ?>
                    <tr>
                        <td style="text-align: left;"><?php echo $v->title;?></td>
                        <td style="text-align: left;"><?php echo str_replace("\n",'<br/>',$v->value);?></td>
                        <td style="vertical-align: middle;"><a href="#" class="tooltip-class edit-btn" title="Edit" id="<?php echo $v->id;?>"><span class="glyphicon glyphicon-pencil"></span></a></td>
                    </tr>
                    <?php
                endforeach;
            else:
            ?>
                <tr>
                    <td colspan="3">No data has been found.</td>
                </tr>
            <?php
            endif;
            ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function(e){
        var page = $('.page-loader');
        var title = $('.modal-title');
        var url;
        $('.add-btn').click(function(e){
            e.preventDefault();
            url = bu + 'manageTextTemplate/add';
            title.html('Add New Text Template');
            page.load(url);
            $('.this-modal').modal();
        });
        $('.edit-btn').click(function(e){
            e.preventDefault();
            url = bu + 'manageTextTemplate/edit/' + this.id;
            title.html('Edit Text Template');
            page.load(url);
            $('.this-modal').modal();
        });
    });
</script>