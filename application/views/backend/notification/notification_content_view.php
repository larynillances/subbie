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

        $url = $nv->is_new && $nv->is_archive && $nv->is_open ? 'editArchiveInvoice/'.$nv->client_id.'/'.$nv->inv_ref : 'jobInvoice/'.$nv->client_id.'/'.$nv->id;
        $link = $nv->is_new && $nv->is_archive && $nv->is_open ? '<strong>'.$nv->inv_ref.' ('.$job_name.')</strong>' : '<strong>'.$nv->job_ref.' ('.$job_name.')</strong>';
        $str = 'added by '.$sender.'.';
        ?>
        <li>
            <div>
                <i class="glyphicon glyphicon glyphicon-eye-close tooltip-class msg-link" id="<?php echo $nv->id;?>" title="Mark as Read"></i>&nbsp;
                <strong><?php echo $user_data->name .':';?></strong>
                        <span class="pull-right text-muted" style="font-size: 11px;">
                            <?php
                            if($nv->is_new && !$nv->is_archive && !$nv->is_open){
                                echo '<em style="color: #d73e3b;font-weight: bold">(New)</em>';
                            }else if($nv->is_new && $nv->is_archive && $nv->is_open){
                                echo '<em style="color: #d73e3b;font-weight: bold">(Archived)</em>';
                            }
                            ?>
                            <em><?php echo date('d/m/Y',strtotime($nv->date))?></em>
                        </span>
            </div>
            <div>
                <a href="<?php echo base_url().$url?>#" class="msg-open" id="<?php echo $nv->id;?>" style="color: #1e90ff;">
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
        $('.filter_msg').change(function(e){
            e.stopPropagation();
            var notification = $('.notification-class');
            var ele = '<img src="'+ bu + 'images/loading_(2).gif" class="loading-img" style="height: 30px;margin:0 155px;">';
            var loading = $('.loading-img');
            var read_all_msg = $('.read-all-message');
            var this_val = $(this).val();
            notification.html(ele);
            notification.load(bu + 'updateNotification/'+ this_val +'?is_view=true',
                function(){
                    loading.css({
                        'display' : 'none'
                    });
                    read_all_msg.css({
                        'display' : 'none'
                    });
                    if(this_val == 3){
                        read_all_msg.css({
                            'display' : 'inline'
                        });
                    }
                }
            );
        });

        $('.msg-open').click(function(e){
            var id = this.id;
            $.post(bu + 'updateNotification/' + id + '?active=1',function(e){
                console.log(e);
            });
        });

        $('.msg-link').click(function(e){
            var id = this.id;
            $.post(bu + 'updateNotification/' + id,function(data){
                /*location.reload();*/
                var notification = $('.notification-class');
                var ele = '<img src="'+ bu + 'images/loading_(2).gif" class="loading-img" style="height: 30px;margin:0 155px;">';
                var loading = $('.loading-img');
                if(data){
                    notification.html(ele);
                    notification.load(bu + 'updateNotification/'+ $('.filter_msg').val() +'?is_view=true',
                        function(){
                            loading.css({
                                'display' : 'none'
                            })
                        }
                    );
                }
            });
        });
    })
</script>