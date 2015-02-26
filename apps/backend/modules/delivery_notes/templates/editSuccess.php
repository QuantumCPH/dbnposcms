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
<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript">

    $(document).ready(function() {
        $("#editnotes").validate();
        $(".chosen-select").chosen({no_results_text: "Oops, nothing found!"}); 
        
        $( "#delivery_date" ).datepicker({dateFormat: 'yy-mm-dd'});
        $( ".itemsid" ).autocomplete(
	{
            source:'<?php echo url_for("delivery_notes/showItems")?>'
	});
        $("#delall").click(function(){
          $(".select_del").prop("checked",$("#delall").prop("checked"))
       }) 
       $(".select_del").click(function(){ 
            if($(".select_del").length == $(".select_del:checked").length) {
//                $("#delall").attr("checked", "checked");
                $("#delall").prop('checked', true);
            } else {
                $("#delall").removeAttr("checked");
            }
       });
       $("#editnotes").on('submit',function(){
           if($(".select_del:checked").length > 0){               
               if(confirm("Are you sure to delete item?")){
                   return ture;
               }else{
                   return false;
               }
           }
       })
  });      
    $(function () {
        $('.addItem').on('click', function () {
            $.fn.custombox({
                url: '<?php echo url_for("delivery_notes/addItem?note_id=".$id)?>'
            });
        });
     });
</script>


<div class="itemslist">
    <!--<h1 class="items-head">Items List <button type="button" class="btn btn-xs btn-primary">Add Item</button></h1>-->
    <h1 class="items-head list-page">
        <div class="titleicon"><img src="<?php echo sfConfig::get('app_web_url') . 'images/delivery_notes_title.png' ?>" />&nbsp;Edit Delivery Note</div>
        
    </h1>

<form action="<?php echo url_for('delivery_notes/update')?>" method="post" id="editnotes">
<div  class="backimgDiv">
          <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>delivery_notes/index';" value="Back" class="btn btn-cancel"/>
<input type="submit" value="Update" class="btn btn-primary" />        
</div>
<div class="itemslist listview" style="">  <br />
    <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
    <?php if ($sf_user->hasFlash('edit_error')): ?>
    <div class="alert alert-danger">
        <?php echo $sf_user->getFlash('edit_error') ?>
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
            <td>Delivery Date:</td><td><label><input type="text" name="delivery_date" class="form-control required" id="delivery_date" value="<?php echo date("Y-m-d",strtotime($delivery_date));?>"></label></td>
        </tr>
        <tr>
            <td>Branch Number:</td><td><select data-placeholder="Choose an Shop..." style="width:182px;" class="chosen-select required" name="shop_id" >
                <?php foreach ($shops as $shop){  ?>              
                  <option value="<?php  echo $shop->getId(); ?>" <?php echo ($shop->getId()==$shopid)?"selected":"";?>><?php  echo $shop->getBranchNumber(); ?></option> 
             <?php     } ?>
                <?php //echo $branch; ?></select></td>
        </tr>
        <tr>
            <td>Company Number:</td><td><label><?php echo $company; ?></label></td>
        </tr>
        
    </table>
    <p><a data-toggle="modal" href="#" class="btn btn-primary addItem">Add New Item</a></p><br />
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="editTable" style="width:44%" >
        <thead>
            <tr>
                <th>Item Number</th>
                <th>Quantity</th>
                <th alt="Select to Delete"><label for="delall" class="dellbl">Del All</label>&nbsp;<input type="checkbox" name="checkall" id="delall" title="Delete All" class="delall" /></th>
            </tr>

        </thead>
        <tbody>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <?php foreach ($notes_list as $note): ?>
                <tr>
                    
                    <td><input type="hidden" name="dnId[]" value="<?php echo $note->getId(); ?>" />
                        
                        <input type="text" name="dnItemNo[]" autocomplete="on" value="<?php echo $note->getItemId();?>" class="form-control itemsid digits required" />
                    </td>
                    <td><input type="text" value="<?php echo $note->getQuantity(); ?>" name="dnItemQty[]" class="form-control required digits" style="width:75px;" /></td>
                <td alt="Select to Delete" title="Select to Delete"><input type="checkbox" name="deletednId[]" value="<?php echo $note->getId(); ?>" class="select_del" title="Select to Delete" /></td>
                </tr>
            <?php endforeach; ?>


        </tbody>

    </table><br />
<!--  <p><a data-toggle="modal" href="#" class="btn btn-primary addItem">Add New Item</a></p>-->
</div>
  
</form>

</div>
