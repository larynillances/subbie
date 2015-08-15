<table class="ourTable">
    <tr style="vertical-align: top;">
        <td>
            <div class="ourGrid grid"></div>
        </td>
        <td style="width: 364px;">
            <table class="filterArea">
                <tr>
                    <td>Filter:</td>
                    <td style="padding: 3px 5px;">
                        <input type="text" name="filter" class="form-control input-sm filter" style="width: 200px;padding: 3px 5px;" />
                        <input type="checkbox" name="exact" class="exact" value="1" />Exact
                    </td>
                </tr>
                <tr>
                    <td>Excluding:</td>
                    <td style="padding: 3px 5px;">
                        <input type="text" name="exclude" class="exclude form-control input-sm" style="width: 200px;padding: 3px 5px;" />
                    </td>
                </tr>
                <tr>
                    <td>Email Type:</td>
                    <td style="padding: 3px 5px;">
                        <?php echo form_dropdown('email_type',$email_type,'','class="form-control input-sm email_type_class"')?>
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
                            <td>User:</td>
                            <td class="userView"></td>
                        </tr>
                        <tr>
                            <td>Invoice:</td>
                            <td class="jobView"></td>
                        </tr>

                        <tr>
                            <td colspan="2"><br /></td>
                        </tr>

                        <tr>
                            <td colspan="2" style="text-align: left!important;">
                                Take-off Information
                            </td>
                        </tr>
                        <tr>
                            <td>Client:</td>
                            <td class="toView"></td>
                        </tr>
                        <tr style="vertical-align: top;">
                            <td>CC:</td>
                            <td class="ccView"></td>
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

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>plugins/css/uploadify.css" />

<script language="javascript" src="<?php echo base_url();?>plugins/js/swfobject.js"></script>
<script language="javascript" src="<?php echo base_url();?>plugins/js/jquery.uploadify-3.1.js"></script>

<style>
    .ourTable{
        font-size: 12px;
        border-collapse: collapse;
    }
    .ourTable>tbody>tr>td{
        padding-right: 10px;
    }
    .ourGrid{
        width: 900px;
        height: 580px;
        border: 1px solid #000000;
        font-size: 12px!important;
    }

    .filterArea{
        border-collapse: collapse;
        font-size: 12px;
        margin-left: 10px;
    }
    .filterArea tr td:first-child{
        font-weight: bold;
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
        {id: "date", name: "Date", field: "date", width: 50, cssClass: "column-empid"},
        {id: "status", name: "Status", field: "status", width: 30, cssClass: "column-empid"},
        {id: "user", name: "User", field: "user", width: 50, cssClass: "column-empid"},
        {id: "email_type", name: "Email Type", field: "email_type", width: 35, cssClass: "column-empid"},
        {id: "job", name: "Invoice", field: "job", width: 80, cssClass: "column-status"},
        {id: "staff_name", name: "Staff", field: "staff_name", width: 80, cssClass: "column-status"},
        {id: "client_name", name: "Client", field: "client_name", width: 80, cssClass: "column-empid"}
    ];

    var ourOptions = {
        enableCellNavigation: true,
        enableColumnReorder: true,
        forceFitColumns: true
    };
    var ourActiveId = "",
        $includes, $excludes,$email_type,
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
            lastExcludes = $excludes.val(),
            email_type = '';
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

        $('.email_type_class').change(function(e){
            if (e.which == 27) {
                $email_type.val('');
            }
            ourGrid.resetActiveCell();
            $('.slick-cell').removeClass('selected');

            email_type = this.value;
            setFilterArgs();
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
                    var thisVal = $('.exact').is(':checked') ? val : val;
                    return $.unique($.grep(thisVal.split(' '), function(v) { return v !== ''; }));
                },
                includesVal = $includes.val(),
                excludesVal = $excludes.val(),
                includes = filterTextSplitFn(includesVal),
                excludes = filterTextSplitFn(excludesVal);

            dataView.setFilterArgs({
                includes: includes,
                email_type: email_type,
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
                (filterContainsAll(isExact ? item.user : item.user, args.includes)) ||
                (filterContainsAll(isExact ? item.job : item.job, args.includes)) ||
                (filterContainsAll(isExact ? item.branch : item.branch, args.includes))
            );
            if (!match) return false;

            match = !(
                (filterContainsAny(isExact ? item.user : item.user, args.excludes)) ||
                (filterContainsAny(isExact ? item.job : item.job, args.excludes)) ||
                (filterContainsAny(isExact ? item.branch : item.branch, args.excludes))
            );

            if (args.email_type != "" && item.email_type_id.indexOf(args.email_type) == -1) {
                return false;
            }


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
            ourDetail.find('.jobView').html(thisData.job);
            ourDetail.find('.branchView').html(thisData.branch);
            ourDetail.find('.toView').html(thisData.message.to_alias + " [" + thisData.message.to + ']');

            var cc = thisData.message.cc;
            var alias = thisData.message.cc_alias;
            var ccStr = "";
            $.each(cc, function(k, v){
                ccStr += alias[k] + " [" + v + ']<br />';
            });
            ourDetail.find('.ccView').html(ccStr);

            var thisDisplay = 'none';
            if(thisData.message.comment){
                thisDisplay = 'table-row';
                ourDetail.find('.messageView').html(thisData.message.comment);
            }
            ourDetail.find('.messageView').parent().css({ display: thisDisplay});

            thisDisplay = 'none';
            if(thisData.export_setting){
                thisDisplay = 'table-row';
                var thisContent = '';
                if(thisData.export_setting.csv){
                    var csvNumber = '';
                    switch(thisData.export_setting.csv.numbers){
                        case "none":
                            csvNumber = 'None';
                            break;
                        case "sku_numbers":
                            csvNumber = 'SKU Numbers';
                            break;
                        case "entry_codes":
                            csvNumber = 'Estimator Codes';
                            break;
                    }

                    thisContent +=
                        '<table class="exportSettingTable">' +
                            '<tr><td colspan="2"><strong style="text-align: center;width: 100%;">CSV Setting</strong><hr /></td></tr>' +
                            '<tr>' +
                                '<td><strong>Job Header:</strong></td>' +
                                '<td>' + (thisData.export_setting.csv.show_job_header === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>No Alternate/Drop Mapping:</strong></td>' +
                                '<td>' + (thisData.export_setting.csv.no_alternate_drop_mapping === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Show Merchant Description:</strong></td>' +
                                '<td>' + (thisData.export_setting.csv.show_merchant_description === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Manufacturing Comments:</strong></td>' +
                                '<td>' + (thisData.export_setting.csv.add_manufacturing_comments === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Show Truss Tag:</strong></td>' +
                                '<td>' + (thisData.export_setting.csv.apply_tag_code === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Hide MAPPING DELETIONS:</strong></td>' +
                                '<td>' + (thisData.export_setting.csv.hide_mapping_deletion === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Numbers:</strong></td>' +
                                '<td>' + csvNumber + '</td>' +
                            '</tr>' +
                        '</table>';
                }
                if(thisData.export_setting.pdf){
                    thisContent += thisContent ? '<br />' : '';
                    var pdfNumber = '';
                    switch(thisData.export_setting.pdf.numbers){
                        case "none":
                            pdfNumber = 'None';
                            break;
                        case "sku_numbers":
                            pdfNumber = 'SKU Numbers';
                            break;
                        case "entry_codes":
                            pdfNumber = 'Estimator Codes';
                            break;
                    }
                    thisContent +=
                        '<table class="exportSettingTable">' +
                            '<tr><td colspan="2"><strong style="text-align: center;width: 100%;">PDF Setting</strong><hr /></td></tr>' +
                            '<tr>' +
                                '<td><strong>No Alternate/Drop Mapping:</strong></td>' +
                                '<td>' + (thisData.export_setting.pdf.no_alternate_drop_mapping === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Show Merchant Description:</strong></td>' +
                                '<td>' + (thisData.export_setting.pdf.show_user_description === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Manufacturing Comments:</strong></td>' +
                                '<td>' + (thisData.export_setting.pdf.add_manufacturing_comments === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Show Truss Tag:</strong></td>' +
                                '<td>' + (thisData.export_setting.pdf.apply_tag_code === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Hide MAPPING DELETIONS:</strong></td>' +
                                '<td>' + (thisData.export_setting.pdf.hide_mapping_deletion === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Description first/Code last:</strong></td>' +
                                '<td>' + (thisData.export_setting.pdf.description_shown === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Select Lengths:</strong></td>' +
                                '<td>' + (thisData.export_setting.pdf.select_length === "true" ? 'yes' : 'no') + '</td>' +
                            '</tr>' +
                            '<tr>' +
                                '<td><strong>Numbers:</strong></td>' +
                                '<td>' + pdfNumber + '</td>' +
                            '</tr>' +
                        '</table>';
                }
                ourDetail.find('.exportSettingView').html(thisContent);
            }
            ourDetail.find('.exportSettingView').parent().css({ display: thisDisplay});
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