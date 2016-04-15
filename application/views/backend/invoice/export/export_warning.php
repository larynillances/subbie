<script language="JavaScript">
    $(function(e){
        $('.closedOption').click(function(e){
            $('#certainDetail').html('');
        });
    });
</script>
<style>
    .printOptionMenu{
        border: 1px solid #000000;
        padding: 0;
        width: 369px;
    }
    #printOptionMenu{
        font-size: 14px;
        text-align: left;
        padding: 3px 5px;
        width: 100%;
    }
    #printOptionMenu tr td{
        text-align: justify;
        font-style: italic;
        color: #ff5640;
    }
</style>

<div class="printOptionMenu">
    <div style="background: #000000;color: #ffffff;padding: 5px 10px;">
        Export Options
        <span id="closedCustomForm" class="closedOption" title="close">x</span>
    </div>
    <table id="printOptionMenu">
        <tr>
            <td>
                This Branch has insufficient Takeoff Return details configured to allow Export.
                Please advise <strong>Estimator Head Office IT Support</strong> immediately.
                You cannot proceed.
            </td>
        </tr>
    </table>
</div>