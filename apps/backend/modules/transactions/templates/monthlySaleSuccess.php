<style>
    table.table thead .sorting,
    table.table thead .sorting_asc,
    table.table thead .sorting_desc,
    table.table thead .sorting_asc_disabled,
    table.table thead .sorting_desc_disabled {
        cursor: pointer;
        *cursor: hand;
    }

    table.table thead .sorting { background:#752271 url('<?php echo sfConfig::get("app_web_url") ?>media/images/asc_des.png') no-repeat center right; }
    table.table thead .sorting_asc { background:#752271 url('<?php echo sfConfig::get("app_web_url") ?>media/images/arrow_up1.png') no-repeat center right; }
    table.table thead .sorting_desc { background:#752271 url('<?php echo sfConfig::get("app_web_url") ?>media/images/arrow_down.png') no-repeat center right; }

    table.table thead .sorting_asc_disabled { background:#752271 url('<?php echo sfConfig::get("app_web_url") ?>media/images/sort_asc_disabled.png') no-repeat center right; }
    table.table thead .sorting_desc_disabled { background:#752271 url('<?php echo sfConfig::get("app_web_url") ?>media/images/sort_desc_disabled.png') no-repeat center right; }
</style>   

<script type="text/javascript">
    $(document).ready(function() {

        var cb = function(start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
            // $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            $('#daterangein').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            // alert("Callback has fired: [" + start.format('MMMM D, YYYY') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
            //   alert("Callback has fired: [" + start.format('YYYY-MM-DD') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
            $('#startDate').val(start.format('YYYY-MM-DD'));
            $('#endDate').val(end.format('YYYY-MM-DD'));
            ///////////////////////////////////////////////////////////////////////////////////////////  


            $.ajax({
                type: "POST",
                url: "paymentMethod",
                data: {startDate: start.format('YYYY-MM-DD'), endDate: end.format('YYYY-MM-DD'), city: $("#city_new").val()},
                success: function() {
                    oTable.fnDraw();


                }
            });
            //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////            
        }

        var optionSet1 = {
            startDate: moment().subtract('days', 29),
            endDate: moment(),
//            minDate: '01/01/2012',
//            maxDate: '12/31/2014',
//            dateLimit: {days: 700},
//            showDropdowns: true,
//            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 30 Days': [moment().subtract('days', 29), moment()],
                'Last 60 Days': [moment().subtract('days', 59), moment()],
                'Last 90 Days': [moment().subtract('days', 89), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            opens: 'left',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-small btn-primary',
            cancelClass: 'btn-small',
            format: 'MM/DD/YYYY',
            separator: ' to ',
            locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Clear',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        };



        //  $('#reportrange span').html(moment().subtract('days', 29).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
        $('#daterangein').val(moment().subtract('days', 29).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
        $('#startDate').val(moment().subtract('days', 29).format('YYYY-MM-DD'));
        $('#endDate').val(moment().format('YYYY-MM-DD'));
        $('#reportrange').daterangepicker(optionSet1, cb);

        $('#reportrange').on('show.daterangepicker', function() {
            console.log("show event fired");
        });
        $('#reportrange').on('hide.daterangepicker', function() {
            console.log("hide event fired");
        });
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            console.log("apply event fired, start/end dates are "
                    + picker.startDate.format('MMMM D, YYYY')
                    + " to "
                    + picker.endDate.format('MMMM D, YYYY')
                    );
        });
        $('#reportrange').on('cancel.daterangepicker', function(ev, picker) {
            console.log("cancel event fired");
        });



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        var oTable = $('#myTable').dataTable({
            "bProcessing": true,
            "bServerSide": true,
            "bpaging": false,
            "bFilter": false,
            "bInfo": false,
            "sAjaxSource": "<?php echo sfConfig::get('app_web_url') ?>server_monthly_report.php",
            "sPaginationType": "full_numbers",
            "aLengthMenu": [[10, 25, 50, 100, 250, 500], [10, 25, 50, 100, 250, 500]],
            "fnServerParams": function(aoData) {
                aoData.push({"name": "startDate", "value": $('#startDate').val()}, {"name": "endDate", "value": $('#endDate').val()});
            }
        });

        $.extend($.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        });

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        $("#csvexport").click(function() {


            var stdate = $("#startDate").val();
            var endate = $("#endDate").val();
            $.ajax({
                type: "POST",
                url: "../../csv_monthly_sale.php",
                data: {startDate: $("#startDate").val(), endDate: $("#endDate").val()},
                cache: false,
                dataType: 'html',
                success: function(data) {
                    window.open('../../csv_monthly_sale.php?startDate=' + stdate + '&endDate=' + endate);
                }
            });
        });
        ////////////////////////////////////////////////////////////////////////////////
    });
</script>



<div class="itemslist">
    <input type="hidden" name="startDate" id="startDate" value="">
    <input type="hidden" name="endDate" id="endDate" value="">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/sale_over.png' ?>" />&nbsp;Reports /Monthly Orders – Sale </h1>
    <div class="backimgDiv">
        <div id="reportrange" class="pull-right" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
            <span></span>
            <input type="text"   id="daterangein" name="daterange" class="daterangeinput" value="">
            <b class="caret"></b>
        </div>
        <div class="btn-group">

            <button type="button" class="btn btn-danger dropdown-toggle  btn-sm "  data-toggle="dropdown" style="color:#000000;background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc">
                Export <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#"  id="csvexport">CSV</a></li>

            </ul>
        </div>
    </div>

    <br/> <br/>



    <div class="itemslist listviewpadding "><br />
        <?php if ($sf_user->hasFlash('access_error')): ?>
            <div class="alert alert-warning">
                <?php echo $sf_user->getFlash('access_error') ?>
            </div>
        <?php endif; ?>
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered itemlistTable" id="myTable" >
            <thead>
                <tr>
                    <th>Month</th>
                    <th>year</th>
                    <th>Order Count</th>
                    <th>Total Sale</th>

                </tr>
            </thead>
            <tbody>


            </tbody>

        </table>

    </div>
</div>