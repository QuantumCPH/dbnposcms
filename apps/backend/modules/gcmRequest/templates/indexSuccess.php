 
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
      var oTable =$('#myTable').dataTable( {
      
        "bProcessing": false,
	"bServerSide": false,
	 
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
         
    } ).columnFilter();
     oTable.fnSort([[6, 'desc']]);
    $.extend( $.fn.dataTableExt.oStdClasses, {
    "sWrapper": "dataTables_wrapper form-inline"
} );
} );
    </script>
<h1>GcmRequest List</h1>

<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered"  id="myTable" >
  <thead>
    <tr>
      <th>Id</th>
      <th>Branch Number</th>
       <th>Branch Name</th>
      <th>User</th>
      <th>Action name</th>
      <th>Created at</th>
     
      <th>Received at</th>
      <th>Status</th>
      <th>Sent</th>
      <th>Received</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($gcm_request_list as $gcm_request): ?>
    <tr>
      <td> <?php echo $gcm_request->getId() ?> </td>
      <td><?php  if($gcm_request->getShopId()){ $shop=ShopsPeer::retrieveByPK($gcm_request->getShopId());  echo $shop->getBranchNumber();    }     ?></td>
      <td><?php  if($gcm_request->getShopId()){ $shop=ShopsPeer::retrieveByPK($gcm_request->getShopId());  echo $shop->getName();    }     ?></td>
      <td><?php if($gcm_request->getUserId()){ $user=UserPeer::retrieveByPk($gcm_request->getUserId()); echo $user->getName(); }?></td>
      <td><?php echo $gcm_request->getActionName() ?></td>
      <td><?php echo $gcm_request->getCreatedAt() ?></td>
 
      <td><?php echo $gcm_request->getReceivedAt() ?></td>
      <td><?php echo $gcm_request->getRequestStatus() ?></td>
      <td><?php echo $gcm_request->getSentCount() ?></td>
      <td><?php echo $gcm_request->getReceiveCount() ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
