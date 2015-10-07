<div class="page-header">
    <div class="pull-right form-inline">
        <div class="btn-group">
            <button class="btn btn-sm btn-primary" data-calendar-nav="prev"><< Prev</button>
            <button class="btn btn-sm" data-calendar-nav="today">Today</button>
            <button class="btn btn-primary btn-sm" data-calendar-nav="next">Next >></button>
        </div>
        <div class="btn-group">
            <button class="btn btn-warning btn-sm" data-calendar-view="year">Year</button>
            <button class="btn btn-warning btn-sm active" data-calendar-view="month">Month</button>
            <button class="btn btn-warning btn-sm" data-calendar-view="week">Week</button>
            <button class="btn btn-warning btn-sm" data-calendar-view="day">Day</button>
        </div>
    </div>
    <h3></h3><br/>
    <button class="btn btn-success btn-sm allocate-job" <?php echo count($job_allocated) > 0 ? '' : 'disabled'?> >Allocate Job</button>
    <button class="btn btn-success btn-sm add-sched">Add Event</button>
    <span style="<?php echo count($job_allocated) > 0 ? '' : 'display:none;'?>">Job to be Allocate:</span>
    <?php
    if(count($job_allocated) >0):
        foreach($job_allocated as $jv):
            ?>
                <strong style="padding: 2px!important;background:#484748;color: #FFFFff"><?php echo $jv->job_name;?></strong>
            <?php
        endforeach;
    endif;
    ?>
</div>
<div class="multi-date-picker"></div>
<div class="calendar"></div>

<div class="modal fade modal-load" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Edit Invoice</h4>
            </div>
            <div class="content-loader"></div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $(function(e){
        var calendar = $(".calendar");
        calendar.calendar({
            tmpl_path: bu + "plugins/tmpls/",
            events_source: bu + 'eventsJson'
        });
        var url;
        var content = $('.content-loader');
        var modal_title = $('.modal-title');
        $('.allocate-job').click(function(e){
           /*$(this).newForm.addNewForm({
               title: 'Allocate Job',
               url: bu + 'allocateJob',
               toFind: '.form-horizontal'
           });*/
            modal_title.html('Allocate Job');
            url = bu + 'allocateJob';
            content.load(url);
            $('.modal-load').modal();
        });
        $('.add-sched').click(function(e){
            /*$(this).newForm.addNewForm({
                title: 'Add Schedule',
                url: bu + 'addSchedule',
                toFind: '.form-horizontal'
            });*/
            modal_title.html('Add Events Schedule');
            url = bu + 'addSchedule';
            content.load(url);
            $('.modal-load').modal();
        });
    })
</script>
<style>
    .calendar-class:hover{
        color: white;
    }
</style>