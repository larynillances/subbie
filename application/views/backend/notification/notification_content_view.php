<ul>
<?php
$words = explode(" ", $user_data->name);
$sender = "";
if(count($words) > 0){
    foreach($words as $wv){
        $sender .= substr($wv,0,1);
    }
}
$ref = count($notification);
if(count($notification) > 0){
    foreach($notification as $nv){
        $job_name_arr = explode("\n",$nv->job_name);
        $check_account_txt = explode(" ",$job_name_arr[0]);
        $job_name = '';
        $reff = count($check_account_txt);
        if(count($check_account_txt) > 0){
            foreach($check_account_txt as $cv){
                if($cv != ''){
                    $job_name .= $cv;
                    $job_name .= $reff != 1 ? ' ' : '';
                }
                $reff--;
            }
        }
        $job_name = str_replace('A/C','',$job_name);
        $job_name = str_replace('a/c','',$job_name);
        $job_name = str_replace(':','',$job_name);

        $url = 'jobInvoice/'.$nv->client_id.'/'.$nv->id;
        $link = '<strong>'.$nv->job_ref.' ('.$job_name.')</strong>';
        $str = 'added by '.$sender.'.';
        ?>
        <li>
            <div>
                <i class="glyphicon glyphicon glyphicon-eye-close tooltip-class msg-link" id="<?php echo $nv->id;?>" title="Mark as Read"></i>&nbsp;
                <strong><?php echo $user_data->name .':';?></strong>
                        <span class="pull-right text-muted" style="font-size: 11px;">
                            <?php echo $nv->is_open ? '<em style="color: #d73e3b;font-weight: bold">(opened)</em>' : ''?>
                            <em><?php echo date('d/m/Y',strtotime($nv->date))?></em>
                        </span>
            </div>
            <div>
                <a href="<?php echo base_url().$url?>" class="msg-open" id="<?php echo $nv->id;?>" style="color: #1e90ff;">
                    <?php echo $link?>
                </a>
                <?php echo ' '.$str?>
            </div>
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
<script>
    $(function(){
        $('.tooltip-class').tooltip();
    })
</script>