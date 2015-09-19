<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Subbie Solutions | Login</title>

    <!-- Bootstrap -->
    <link href="<?php echo base_url();?>plugins/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/css/signin.css" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo base_url();?>images/subbie-small-logo.png" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url();?>plugins/js/html5shiv.js"></script>
    <script src="<?php echo base_url();?>plugins/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div class="container">
    <?php
    echo form_open('validate','class="form-signin" role="form"');
    ?>
        <h2 class="form-signin-heading">Subbie Solutions</h2>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Username" required autofocus name="email">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Password" required name="password">
        </div>
        <label class="checkbox">
            <input type="checkbox" value="remember-me"> Remember me
        </label>
        <?php
        echo $this->session->flashdata('error_msg');
        ?>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="login">Sign in</button>
    <?php
    echo form_close();
    ?>
</div>

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo base_url();?>plugins/js/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo base_url();?>plugins/js/bootstrap.min.js"></script>
</body>
</html>