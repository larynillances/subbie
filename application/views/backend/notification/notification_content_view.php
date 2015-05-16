<ul>
<?php
$ref = count($notification);
if(count($notification) > 0){
    foreach($notification as $nv){
        $url = 'jobInvoice/'.$nv->client_id;
        $str = 'Invoice <strong>'.$nv->job_ref.'</strong> has been added by '.$user_data->name.'.';
        ?>
        <li>
            <a href="<?php echo base_url().$url?>" class="msg-link" id="<?php echo $nv->id;?>">
                <div>
                    <i class="glyphicon glyphicon glyphicon-eye-close"></i>&nbsp;
                    <strong><?php echo $user_data->name .':';?></strong>
                            <span class="pull-right text-muted" style="font-size: 11px;">
                                <em><?php echo date('d/m/Y',strtotime($nv->date))?></em>
                            </span>
                </div>
                <div><?php echo $str?></div>
            </a>
        </li>
        <?php
        echo $ref != 1 ? '<li class="divider"></li>' : '';

        $ref--;
    }
}else{
    echo '<li>No message has been found.</li>';
}
?>
</ul>