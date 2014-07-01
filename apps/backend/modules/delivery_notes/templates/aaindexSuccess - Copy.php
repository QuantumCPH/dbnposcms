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
            "sDom": "<'paging'<'span6'l><'span6'f>r>t<'paging'<'span6'i><'span6'p>>"

        });

        $.extend($.fn.dataTableExt.oStdClasses, {
            "sWrapper": "dataTables_wrapper form-inline"
        });
    });
</script>


<div class="itemslist">
    <!--<h1 class="items-head">Items List <button type="button" class="btn btn-xs btn-primary">Add Item</button></h1>-->
    <h1 class="items-head"><img src="<?php echo sfConfig::get('app_web_url') . 'images/active-item2.png' ?>" />&nbsp;Delivery Notes</h1>
</div> 
<div class="itemslist listview">
    <div class="searchFrm">
        <div class="frmborder">  
            <form name="searchFrm" action="">
                <input type="submit" name="searchbtn" value="" class="searchbtn" /><input type="text" name="searchFld" class="searchFld" placeholder="Type Delivery Number" value="" />
            </form>
        </div>    
    </div> 
    
    
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="myTable" >
        <thead>
            <tr>
                <th>ID</th>
                <th>Delivery Number</th>
                <th>Delivery Date</th>
                <th>Quantity</th>
                <th>Company Number</th>
                <th>Branch Number</th>
                <th>Created At:</th>
            </tr>

        </thead>
        <tbody>
            <?php foreach ($notes_list as $note): ?>
                <?php if($note->getIsSynced() && $note->getIsReceived()){
                    $class="success";
                    
                    
                }if($note->getIsSynced() && !$note->getIsReceived()){
                    $class="warning";
                } ?>
                <tr >
                    <td><?php echo $note->getId(); ?></td>
                    <td><a href="<?php echo sfConfig::get("app_admin_url") ?>delivery_notes/view/id/<?php echo $note->getNoteId() ?> "><?php echo $note->getNoteId(); ?><a/></td>
                    <td><?php echo $note->getDeliveryDate(); ?></td>
                    <td><?php echo $note->getQuantity(); ?></td>
                    <td><?php echo $note->getCompanyNumber(); ?></td>
                    <td><?php echo $note->getBranchNumber(); ?></td>
                    <td><?php echo $note->getCreatedAt(); ?></td>
                </tr>
            <?php endforeach; ?>


        </tbody>

    </table>
    <!-- <tr><td colspan="11" align="left"><strong>Total Items. <?php //echo $itemsCount;   ?></strong></td></tr>-->
    
    </div>
