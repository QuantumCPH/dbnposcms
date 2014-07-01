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
        $('#myTable').dataTable({
            "bPaginate": true,
            "bLengthChange": true,
            "bFilter": false,
            "bSort": true,
            "bInfo": true,
            "bAutoWidth": false,
            "sDom": 'T<"clear">lfrtip',
            "oTableTools": {
                "sSwfPath": "<?php echo sfConfig::get("app_web_url") ?>media/swf/copy_csv_xls.swf",
                "aButtons": [
                    "csv",
                    "print"

                ]
            },
            "aLengthMenu": [[10, 25, 50,100,250 ,-1], [10, 25, 50,100,250,"All"]]

        });

        $.extend($.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        });
    });
</script>


<div class="itemslist">
    <!--<h1 class="items-head">Items List <button type="button" class="btn btn-xs btn-primary">Add Item</button></h1>-->
    <h1 class="items-head list-page">
        <div class="titleicon"><img src="<?php echo sfConfig::get('app_web_url') . 'images/delivery_notes_title.png' ?>" />&nbsp;Delivery Note Details</div>
        
    </h1>
</div>
<div  class="backimgDiv">
          <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>delivery_notes/index';" value="Back" class="btn btn-cancel"/>
       <?php if($edit_del):?>  
          <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>delivery_notes/edit/id/<?php echo $id;?>';" value="Edit" class="btn btn-primary"/>
          <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>delivery_notes/delNotes/id/<?php echo $id;?>';" value="Delete" class="btn btn-cancel"/>
       <?php endif;?>
          
<?php if(gf::getPreviousDNId($dn->getGroupId())){ ?>
           <a href="<?php echo url_for("delivery_notes/view?id=".gf::getPreviousDNId($dn->getGroupId())) ?>" class="btn btn-primary viewPrev">Previous </a>
        <?php } ?>         
 <?php if(gf::getNextDNId($dn->getGroupId())){?>
           <a href="<?php echo url_for("delivery_notes/view?id=".gf::getNextDNId($dn->getGroupId()))?>" class="btn btn-primary viewNext">Next </a>
        <?php } ?>
        
</div>
<div class="itemslist listview" style="">  <br />
    <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
    <?php if ($sf_user->hasFlash('message')): ?>
    <div class="alert alert-success">
        <?php echo $sf_user->getFlash('message') ?>
    </div>
    <?php endif;?>
    <table cellspacing="0" class="deliverinfotbl">
        <tr>
            <td>Delivery Number:</td><td><label><?php echo $id; ?></label></td>
        </tr>
        <tr>
            <td>Delivery Date:</td><td><label><?php echo date("Y-m-d",strtotime($delivery_date)); ?></label></td>
        </tr>
        <tr>
            <td>Company Number:</td><td><label><?php echo $company; ?></label></td>
        </tr>
        <tr>
            <td>Branch Number:</td><td><label><?php echo $branch; ?></label></td>
        </tr>
    </table>
<!--    <ul class="list-group" style="width: 350px;">
        <li class="list-group-item">
            <span class="badge"><?php echo $id; ?></span>
            Delivery Number:
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $delivery_date; ?></span>
            Delivery Date:
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $company; ?></span>
            Company Number:
        </li>
        <li class="list-group-item">
            <span class="badge"><?php echo $branch; ?></span>
            Branch Number:
        </li>

    </ul>-->
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTable" >
        <thead>
            <tr>
                <th>Item Number</th>
                <th>Is Synced</th>
                <th>Synced At</th>
                <th>Quantity</th>
                <th>Is Received</th>
                <th>Received At</th>
                <th>Received Quantity</th>
                <th>Comments</th>
                <th>Updated By</th>
            </tr>

        </thead>
        <tbody>
            <?php foreach ($notes_list as $note): ?>
                <tr>
                    <td><?php echo $note->getItemId(); ?></td>
                    <td><?php echo $note->getIsSynced() == 1 ? "Yes" : "No"; ?></td>
                    <td><?php echo $note->getSyncedAt(); ?></td>
                    <td><?php echo $note->getQuantity(); ?></td>
                    <td><?php echo $note->getIsReceived() == 1 ? "Yes" : "No"; ?></td>
                    <td><?php echo $note->getShopRespondedAt(); ?></td>
                    <td><?php echo $note->getReceivedQuantity() ?></td>
                    <td><?php echo $note->getComment(); ?></td>
                    <td><?php 
                      if($note->getUpdatedBy()){
                        $user = UserPeer::retrieveByPK($note->getUpdatedBy()); 
                        if($user) echo $user->getName(); 
                      }
                    ?></td>
                </tr>
            <?php endforeach; ?>


        </tbody>

    </table>
    <!-- <tr><td colspan="11" align="left"><strong>Total Items. <?php //echo $itemsCount;     ?></strong></td></tr>-->

</div>
