<style>
    table.table thead .sorting,
table.table thead .sorting_asc,
table.table thead .sorting_desc,
table.table thead .sorting_asc_disabled,
table.table thead .sorting_desc_disabled {
    cursor: pointer;
    *cursor: hand;
}
 
table.table thead .sorting { background:#752271 url('<?php echo sfConfig::get("app_web_url")?>media/images/asc_des.png') no-repeat center right; }
table.table thead .sorting_asc { background:#752271 url('<?php echo sfConfig::get("app_web_url")?>media/images/arrow_up1.png') no-repeat center right; }
table.table thead .sorting_desc { background:#752271 url('<?php echo sfConfig::get("app_web_url")?>media/images/arrow_down.png') no-repeat center right; }
 
table.table thead .sorting_asc_disabled { background:#752271 url('<?php echo sfConfig::get("app_web_url")?>media/images/sort_asc_disabled.png') no-repeat center right; }
table.table thead .sorting_desc_disabled { background:#752271 url('<?php echo sfConfig::get("app_web_url")?>media/images/sort_desc_disabled.png') no-repeat center right; }
    </style>
 
<script type="text/javascript">
 
    $(document).ready(function() {
         $.datepicker.regional[""].dateFormat = 'yy-mm-dd 00:00:00';
            	$.datepicker.setDefaults($.datepicker.regional['']);
  
    $('#myTable').dataTable( {
      
        "bProcessing": true,
	"bServerSide": true,
	"sAjaxSource": "<?php echo sfConfig::get('app_web_url')?>server_cronjob_history_info.php",
        "sPaginationType": "full_numbers",
         "sDom": "<'paging'<'span6'l><'span6'f>r>t<'paging'<'span6'i><'span6'p>>",
         "fnServerParams": function ( aoData ) {
    aoData.push({ "name": "history_id", "value": $("#history_id").val() });
}
         
    } ).columnFilter({ aoColumns: [ 	{ type: "text" },
				    	 		{ type: "text" },
                                                        null, 
                                    			null
						]

		});
    
    $.extend( $.fn.dataTableExt.oStdClasses, {
    "sWrapper": "dataTables_wrapper form-inline"
} );
} );
    </script>
   <input type="hidden" name="history_id" id="history_id" value="<?php echo $history_id;  ?>">
  
<div class="itemslist">
 
<h1 class="items-head list-page">Crob job History Info &nbsp;  <img src="<?php echo sfConfig::get('app_web_url').'images/inventory_over.png'?>" /></h1>
<div  class="backimgDiv"> 
  <input type="button" onclick="document.location.href='<?php echo $close_url;?>';" value="Back" class="btn btn-cancel"/>
  
</div>
</div>
 
<div class="itemslist listviewpadding "><br />
        <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTable" >
<thead>
  <tr>
      <th>xml</th>
    <th>csv</th>
     <th>status</th>
    <th>message</th>
    <th>created at</th>
  </tr>
 </thead>
<tbody>

  
</tbody>
 
</table>
 

</div>
   