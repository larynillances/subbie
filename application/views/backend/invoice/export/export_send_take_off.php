<?php
echo $comment ? $comment . "<br /><br />" : '';
echo 'Kind regards<br /><br />';
echo $franchise->franchise_branch_manager . "<br />";
echo $franchise->franchise_branch_name . "<br />";
echo $franchise->franchise_phone . " ph<br />";
echo $franchise->franchise_fax . " fax<br />";
echo $franchise->franchise_branch_email . "<br />";
echo base_url().'<br /><br />';
echo '<img src="'.base_url('images/subbie-small-logo.png').'" />';