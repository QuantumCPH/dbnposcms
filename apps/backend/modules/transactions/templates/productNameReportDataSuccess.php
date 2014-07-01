   <script type="text/javascript">
               $(document).ready(function() {

                  var cb = function(start, end, label) {
                    console.log(start.toISOString(), end.toISOString(), label);
                   // $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                     $('#daterangein').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                   // alert("Callback has fired: [" + start.format('MMMM D, YYYY') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
                     alert("Callback has fired: [" + start.format('YYYY-MM-DD') + " to " + end.format('MMMM D, YYYY') + ", label = " + label + "]");
                     
                     
                       $.ajax({  
        type: "POST",  
        url: "productNameReportData",  
        data: {startDate: start.format('YYYY-MM-DD'), endDate: end.format('YYYY-MM-DD'), city: $("#city_new").val()},
        success: function(){  
            $(this).hide();
            $('div.success').fadeIn(); 
        showUsers()
        }  
    }); 
                     
                  }
               
                  var optionSet1 = {
                    startDate: moment().subtract('days', 29),
                    endDate: moment(),
//                    minDate: '01/01/2012',
//                    maxDate: '12/31/2014',
//                    dateLimit: { days: 600 },
//                    showDropdowns: true,
//                    showWeekNumbers: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    ranges: {
                       'Today': [moment(), moment()],
                       'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                       'Last 7 Days': [moment().subtract('days', 6), moment()],
                       'Last 30 Days': [moment().subtract('days', 29), moment()],
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
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr','Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay: 1
                    }
                  };

                

                //  $('#reportrange span').html(moment().subtract('days', 29).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
                  $('#daterangein').val(moment().subtract('days', 29).format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));
                  $('#reportrange').daterangepicker(optionSet1, cb);

                  $('#reportrange').on('show.daterangepicker', function() { console.log("show event fired"); });
                  $('#reportrange').on('hide.daterangepicker', function() { console.log("hide event fired"); });
                  $('#reportrange').on('apply.daterangepicker', function(ev, picker) { 
                    console.log("apply event fired, start/end dates are " 
                      + picker.startDate.format('MMMM D, YYYY') 
                      + " to " 
                      + picker.endDate.format('MMMM D, YYYY')
                    ); 
                  });
                  $('#reportrange').on('cancel.daterangepicker', function(ev, picker) { console.log("cancel event fired"); });

                  $('#options1').click(function() {
                    $('#reportrange').data('daterangepicker').setOptions(optionSet1, cb);
                  });

                  $('#options2').click(function() {
                    $('#reportrange').data('daterangepicker').setOptions(optionSet2, cb);
                  });

                  $('#destroy').click(function() {
                    $('#reportrange').data('daterangepicker').remove();
                  });

 
               });
               </script>

            
 
<div class="itemslist">

    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/sale_over.png' ?>" />&nbsp;Reports</h1>
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
    <li><a href="#">CSV</a></li>
    
   
  </ul>
</div>
</div>
</div>
 

            

        


              
               


             


<div class="itemslist listviewpadding "><br />
    <?php if ($sf_user->hasFlash('access_error')): ?>
        <div class="alert alert-warning">
            <?php echo $sf_user->getFlash('access_error') ?>
        </div>
    <?php endif; ?>
   

</div>
