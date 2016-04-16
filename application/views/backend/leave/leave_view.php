<script>
    var fDp, sDp, sDefault;
    $(function(e){
        fDp = $('.fDp');
        sDp = $('.sDp');
        var $sDp = <?php echo count($staff) > 0 ? json_encode($staff) : '[]'; ?>;
        fDp.selectCountry({
            cityName: 'user_id',
            cityId: 'sDp',
            cityClass: 'sDp',
            city: $sDp,
            appendWhere: $('.userArea'),
            callBack: function(e){
                sDp = $('.sDp');
                sDp
                    .live('change', function(e) {
                        sDefault = $('.sDp').val();
                        setFilterArgs();
                    });
            }
        });
        fDp
            .stop()
            .on('change', function(e){
                $('.userArea').html('');
                sDefault = "";
                setFilterArgs();
            });
    });
</script>
<style>
    .leaveGrid{
        margin: 10px 0;
        height: 400px;
        border: 1px solid #000000;
        font-size: 12px!important;
    }
    .slick-row{
        font-size: 12px!important;
    }
    .slick-cell{
        font-size: 12px!important;
        cursor: pointer;
        text-align: center;
    }
    .slick-header-column{
        padding:5px 10px!important;
        color:#FFF!important;
        text-align: center!important;
        font-size: 12px!important;
    }
    .slick-row:hover {
        background: #44a7cc!important;
    }
    .slick-row.active{
        background: #ff8f47!important;
    }
    .column-left, .reason{
        text-align: left;
    }

    .is_approved{
        background: #45ff96!important;
    }
    .is_disapproved{
        background: #ff5951!important;
    }

    .is_cancelled{
        color: #ffffff!important;
        background: #696d6d !important;
    }

    .licenseBtnArea{
        white-space: nowrap;
        padding: 5px;
    }
    .fa{
        cursor: pointer;
    }

    .slickTitle{
        font-family: "Arial", sans-serif!important;
        background: #000000;
        padding: 10px;
        z-index: 999;
        font-size: 12px;
        position: absolute;
        color: #ffffff;
        border-radius: 5px;
    }
    .slickTitle table{
        width: 100%;
        border-collapse: collapse;
    }
    .slickTitle table tr td{
        border: 1px solid #ffffff;
        padding: 3px 5px;
    }
    .slickTitle table .headerTr td{
        background: #ffffff;
        color: #000000;
    }

    .tableForms{
        width: 100%;
    }
    .tableForms tr td{
        padding: 0 2px;
    }

    /*to fix bootstrap problem*/
    .leaveGrid, .leaveGrid div {
        -webkit-box-sizing: content-box;
        -moz-box-sizing: content-box;
        box-sizing: content-box;
    }
    .decision{
        text-align: left!important;
    }
</style>
<div class="form-horizontal">
    <div class="row">
        <div class="col-sm-8">
            <div class="form-group">
                <div class="col-sm-3">
                    <?php
                    echo form_dropdown('user_id', $staff, '', 'class="sDp form-control input-sm"');
                    ?>
                </div>
                <div class="col-sm-3">
                    <?php
                    echo form_dropdown('type', $type, '', 'class="type form-control input-sm"');
                    ?>
                </div>
                <div class="col-sm-2">
                    <?php
                    echo form_dropdown('status', $decision, '', 'class="status form-control input-sm"');
                    ?>
                </div>
                <div class="col-sm-1">
                    <a href="<?php echo base_url() . "staffLeaveAdd"; ?>" class="addLeave" style="font-size: 20px;">
                        <i class="fa fa-plus tooltip-class" title="Request Leave"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <?php
            if(in_array($account_type, array(1,2,4))) {
                ?>
                <div class="pull-right">
                    <a href="<?php echo base_url() . "leaveAuditLog"; ?>">
                        <i class="fa fa-list" title="Logs"></i>
                    </a>
                    <a href="<?php echo base_url() . "leaveEmailLog"; ?>">
                        <i class="fa fa-envelope" title="Email Logs"></i>
                    </a>
                    <i class="optionBtn fa fa-cog" data-toggle="modal" data-target="#optionModal" title="Option"></i>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<div class="leaveGrid grid"></div>
<?php
if(in_array($account_type, array(1,2,4))) {
    ?>
    <div class="modal fade" id="optionModal">
        <div class="modal-dialog modal-default">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Leave Option</h4>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
<?php
}
?>
<script>
    function buttonFormatter(row,cell,value,columnDef,dataContext){
        var btn = '<a href="#" class="tooltip-class" title="Edit Leave"><i class="fa fa-pencil" id="' + dataContext.id + '"></i></a>';
        return btn;
    }
    function htmlFormatter(row, cell, value, columnDef, dataContext) {
        return value;
    }

    var leaveGrid, leaveData = [], leaveDataView,
        leaveGridColumns = [
            <?php
            echo in_array($account_type, array(1,2,4)) ?
                '{id: "button", name: "&nbsp;", field: "button", width: 10, formatter: buttonFormatter,cssClass:"text-center"},' : '';
            ?>
            {id: "date", name: "Date", field: "date", width: 60,cssClass:"text-center"},
            {id: "user", name: "Name", field: "user", width: 90, cssClass:"text-center"},
            {id: "leave_type", name: "Type", field: "type", width: 60, cssClass:"text-center"},
            {id: "reason", name: "Reason", field: "reason", width: 100, cssClass: "reason", formatter: htmlFormatter},
            {id: "range_date", name: "Range", field: "range_date", width: 150,cssClass:"text-center range"},
            {id: "duration", name: "Duration", field: "duration", width: 50,cssClass:"text-center"},
            {id: "actual_leave", name: "AL", field: "actual_leave", width: 50,cssClass:"text-center"},
            {id: "status", name: "Status", field: "status", width: 50,cssClass:"text-center"},
            {id: "decision", name: "Decision", field: "reason_decision", width: 100,cssClass:"decision"},
            {id: "actioned_by", name: "Actioned By", field: "actioned_by", width: 80}
        ],
        leaveGridOptions = {
            enableCellNavigation: true,
            enableColumnReorder: false,
            multiColumnSort: true,
            forceFitColumns: true
        },
        licenseCurrentRow,
        leaveGridActiveId = "",
        filterContainsAll, filterMatchAll, filterContainsAny;
    var setFilterArgs, thisType;
    var limit = 10;
    var active_ids = [];

    $(function(e){
        var type = $('.type');
        var status = $('.status');
        var sDp = $('.sDp');
        var addLeave = $('.addLeave');
        leaveData = <?php echo $leave ? $leave : '[]'; ?>;

        leaveGridLoad();

        function leaveGridLoad(){
            leaveDataView = new Slick.Data.DataView({ inlineFilters: true });
            leaveGrid = new Slick.Grid(".leaveGrid", leaveDataView, leaveGridColumns, leaveGridOptions);
            //region Filter
            filterContainsAll = function(val, search) {
                if(val){
                    return val.indexOf(search) !== -1;
                }
                else{
                    return false;
                }
            };
            filterMatchAll = function(val, search) {
                var matchCount = 0;
                for (var i = search.length - 1; i >= 0; i--) {
                    if (val.indexOf(search[i]) > -1) {
                        matchCount += 1;
                    }
                }

                return (matchCount == search.length);
            };
            filterContainsAny = function(val, search) {
                for (var i = search.length - 1; i >= 0; i--) {
                    if (val.indexOf(search[i]) > -1) {
                        return true;
                    }
                }

                return false;
            };
            setFilterArgs = function() {
                var filterTextSplitFn = function(val) {
                        var thisVal = val.toLowerCase();
                        return $.unique($.grep(thisVal.split(' '), function(v) { return v !== ''; }));
                    };

                $('.slick-cell').removeClass('selected');
                leaveGrid.resetActiveCell();
                var filterData = {
                    dp: sDp.val(),
                    type: type.val(),
                    status: status.val()
                };
                leaveDataView.setFilterArgs(filterData);
                leaveDataView.refresh();
            };
            function myFilter(item, args) {
                var match = true;
                match = args.dp ? item.user_id == args.dp : match;
                match = match && args.type ? item.type_id == args.type : match;
                match = match && args.status ? item.status_id == args.status : match;

                return match;
            }
            sDp.change(function(e) {
                setFilterArgs();
            });
            type.change(function(e) {
                setFilterArgs();
            });
            status.change(function(e) {
                setFilterArgs();
            });
            //endregion

            //start
            // wire up model events to drive the grid
            leaveDataView.onRowCountChanged.subscribe(function (e, args) {
                leaveGrid.updateRowCount();
                leaveGrid.render();
            });

            leaveDataView.onRowsChanged.subscribe(function (e, args) {
                leaveDataView.getItemMetadata = function (row) {
                    var item = leaveDataView.getItem(row);
                    var this_status = "";
                    switch(item.status_id){
                        case 1:
                            this_status = "is_approved";
                            break;
                        case 2:
                            this_status = "is_disapproved";
                            break;
                        case 3:
                            this_status = "is_cancelled";
                            break;
                    }

                    if(this_status){
                        return {
                            'cssClasses': this_status
                        };
                    }

                };

                leaveGrid.invalidateRows(args.rows);
                leaveGrid.render();
            });
            //end

            leaveDataView.beginUpdate();
            leaveDataView.setFilter(myFilter);
            setFilterArgs(1);
            leaveDataView.setItems(leaveData);
            leaveDataView.endUpdate();

            $('.fa-pencil')
                .die()
                .live('click', function(e){
                    e.stopPropagation();
                    var thisId = parseInt(this.id);
                    location.replace(bu + 'staffLeaveEdit/' + thisId);
                });

            var slickTitle = $('.leaveGrid.slickTitle');
            var thisTitle = "";
            var hoverEle = '<span class="slickTitle"></span>';
            var slickCell = $('.slick-cell');
            slickCell
                .live({
                    mouseenter: function(e) {
                        var thisId = $(this).parent('').parent('').parent('').parent('').attr('id');

                        var thisTitle = $(this).html();

                        if(!$(this).parent('').hasClass('no_hover') && ($(this).hasClass('reason') || $(this).hasClass('decision'))){
                            $('body').after(hoverEle);
                            if(thisTitle){
                                slickTitle = $('.slickTitle');
                                slickTitle.html(thisTitle);
                            }

                            var thisTop = $(this).offset().top + $(this).innerHeight();
                            var thisLeft = parseFloat($(this).offset().left);

                            if($(this).hasClass('reason')){
                                slickTitle.css({
                                    top: thisTop + 'px',
                                    left: thisLeft + "px"
                                });
                            }
                            else if($(this).hasClass('decision')){
                                if(thisTitle){
                                    slickTitle.css({
                                        top: thisTop + 'px',
                                        left: thisLeft + "px"
                                    });
                                }
                            }
                        }
                    },
                    mouseleave: function(e) {
                        if(slickTitle.length != 0){
                            slickTitle.remove();
                        }
                    }
                });
        }

        addLeave.click(function (e) {
            e.preventDefault();
            var thisUrl = bu + 'staffLeaveAdd';
            thisUrl += (fDp.val() ? '?fId=' + fDp.val() : '');
            thisUrl += (sDefault ? (fDp.val() ? '&' : '?') + 'staff=' + sDefault : '');
            location.replace(thisUrl);
        });

        var optionModal = $('#optionModal');
        optionModal
            .on('show.bs.modal', function (e) {
                var thisUrl = bu + 'leaveEmailOption';
                optionModal
                    .find('.modal-body')
                    .load(thisUrl, function(e){

                    });
            })
            .on('hidden.bs.modal', function (e) {
                optionModal.find('.modal-body').html('');
            });
    });
</script>