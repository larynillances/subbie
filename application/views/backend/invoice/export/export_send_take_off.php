<?php
echo $comment ? $comment . "<br /><br />" : '';
echo 'Kind regards<br /><br />';
echo $franchise->contact_name . "<br />";
echo $franchise->client_name . "<br />";
echo $franchise->phone . " ph<br />";
echo $franchise->mobile . " mobile<br />";
echo $franchise->email . "<br />";
echo base_url().'<br /><br />';
echo '<img src="'.base_url('images/subbie-small-logo.png').'" />';