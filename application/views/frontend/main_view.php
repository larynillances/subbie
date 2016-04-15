<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Subbie Solutions | <?php echo $page_header_title;?> </title>

    <!-- Bootstrap Core CSS -->
    <link href="<?php echo base_url();?>plugins/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?php echo base_url();?>plugins/css/business-casual-2.css" rel="stylesheet">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic" rel="stylesheet" type="text/css">
    <link rel="shortcut icon" href="<?php echo base_url();?>images/subbie-small-logo.png" />

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.<?php echo base_url()?>plugins/js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>
<div class="container">

    <!-- Static navbar -->
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo base_url();?>"><img src="<?php echo base_url().'images/subbie-small-logo.png'?>" width="80"> Subbie Solutions</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="<?php echo base_url();?>">Home</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'?page=about';?>">About</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'?page=services';?>">Our Services</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'?page=contact';?>">Contact Us</a>
                    </li>
                    <li>
                        <a href="<?php echo base_url().'?page=login';?>">Login</a>
                    </li>
                </ul>
            </div><!--/.nav-collapse -->
        </div><!--/.container-fluid -->
    </nav>
    <div class="row">
        <div class="container-fluid">
            <div>
                <div class="col-lg-12 text-center">
                    <?php $this->load->view($page_load);?>
                </div>
            </div>
        </div>
    </div>
</div> <!-- /container -->

<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <p>Copyright &copy; Subbie Solutions 2015</p>
            </div>
        </div>
    </div>
</footer>

<!-- jQuery -->
<script src="<?php echo base_url()?>plugins/js/jquery.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="<?php echo base_url()?>plugins/js/bootstrap.min.js"></script>

<!-- Script to Activate the Carousel -->
<script>
    $('.carousel').carousel({
        interval: 5000 //changes the speed
    })
</script>

</body>

</html>
