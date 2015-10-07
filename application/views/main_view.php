<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subbie | <?php echo $page_name;?></title>

    <!-- Bootstrap -->
    <link href="<?php echo base_url();?>plugins/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/css/my_css.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/css/sticky-footer.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/css/smoothness/jquery-ui.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/css/addForm.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/css/bootstrap-datetimepicker.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/css/calendar.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/css/mdp.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/css/slick.grid.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/css/example-bootstrap.css" rel="stylesheet">
    <link href="<?php echo base_url();?>plugins/font-awesome-4.1.0/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo base_url() . "plugins/css/email.validation.css"; ?>" />
    <link rel="shortcut icon" href="<?php echo base_url();?>images/subbie-small-logo.png" />
    <link href="<?php echo base_url().'plugins/multi-select/css/bootstrap-multiselect.css';?>" rel="stylesheet">
    <link href="<?php echo base_url().'plugins/css/fileinput.css';?>" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="<?php echo base_url();?>plugins/js/html5shiv.js"></script>
    <script src="<?php echo base_url();?>plugins/js/respond.min.js"></script>
    <![endif]-->

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="<?php echo base_url();?>plugins/js/jquery-1.8.3.min.js"></script>
    <!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>-->
    <script src="<?php echo base_url();?>plugins/js/jquery-ui.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo base_url();?>plugins/js/underscore-min.js"></script>
    <script src="<?php echo base_url();?>plugins/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url();?>plugins/js/moment.js"></script>
    <script src="<?php echo base_url();?>plugins/js/bootstrap-datetimepicker.min.js"></script>
    <script src="<?php echo base_url();?>plugins/js/jquery-ui.multidatespicker.js"></script>
    <script src="<?php echo base_url();?>plugins/js/addForm.js"></script>
    <script src="<?php echo base_url();?>plugins/js/number.js"></script>
    <script src="<?php echo base_url();?>plugins/js/table-fixed-header.js"></script>
    <script src="<?php echo base_url();?>plugins/js/jquery.scrollTableBody-1.0.0.js"></script>
    <script src="<?php echo base_url();?>plugins/js/jquery.event.drag-2.0.min.js"></script>
    <script src="<?php echo base_url();?>plugins/js/slick.core.js"></script>
    <script src="<?php echo base_url();?>plugins/js/slick.grid.js"></script>
    <script src="<?php echo base_url();?>plugins/js/slick.dataview.js"></script>
    <script src="<?php echo base_url();?>plugins/js/bootstrap-slickgrid.js"></script>
    <script src="<?php echo base_url();?>plugins/js/select.country.js"></script>
    <script src="<?php echo base_url();?>plugins/js/main.js"></script>
    <script src="<?php echo base_url();?>plugins/js/modified-modal.js"></script>
    <script src="<?php echo base_url() . "plugins/js/email.validation.js"; ?>"></script>
    <script src="<?php echo base_url() . "plugins/multi-select/js/bootstrap-multiselect.js" ?>"></script>
    <script src="<?php echo base_url() . "plugins/js/fileinput.js" ?>"></script>
    <script>
        $.ajaxSetup({ cache: false });
        jQuery(document).ready(function($){
            // Get current url
            // Select an a element that has the matching href and apply a class of 'active'. Also prepend a - to the content of the link
            //var url = window.location.href;
            $.post(bu + 'checkTrackingLogPage',{select: 1,page_name:window.location.pathname},function(data){});
        });
    </script>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
          </button>
            <a class="navbar-brand" href="<?php echo base_url().'trackingLog'?>">Subbie Solutions</a>
        </div>
        <div class="navbar-collapse collapse">
            <?php $this->load->view('header_view');?>
        </div>
    </div>
</div>
<div class="container" style="width: 1300px;">
    <h2 class="page-header"><?php echo $page_name;?></h2>
    <?php $this->load->view($page_load);?>
</div>
<div id="footer">
    <div class="container">
        <p class="text-muted">2014 &copy; Subbie Solutions</p>
    </div>
</div>

<!--large modal-->
<div class="modal fade this-modal bs-example-modal-lg largeModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Add Invoice Entry</h4>
            </div>
            <div class="page-loader lg-page-load"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--default modal-->
<div class="modal fade my-modal defaultModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title my-title">New Order</h4>
            </div>
            <div class="load-page df-page-load"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--small modal-->
<div class="modal fade bs-example-modal-sm sm-modal smallModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title sm-title">New Order</h4>
            </div>
            <div class="sm-load-page sm-page-load"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!--javascript code-->
<script>
    var bu = '<?php echo base_url()?>';
    $('.tooltip-class').tooltip();
    /*$('.table-fixed-header').fixedHeader();*/

    $(function(e){
        var content = $('.page-loader');
        var page = $('.load-page');
        var title = $('.my-title');
        $('.invoiceCreateBtn').click(function(e){
            e.preventDefault();
            var url = bu + 'invoiceCreate';
            content.load(url);
            $('.this-modal').modal();
        });

        $('.orderBookInputBtn').click(function(e){
            e.preventDefault();
            var url = bu + 'orderBookInput';
            page.load(url);
            $('.my-modal').modal();
        });

        $('.quotationBtn').click(function(e){
            e.preventDefault();
            title.html('New Quote Request');
            var url = bu + 'quotation/new';
            page.load(url);
            $('.my-modal').modal();
            /*$(this).newForm.addNewForm({
                title: 'New Quote Request',
                url: bu + 'quotation/new',
                toFind:'.form-horizontal'
            });*/
        });
    });
</script>
<script src="<?php echo base_url();?>plugins/js/calendar.js"></script>
<script src="<?php echo base_url();?>plugins/js/jstz.min.js"></script>
<script src="<?php echo base_url();?>plugins/js/app.js"></script>
</body>
</html>