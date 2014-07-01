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
    $('#myTable').dataTable( {
      
        "bProcessing": true,
	"bServerSide": true,
	"sAjaxSource": "<?php echo sfConfig::get('app_web_url')?>server_shops.php",
        "sPaginationType": "full_numbers",
         "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "sSwfPath": "<?php echo sfConfig::get("app_web_url") ?>media/swf/copy_csv_xls.swf",
                "aButtons": [
                    "csv",
                    "print"

                ]
            },
            "aLengthMenu": [[10, 25, 50,100,250 ,-1], [10, 25, 50,100,250,"All"]]
         
    } ).columnFilter({ aoColumns: [ 	null,{ type: "text" }, { type: "text" }, { type: "text" },{ type: "text" },
				    	 		 
                                    			null
						]

		});
    
    $.extend( $.fn.dataTableExt.oStdClasses, {
    "sWrapper": "dataTables_wrapper form-inline"
} );
} );
    </script>
 

<div class="itemslist">
 
<h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/shops_over.png'?>" />&nbsp;Branches
  
</h1>
</div>
<div  class="backimgDiv">
      <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>shops/add';" value="Add" class="btn btn-primary"/>
  </div>
<div class="regForm listviewpadding"><br />
     <?php if ($sf_user->hasFlash('message')): ?>
    <div class="alert alert-success">
        <?php echo $sf_user->getFlash('message') ?>
    </div>
    <?php endif;?>
    <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?> 
<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTable" >
<thead>
  <tr>
        <th>ID</th>
     <th>Name</th>
      <th>Branch number</th>
      <th>Company number</th>
      <th>Tel</th>
      <th>Updated By</th>
      <th>Action</th>
  </tr>
 </thead>
<tbody>

  
</tbody>
 <tfoot>
		 <tr>
        <th>ID</th>
     <th>Name</th>
      <th>Branch number</th>
      <th>Company number</th>
      <th>Tel</th>
      <th>Updated By</th>
      <th>Action</th>
  </tr>
	</tfoot>
</table>
 <br clear="all" /><br />
<ul class="sf_admin_actions"><li></li></ul>
</div>