<style>
    .auditGrid{
        height: 500px;
        border: 1px solid #000000;
        font-size: 11px!important;
    }

    .slick-row, .alt_drop_div{
        font-size: 12px!important;
    }

    .slick-cell{
        font-size: 11px!important;
        padding: 5px!important;
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
    .slick-cell.description{
        text-align: left;
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
    select, input[type=text]{
        padding: 5px 8px;
    }
    .filterArea{
        font-size: 12px;
        width: 100%;
    }
</style>

<table class="filterArea">
    <tr>
        <td>
            <?php
            echo form_dropdown('type', $log_type, '', 'class="type form-control input-sm" style="width: 120px;"');
            ?>
        </td>
        <td>
            <input type="text" name="name" class="name form-control input-sm" placeholder="User" style="width: 150px;" />
        </td>
        <td>
            <table>
                <tr>
                    <td>
                        <span id="dateStartTxt">Start&nbsp;</span>
                    </td>
                    <td>
                        <input type="hidden" name="dateRangeStart" class="dateRangeStart" />
                    </td>
                    <td style="padding: 0 5px;">to</td>
                    <td>
                        <span id="dateEndTxt">End&nbsp;</span>
                    </td>
                    <td>
                        <input type="hidden" name="dateRangeEnd" class="dateRangeEnd" />
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td>
                        <input type="text" name="changes" class="changes form-control input-sm" placeholder="Changes" style="width: 180px;" />
                    </td>
                    <td style="width: 10px;padding: 0 5px;">
                        <input type="checkbox" name="anyChanges" class="anyChanges" />
                    </td>
                    <td>Any</td>
                </tr>
            </table>
        </td>
        <td style="white-space: nowrap;">
            <input type="button" name="clear" value="Clear" class="clearBtn pure_black btn btn-sm btn-danger" />
            <input type="button" name="print" value="Print" class="printBtn pure_black btn btn-sm btn-primary" />
        </td>
        <td style="width: 300px;text-align: right;">
            <a href="<?php echo base_url() . 'staffLeave'; ?>" class="pure_black btn btn-sm btn-primary"><i class="glyphicon glyphicon-arrow-left"></i> Back</a>
        </td>
    </tr>
</table><br/>
<div class="auditGrid grid"></div>

<script language="JavaScript">
    function formatter(row, cell, value, columnDef, dataContext) {
        return value;
    }

    //region Variables
    var auditGrid, auditGridJson = [], auditDataView,
        auditGridSortCol = "date",
        auditGridSort = true,
        auditGridColumns = [
            {id: "type", name: "Type", field: "type", width: 30, sortable: true},
            {id: "date", name: "Date", field: "date", width: 100, sortable: true},
            {id: "name", name: "User", field: "name", width: 80, sortable: true},
            {id: "changes", name: "Changes", field: "changes", width: 600, cssClass: "description", sortable: true, formatter: formatter}
        ],
        auditGridOptions = {
            enableCellNavigation: true,
            enableColumnReorder: true,
            multiColumnSort: true,
            forceFitColumns: true
        },
        auditGridActiveId = "",
        auditCurrentRow,
        filterContainsAll, filterContainsAllArray, filterContainsAny,
        date_start, date_end;
    //endregion

    $(function(e){
        auditGridJson = <?php echo $log ? $log : '[]'; ?>;

        auditDataView = new Slick.Data.DataView({ inlineFilters: true });
        auditGrid = new Slick.Grid(".auditGrid", auditDataView, auditGridColumns, auditGridOptions);
        auditGrid.setSortColumn(auditGridSortCol, auditGridSort);

        //region filter
        var type = $('.type');
        var name = $('.name');
        var changes = $('.changes');
        var anyChanges = $('.anyChanges');
        var dRS = $('.dateRangeStart');
        var dRE = $('.dateRangeEnd');
        var clearBtn = $('.clearBtn');
        var printBtn = $('.printBtn');

        filterContainsAll = function(val, search) {
            if(val){
                return val.indexOf(search) !== -1;
            }
            else{
                return false;
            }
        };
        filterContainsAllArray = function(val, search) {
            for (var i = search.length - 1; i >= 0; i--) {
                if (val.indexOf(search[i]) === -1) {
                    return false;
                }
            }

            return true;
        };
        filterContainsAny = function(val, search) {
            for (var i = search.length - 1; i >= 0; i--) {
                if (val.indexOf(search[i]) > -1) {
                    return true;
                }
            }

            return false;
        };
        var filterTextSplitFn = function(val) {
            var thisVal = val.toLowerCase();
            return $.unique($.grep(thisVal.split(' '), function(v) { return v !== ''; }));
        };
        var setFilterArgs = function() {
            var changesVal = filterTextSplitFn(changes.val());
            auditDataView.setFilterArgs({
                type: type.val(),
                name: name.val(),
                date_start: date_start,
                date_end: date_end,
                changes: changesVal,
                any: anyChanges.is(':checked')
            });
            auditDataView.refresh();
        };
        function myFilter(item, args) {
            var match = item.type == args.type;
            if(args.type == ""){
                match = true;
            }
            if(match){
                if(args.user != ""){
                    match = filterContainsAll(item.name.toLowerCase(), args.name.toLowerCase());
                }
            }
            if(match){
                if(args.changes != ""){
                    match = args.any ?
                        filterContainsAny(item.changes.toLowerCase(), args.changes) :
                        filterContainsAllArray(item.changes.toLowerCase(), args.changes);
                }
            }
            if(match){
                if(args.date_start && args.date_end){
                    match = args.date_start <= item.date_time && args.date_end >= item.date_time;
                }
            }

            return match;
        }
        $('.name, .code, .changes')
            .stop()
            .on('propertychange keyup input paste', function(e) {
                // clear on Esc
                if (e.which == 27) {
                    $(this).val('');
                }

                setFilterArgs();
            });
        type.change(function(e){
            setFilterArgs();
        });

        dRS.datepicker({
            dateFormat: "dd/mm/yy",
            showOn: "button",
            buttonImage: bu + "images/calendar-add.png",
            buttonImageOnly: true,
            maxDate: dRE.val(),
            onSelect: function() {
                var date = $(this).val();
                $('#dateStartTxt').html(String(date));

                var option = "maxDate";

                var date2 = dRS.datepicker('getDate', '+1d');
                date2.setDate(date2.getDate()+1);

                $(this).datepicker("option", option, date);
                option = "minDate";
                dRE.datepicker("option", option, date);

                var s = date.split('/');
                var dateStart = new Date(s[2], s[1] - 1, s[0], 0, 0, 0);
                var end = dRE.val();
                var e = end.split('/');
                var dateEnd = new Date(e[2], e[1] - 1, e[0], 23, 59, 59);

                date_start = dateStart.getTime()/1000;
                date_end = dateEnd.getTime()/1000;
                if(dateStart.getTime() > dateEnd.getTime()){
                    dRE.val(date);
                    $('#dateEndTxt').html(String(date));
                }

                setFilterArgs();
            }
        });
        dRE.datepicker({
            dateFormat: "dd/mm/yy",
            showOn: "button",
            buttonImage: bu + "images/calendar-add.png",
            buttonImageOnly: true,
            minDate: dRS.val(),
            onSelect: function() {
                var date = $(this).val();
                $('#dateEndTxt').html(String(date));

                var option = "minDate";
                $(this).datepicker("option", option, date);

                option = "maxDate";
                dRS.datepicker("option", option, date);

                var s = dRS.val().split('/');
                var dateStart = new Date(s[2], s[1] - 1, s[0], 0, 0, 0);
                var end = dRE.val();
                var e = end.split('/');
                var dateEnd = new Date(e[2], e[1] - 1, e[0], 23, 59, 59);

                date_start = dateStart.getTime()/1000;
                date_end = dateEnd.getTime()/1000;

                setFilterArgs();
            }
        });
        anyChanges.click(function(e){
            setFilterArgs();
        });

        clearBtn.click(function(e){
            $('.type, .name, .changes, .dateRangeStart, .dateRangeEnd').val('');
            $('#dateStartTxt').html('Start');
            $('#dateEndTxt').html('End');

            date_start = "";
            date_end = "";

            setFilterArgs();
        });
        printBtn.click(function(e){
            var thisUrl = bu + 'leaveAuditLog?isPrint=1';
            if(type.val()){
                thisUrl += "&type=" + type.val();
            }
            if(name.val()){
                thisUrl += "&name=" + name.val();
            }
            if(changes.val()){
                thisUrl += "&changes=" + changes.val();
            }
            if(anyChanges.is(':checked')){
                thisUrl += "&any=1";
            }
            if(date_start && date_end){
                thisUrl += "&dateStart=" + date_start + "&dateEnd=" + date_end;
            }

            var myWindow = window.open(
                thisUrl,
                'PDF',
                'width=842,height=595;toolbar=no,menubar=no,location=no,titlebar=no'
            );
        });
        //endregion

        //region wire up model events to drive the grid
        auditDataView.onRowCountChanged.subscribe(function (e, args) {
            auditGrid.updateRowCount();
            auditGrid.render();
        });

        auditDataView.onRowsChanged.subscribe(function (e, args) {
            auditDataView.getItemMetadata = function (row) {
                if (auditDataView.getItem(row).no_hover) {
                    return {
                        'cssClasses': 'no_hover'
                    };
                }
            };

            auditGrid.invalidateRows(args.rows);
            auditGrid.render();
        });
        //endregion

        //region for sorting a column - start
        auditGrid.onSort.subscribe(function (e, args) {
            var col = args.sortCols;

            for (var i = 0, l = col.length; i < l; i++) {
                var field = col[i].sortCol.field;
                var sign = col[i].sortAsc ? 1 : -1;
                auditGridSort = col[i].sortAsc ? 1 : -1;
                auditGridSortCol = field;
                auditDataView.sort(compare, col[i].sortAsc);
            }
        });

        function compare(a, b) {
            var x = a[auditGridSortCol], y = b[auditGridSortCol];
            return (x == y ? 0 : (x > y ? 1 : -1));
        }
        //endregion

        auditDataView.beginUpdate();
        auditDataView.setFilter(myFilter);
        setFilterArgs();
        auditDataView.setItems(auditGridJson);
        auditDataView.endUpdate();
        //endregion

        var slickTitle = $('.auditGrid.slickTitle');
        var thisTitle = "";
        var hoverEle = '<span class="slickTitle"></span>';
        var slickCell = $('.slick-cell.description');
        slickCell
            .live({
                mouseenter: function(e) {
                    var thisId = $(this).parent('').parent('').parent('').parent('').attr('id');

                    var thisTitle = $(this).html();
                    if(!$(this).parent('').hasClass('no_hover')){
                        $('body').after(hoverEle);
                        slickTitle = $('.slickTitle');
                        slickTitle.html(thisTitle);

                        var thisTop = $(this).offset().top + $(this).innerHeight();
                        var thisLeft = parseFloat($(this).offset().left);
                        slickTitle.css({
                            top: thisTop + 'px',
                            left: thisLeft + "px"
                        });
                    }
                },
                mouseleave: function(e) {
                    if(slickTitle.length != 0){
                        slickTitle.remove();
                    }
                }
            });
    });
</script>