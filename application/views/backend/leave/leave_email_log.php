<table class="ourTable">
    <tr style="vertical-align: top;">
        <td>
            <div class="ourGrid grid"></div>
        </td>
        <td style="width: 364px;">
            <table class="filterArea">
                <tr>
                    <td>Filter:</td>
                    <td>
                        <input type="text" name="filter" class="filter" style="width: 200px;padding: 3px 5px;" />
                        <input type="checkbox" name="exact" class="exact" value="1" />Exact
                    </td>
                </tr>
                <tr>
                    <td>Excluding:</td>
                    <td style="white-space: nowrap!important;">
                        <input type="text" name="exclude" class="exclude" style="width: 200px;padding: 3px 5px;" />
                        <a href="<?php echo base_url() . 'staffLeave'; ?>" class="pure_black btn btn-sm btn-primary">Back</a>
                    </td>
                </tr>
            </table>
            <hr style="width: 100%;"/>

            <div id="ourDetail">
                <div class="ourHeader">
                    Email Log
                    <a href="#" class="closeBtn">x</a>
                </div>
                <div class="ourForm">
                    <table class="ourView">
                        <tr>
                            <td>Date:</td>
                            <td class="dateView"></td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td class="statusView"></td>
                        </tr>
                        <tr>
                            <td>Staff:</td>
                            <td class="requestView"></td>
                        </tr>
                        <tr>
                            <td>User:</td>
                            <td class="userView"></td>
                        </tr>

                        <tr>
                            <td colspan="2"><br /></td>
                        </tr>

                        <tr>
                            <td colspan="2" style="text-align: left!important;">
                                Information
                            </td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>To:</td>
                            <td class="toView"></td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Subject:</td>
                            <td class="subjectView"></td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>Message:</td>
                            <td class="messageView"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
    </tr>
</table>

<style>
    .ourTable{
        font-size: 12px;
        border-collapse: collapse;
    }
    .ourTable>tbody>tr>td{
        padding-right: 10px;
    }
    .ourGrid{
        width: 700px;
        height: 580px;
        border: 1px solid #000000;
        font-size: 11px!important;
    }

    .filterArea{
        border-collapse: collapse;
        font-size: 12px;
        margin-left: 10px;
    }
    .filterArea tr td:first-child{
        font-weight: bold;
    }
    .filter, .exclude{
        padding: 5px 8px!important;
    }

    .slick-header-column{
        padding:5px 10px!important;
        color:#FFF!important;
        text-align: center!important;
        font-size: 12px!important;
    }
    .slick-cell{
        cursor: pointer;
    }
    .slick-row:hover {
        background: #44a7cc!important;
    }
    .slick-row.active{
        background: #ff8f47!important;
    }
    .column-empid, .column-status{
        text-align: center;
    }

    #ourDetail{
        display: none;
        width: 100%;
        border: 1px solid #000000;
    }
    .ourHeader{
        background: #000000;
        color: #ffffff;
        padding: 5px 10px;
    }
    .ourForm{
        width: 100%;
        padding: 3px 5px;
    }
    .ourView{
        width: 100%;
        font-size: 12px;
        border-collapse: collapse;
    }
    .ourView tr td{
        text-align: left;
        padding: 5px 10px;
    }
    .ourView>tbody>tr>td:first-child{
        font-weight: bold;
        width: 40px;
        white-space: nowrap;
        text-align: right;
    }

    input[type=text], textarea{
        padding: 5px 8px;
        width: 260px;
    }

    .closeBtn{
        float: right;
        color: #ffffff;
    }

    .exportSettingTable{
        font-size: 10px;
        white-space: nowrap;
    }
    .exportSettingTable tr td:first-child{

    }
</style>
<script>
    var ourGrid, dataView;
    var ourColumns = [
        {id: "date", name: "Date", field: "date", width: 30, cssClass: "column-empid"},
        {id: "staff", name: "Staff", field: "staff", width: 80, cssClass: "column-empid"},
        {id: "user", name: "User", field: "user", width: 80, cssClass: "column-empid"},
        {id: "status", name: "Status", field: "status", width: 20, cssClass: "column-status"}
    ];

    var ourOptions = {
        enableCellNavigation: true,
        enableColumnReorder: true,
        forceFitColumns: true
    };
    var ourActiveId = "",
        $includes, $excludes,
        filterContainsAll, filterContainsAny;

    $(function () {
        var log = <?php echo $log ? $log : '[]'; ?>;
        var ourDetail = $('#ourDetail');
        var closeBtn = $('.closeBtn');

        dataView = new Slick.Data.DataView({ inlineFilters: true });
        ourGrid = new Slick.Grid(".ourGrid", dataView, ourColumns, ourOptions);

        //region Filter Area
        $includes = $('.filter');
        $excludes = $('.exclude');
        var lastIncludes = $includes.val(),
            lastExcludes = $excludes.val();
        //start
        $('.filter, .exclude')
            .stop()
            .on('propertychange keyup input paste', function(e) {
                // clear on Esc
                if (e.which == 27) {
                    $includes.val('');
                    $excludes.val('');
                }

                ourGrid.resetActiveCell();
                $('.slick-cell').removeClass('selected');

                if ($includes.val() !== lastIncludes ||
                    $excludes.val() !== lastExcludes){
                    setFilterArgs();
                }
            });

        filterContainsAll = function(val, search) {
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

        var setFilterArgs = function() {
            var filterTextSplitFn = function(val) {
                    var thisVal = $('.exact').is(':checked') ? val : val.toLowerCase();
                    return $.unique($.grep(thisVal.split(' '), function(v) { return v !== ''; }));
                },
                includesVal = $includes.val(),
                excludesVal = $excludes.val(),
                includes = filterTextSplitFn(includesVal),
                excludes = filterTextSplitFn(excludesVal);

            dataView.setFilterArgs({
                includes: includes,
                excludes: excludes
            });
            dataView.refresh();

            lastIncludes = includesVal;
            lastExcludes = excludesVal;
        };

        var filterFn = function(item, args) {
            var isExact = $('.exact').is(':checked');
            var match = false;
            match = (
                (filterContainsAll(isExact ? item.user : (item.user ? item.user.toLowerCase() : item.user), args.includes))
            );
            if (!match) return false;

            match = !(
                (filterContainsAny(isExact ? item.user : (item.user ? item.user.toLowerCase() : item.user), args.excludes))
            );

            return match;
        };
        //endregion

        dataView.onRowCountChanged.subscribe(function (e, args) {
            ourGrid.updateRowCount();
            ourGrid.render();
        });

        dataView.onRowsChanged.subscribe(function (e, args) {
            ourGrid.invalidateRows(args.rows);
            ourGrid.render();
        });

        ourGrid.onClick.subscribe(function(e, args) {
            var currentRow = args.row;
            var thisData = dataView.getItem(currentRow);

            ourDetail.css({
                display: 'inherit'
            });

            ourDetail.find('.dateView').html(thisData.date);
            ourDetail.find('.statusView').html(thisData.status);
            ourDetail.find('.userView').html(thisData.user);
            ourDetail.find('.requestView').html(thisData.staff);
            ourDetail.find('.toView').html(" &lt;" + thisData.message.to + '&gt;');
            ourDetail.find('.subjectView').html(thisData.message.subject);

            var thisDisplay = 'none';
            if(thisData.message.body){
                thisDisplay = 'table-row';
                ourDetail.find('.messageView').html(thisData.message.body);
            }
            ourDetail.find('.messageView').parent().css({ display: thisDisplay});
        });

        closeBtn.click(function(e){
            e.preventDefault();

            ourDetail.css({
                display: 'none'
            });
        });

        dataView.beginUpdate();
        dataView.setFilter(filterFn);
        setFilterArgs();
        dataView.setItems(log);
        dataView.endUpdate();
        dataView.refresh();
    });
</script>