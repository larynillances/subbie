<style>
    .iconArea{
        float: right;
    }
    .iconArea span{
        margin-right: 5px;
    }
    .icon-button{
        cursor: pointer;
    }
</style>
<?php
echo form_open('');
    ?>
    <div class="filterArea form-inline">
        <input type="text" class="holiday form-control input-sm" data-provide="typeahead" name="holiday" value="<?php echo @$holidayFilter->holiday; ?>" placeholder="Holiday Title" autocomplete="off" />
        <div class="form-group">
            <?php
            echo form_dropdown('year', $year, @$holidayFilter->year, 'class="year form-control input-sm"');
            ?>
        </div>
        <div class="form-group">
            <?php
            echo form_dropdown('type', $type, @$holidayFilter->type, 'class="type form-control input-sm"');
            ?>
        </div>
        <input type="submit" class="optionBtn btn btn-sm btn-primary" name="filter" value="Filter" />
        <input type="submit" class="optionBtn btn btn-sm btn-danger" name="clearFilter" value="Clear" />
        <button class="optionBtn btn btn-sm btn-primary collapseAllBtn" type="button">
            <?php
            if($holidayFilter->isExpandAll){
                echo '<span class="glyphicon glyphicon-chevron-down"></span> <span class="txt">Expand All</span>';
            }
            else{
                echo '<span class="glyphicon glyphicon-chevron-up"></span> <span class="txt">Collapse All</span>';
            }
            ?>
            <input type="hidden" name="isExpandAll" value="<?php echo $holidayFilter->isExpandAll ? 1 : 0; ?>" />
        </button>
        <button type="button" class="btn-primary btn btn-sm addHolidayBtn">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add
        </button>
        <?php
        if($total_pages > 0){
            ?>
            <button type="button" class="btn-primary btn btn-sm copyAllHolidayQueryBtn">
                <span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span> Copy
            </button>
            <?php
        }
        ?>
        <a class="printBtn btn btn-sm btn-primary" href="<?php echo base_url() . 'staffHoliday?isPrint=1'; ?>" data-page="<?php echo $page; ?>" target="_blank">
            <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
            Print
        </a>
    </div>
    <?php
echo form_close();
?>
<div class="modal fade" id="holidayModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Holiday</h4>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-sm btn-primary saveHolidayBtn">Save</button>
                <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Exit</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="printModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Print</h4>
            </div>
            <div class="modal-body center" style="text-align: center;">
                <div class="modal-body-delete-txt">This Page Only or Whole Year?</div>
                <div style="margin: 10px 0;">
                    <button type="button" class="btn btn-sm btn-danger printPageBtn" data-dismiss="modal">Page</button>
                    <button type="button" class="btn btn-sm btn-primary printAllBtn" data-dismiss="modal">All</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade copyAllModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Copy All Holiday</h4>
            </div>
            <div class="modal-body center" style="text-align: center;">
                <div class="modal-body-delete-txt">
                    <?php
                    echo form_dropdown('copyYear', $yearAdvance, '', 'class="copyYear form-control input-sm"');
                    ?>
                </div>
                <div style="margin: 10px 0;">
                    <button type="button" class="btn btn-sm btn-danger copyAllHolidayBtn">Copy</button>
                    <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if(count($holidays) > 0){
    echo '<div class="panelArea">';
    foreach($holidays as $v){
        ?>
        <div class="row panelDiv" style="padding-right: 10px;">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                    <?php
                    echo $v->holiday;
                    ?>
                    <div class="iconArea">
                        <?php
                        echo '<span class="label label-success">' . date('D j/n/Y', strtotime($v->date)) . '</span>';
                        echo '<span class="glyphicon glyphicon-duplicate copyHolidayBtn icon-button" data-toggle="tooltip" data-placement="top" title="Copy" aria-hidden="true" id="' . $v->id . '"></span>';
                        echo '<span class="glyphicon glyphicon-cog editHolidayBtn icon-button" data-toggle="tooltip" data-placement="top" title="Edit" aria-hidden="true" id="' . $v->id . '"></span>';
                        echo '<span class="glyphicon glyphicon-trash deleteHolidayBtn icon-button" data-toggle="tooltip" data-placement="top" title="Delete" aria-hidden="true" id="' . $v->id . '"></span>';
                        echo '<span class="glyphicon glyphicon-chevron-' . ($holidayFilter->isExpandAll ? 'down' : 'up') . ' collapseBtn icon-button" id="' . $v->id . '" data-toggle="collapse" data-target="#issueBody' . $v->id . '"></span>';
                        ?>
                    </div>
                </div>
                <div class="panel-body issueBody collapse<?php echo $holidayFilter->isExpandAll ? "" : " in"; ?>" id="issueBody<?php echo $v->id; ?>">
                    <table class="table">
                        <tr>
                            <td class="col-md-1">
                                <strong>Date:</strong>
                            </td>
                            <td class="col-md-10">
                                <?php
                                echo date('d F Y', strtotime($v->date));
                                if($v->date_to){
                                    echo '<div class="pull-right"><strong>To:</strong> ' . date('d F Y', strtotime($v->date_to)) . '</div>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Type:</strong>
                            </td>
                            <td>
                                <?php
                                echo $v->type;
                                ?>
                            </td>
                        </tr>

                        <tr style="vertical-align: top;">
                            <td>
                                <strong>Description:</strong>
                            </td>
                            <td>
                                <?php
                                echo $v->description;
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php
    }
    echo '</div>';
    ?>
    <div class="modal fade deleteModal">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Delete Holiday</h4>
                </div>
                <div class="modal-body center" style="text-align: center;">
                    <div class="modal-body-delete-txt">Are you sure you want to remove this Holiday?</div>
                    <div style="margin: 10px 0;">
                        <button type="button" class="btn btn-sm btn-danger yesDeleteBtn" data-dismiss="modal">Yes</button>
                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">No</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
else{
    if(@$isFilter){
        ?>
        <div class="panel panel-primary">
            <div class="panel-heading">No Result</div>
            <div class="panel-body">
                Cannot find any Holiday related to the Filter.
            </div>
        </div>
        <?php
    }
    else{
        ?>
        <div class="panel panel-primary">
            <div class="panel-heading">No Holiday Submitted</div>
            <div class="panel-body">
                You haven't submitted any Holiday yet.
            </div>
        </div>
    <?php
    }
}

if($total_pages > 1){
    $prev = $page - 1;
    $next = $page + 1;
    ?>
    <nav class="pull-right">
        <ul class="pagination pagination-sm">
            <li<?php echo $prev > 0 ? '' : ' class="disabled"'; ?>>
                <a href="<?php echo $prev > 0 ? (base_url() . 'staffHoliday?p=' . $prev) : '#'; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
            <?php
            for($i = 1;$i <= $total_pages; $i++){
                echo  '<li' . ($page == $i ? ' class="disabled"' : '') . '><a href="' . ($page == $i ? '#' : (base_url() . 'staffHoliday?p=' . $i)) . '">' . $i . '</a></li>';
            }
            ?>
            <li<?php echo $next <= $total_pages ? '' : ' class="disabled"'; ?>>
                <a href="<?php echo $next <= $total_pages ? (base_url() . 'staffHoliday?p=' . $next) : '#'; ?>" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>
    <br style="clear: both;" />
<?php
}
?>
<script src="<?php echo base_url() . "plugins/js/jquery.growl.js"; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . "plugins/css/jquery.growl.css"; ?>" />
<script src="<?php echo base_url() . "plugins/js/bootstrap3-typeahead.js"; ?>"></script>
<link rel="stylesheet" href="<?php echo base_url() . "plugins/css/bootstrap-select.css"; ?>" />
<script src="<?php echo base_url() . "plugins/js/bootstrap-select.js"; ?>"></script>
<style>
    .filterArea{
        margin-bottom: 10px;
    }
    textarea{
        resize: none;
    }
    .panel{
        margin-bottom: 5px;
    }
    .panel-body{
        font-size: 12px;
    }
    .row{
        margin-bottom: 5px!important;
    }
</style>
<script>
    (function ($) {
        $('[data-toggle="tooltip"]').tooltip();

        //region type head
        $('.holiday').typeahead({
            minLength: 2,
            source: <?php echo $title_json ? html_entity_decode($title_json) : '[]'; ?>
        });
        //endregion

        var addHolidayBtn = $('.addHolidayBtn');
        var copyAllHolidayQueryBtn = $('.copyAllHolidayQueryBtn');
        var copyAllHolidayBtn = $('.copyAllHolidayBtn');
        var copyAllModal = $('.copyAllModal');
        var copyHolidayBtn = $('.copyHolidayBtn');
        var printBtn = $('.printBtn');
        var editHolidayBtn = $('.editHolidayBtn');
        var deleteHolidayBtn = $('.deleteHolidayBtn');
        var holidayModal = $('#holidayModal');
        var saveHolidayBtn = $('.saveHolidayBtn');
        var deleteModal = $('.deleteModal');
        var yesDeleteBtn = $('.yesDeleteBtn');

        //region Collapse Expand Area
        var collapseAllBtn = $('.collapseAllBtn');
        var collapseBtn = $('.collapseBtn');
        var isIssueCollapseBtn = 0;
        collapseAllBtn.click(function(e){
            isIssueCollapseBtn = 0;
            isCollapseAll();
        });
        collapseBtn.click(function(e){
            isIssueCollapseBtn = 1;
        });
        $('.issueBody')
            .stop()
            .on('shown.bs.collapse', function () {
                $('.collapseBtn[data-target="#' + (this.id) + '"]')
                    .removeClass('glyphicon-chevron-down')
                    .addClass('glyphicon-chevron-up');
                var ibi = $('.issueBody.in').length;
                if(isIssueCollapseBtn && ibi == $('.issueBody').length){
                    collapseAllBtn.find('.txt').html('Collapse All');
                    collapseAllBtn
                        .find('.glyphicon')
                        .removeClass('glyphicon-chevron-down')
                        .addClass('glyphicon-chevron-up');
                    collapseAllBtn.find('input[name="isExpandAll"]').val("0");
                }
            })
            .on('hidden.bs.collapse', function () {
                $('.collapseBtn[data-target="#' + (this.id) + '"]')
                    .removeClass('glyphicon-chevron-up')
                    .addClass('glyphicon-chevron-down');
                if(isIssueCollapseBtn && $('.issueBody.in').length == 0){
                    collapseAllBtn.find('.txt').html('Expand All');
                    collapseAllBtn
                        .find('.glyphicon')
                        .removeClass('glyphicon-chevron-up')
                        .addClass('glyphicon-chevron-down');
                    collapseAllBtn.find('input[name="isExpandAll"]').val("1");
                }
            });

        function isCollapseAll(){
            if(collapseAllBtn.find('.glyphicon').hasClass('glyphicon-chevron-up')){
                $('.issueBody').collapse('hide');
                collapseAllBtn.find('.txt').html('Expand All');
                collapseAllBtn
                    .find('.glyphicon')
                    .removeClass('glyphicon-chevron-up')
                    .addClass('glyphicon-chevron-down');
                collapseAllBtn.find('input[name="isExpandAll"]').val("1");
            }
            else{
                $('.issueBody').collapse('show');
                collapseAllBtn.find('.txt').html('Collapse All');
                collapseAllBtn
                    .find('.glyphicon')
                    .removeClass('glyphicon-chevron-down')
                    .addClass('glyphicon-chevron-up');
                collapseAllBtn.find('input[name="isExpandAll"]').val("0");
            }
        }
        //endregion

        var holidayUrl = '';
        var holidayTitle = '';
        addHolidayBtn.click(function(e){
            holidayUrl = bu + 'staffHolidayAdd';
            holidayTitle = 'Holiday Add';
            holidayModal.modal('show');
        });
        copyAllHolidayQueryBtn.click(function(e){
            copyAllModal.modal('show');
        });
        copyAllHolidayBtn.click(function(e){
            var copyYear = $('.copyYear').val();
            var copyIsClick = 1;
            if(copyYear){
                copyAllModal
                    .modal('hide')
                    .on('hidden.bs.modal', function (e) {
                        if(copyIsClick){
                            holidayUrl = bu + 'staffHolidayCopy?year=' + copyYear;
                            holidayTitle = 'Holiday All Copy';
                            holidayModal.modal('show');
                            copyIsClick = 0;
                        }
                    });
            }
        });
        copyHolidayBtn.click(function(e){
            var thisId = this.id;
            holidayUrl = bu + 'staffHolidayCopy?id=' + thisId;
            holidayTitle = 'Holiday Copy';
            holidayModal.modal('show');
        });

        var printUrl = printBtn.attr('href');
        var printPage = printBtn.data('page');
        printBtn.click(function(e){
            e.preventDefault();
            $('#printModal').modal('show');
        });
        $('.printPageBtn').click(function(e){
            printUrl += '&p=' + printPage;
            window.open(printUrl);
        });
        $('.printAllBtn').click(function(e){
            window.open(printUrl);
        });
        editHolidayBtn
            .stop()
            .on('click', function(e){
                e.stopPropagation();
                var thisId = parseInt(this.id);
                holidayUrl = bu + 'staffHolidayEdit/' + thisId;
                holidayTitle = 'Holiday Edit';
                holidayModal.modal('show');
            });
        holidayModal
            .on('show.bs.modal', function (e) {
                saveHolidayBtn.removeAttr('disabled');
                holidayModal.find('.modal-title').html(holidayTitle);
                holidayModal.find('.modal-body').load(holidayUrl, function(e){
                    var d = $('.date');
                    var dt = $('.date_to');
                    d
                        .datetimepicker({
                            format: 'DD/MM/YYYY',
                            pickTime: false
                        })
                        .on("dp.change",function (e) {
                            dt.data("DateTimePicker").setMinDate(e.date);
                        });
                    dt
                        .datetimepicker({
                            format: 'DD/MM/YYYY',
                            pickTime: false
                        })
                        .on("dp.change",function (e) {
                            if(e.date){
                                d.data("DateTimePicker").setMaxDate(e.date);
                            }
                            else{

                            }
                        });
                });
                saveHolidayBtn
                    .stop()
                    .on('click', function(e){
                        var hasEmpty = false;
                        $('.required').each(function(e){
                            if(!$(this).val()){
                                hasEmpty = true;
                                $(this).css({
                                    border: '1px solid #F00'
                                });
                            }
                            else{
                                $(this).css({
                                    border: '1px solid #CCC'
                                });
                            }
                        });

                        if(!hasEmpty){
                            $('.holidayForm').submit();
                        }
                    });
            })
            .on('hidden.bs.modal', function (e) {
                holidayModal.find('.modal-title').html('');
                holidayModal.find('.modal-body').html('');
            });

        addPanelCell();
        function addPanelCell(){
            var panelDiv = $('.panelDiv');
            panelDiv.unwrap();
            var limit = 10;
            for(var i = 0; i < panelDiv.length; i+=limit) {
                panelDiv.slice(i, i+limit).wrapAll('<div class="col-sm-6 panelCell" style="display: table-cell;"></div>');
            }
        }
        deleteHolidayBtn.click(function(e){
            e.stopPropagation();
            var parentPanel = $(this).parent('').parent('').parent('').parent('');
            var thisId = parseInt(this.id);
            deleteModal.modal('show');
            yesDeleteBtn
                .stop()
                .on('click', function(e){
                    $.post(
                        bu + 'staffHolidayDelete',
                        {
                            id: thisId
                        },
                        function(r){
                            if(r == 1){
                                $.growl.notice({ message: 'Successfully Remove!' });
                                parentPanel.remove();
                                addPanelCell();
                            }
                            else{
                                $.growl.error({ message: 'Failed to Remove!' });
                            }
                        }
                    );
                });
        });
    })($);
    //$.noConflict(true);
</script>