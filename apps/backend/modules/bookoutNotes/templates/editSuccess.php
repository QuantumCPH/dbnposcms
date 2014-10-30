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
      
         $('#editbookout').validate({
        
    });

    // must be called after validate()
    $('input.dnReceivedQuan').each(function () {
        $(this).rules('add', {
            required: true
        });
    });

        $(".chosen-select").chosen({no_results_text: "Oops, nothing found!"}); 
        
        $( "#delivery_date" ).datepicker({dateFormat: 'yy-mm-dd'});
        $( ".itemsid" ).autocomplete(
	{
            source:'<?php echo url_for("delivery_notes/showItems")?>'
	});
       
  });      
  
</script>


<div class="itemslist">
    <!--<h1 class="items-head">Items List <button type="button" class="btn btn-xs btn-primary">Add Item</button></h1>-->
    <h1 class="items-head list-page">
        <div class="titleicon"><img src="<?php echo sfConfig::get('app_web_url') . 'images/delivery_notes_title.png' ?>" />&nbsp;Edit Bookout Note</div>
        
    </h1>

<form action="<?php echo url_for('bookoutNotes/update')?>" method="post" id="editbookout">
<div  class="backimgDiv">
          <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>bookoutNotes/index';" value="Back" class="btn btn-cancel"/>
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
            <td>Bookout Number:</td><td><label><?php echo $id; ?></label></td>
        </tr>
        <tr>
            <td>Bookout  Date:</td><td><label><?php echo date("Y-m-d",strtotime($delivery_date));?><input type="hidden" name="delivery_date"  value="<?php echo date("Y-m-d",strtotime($delivery_date));?>"></label></td>
        </tr>
        <tr>
            <td>Branch Number:</td><td><?php  echo $branchNumber; ?>
<!--                <select data-placeholder="Choose an Shop..." style="width:182px;" class="chosen-select required" name="shop_id" >
                <?php foreach ($shops as $shop){  ?>              
                  <option value="<?php  echo $shop->getId(); ?>" <?php echo ($shop->getId()==$shopid)?"selected":"";?>><?php  echo $shop->getBranchNumber(); ?></option> 
             <?php     } ?>
                <?php //echo $branch; ?></select>-->
                <input type="hidden" name="shop_id" value="<?php  echo $shopid; ?>">
            </td>
        </tr>
        <tr>
            <td>Company Number:</td><td><label><?php echo $company; ?></label></td>
        </tr>
        
    </table>
   
    <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="editTable" style="width:84%" >
        <thead>
            <tr>
                <th>Item Number</th>
                <th>Send Quantity</th>
                 <th>Received Comment</th>
                 <th>Received Quantity</th>
                <th>Reply Comment</th>
            </tr>

        </thead>
        <tbody>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <?php foreach ($notes_list as $note): ?>
                <tr>
                    
                    <td><input type="hidden" name="dnId[]" value="<?php echo $note->getId(); ?>" />
                        
                         <input type="hidden" name="dnItemNo[]" autocomplete="on" value="<?php echo $note->getItemId();?>"   />
                        <?php echo $note->getItemId();?>
                    </td>
                    <td><?php echo $note->getQuantity(); ?></td>
                       <td><?php echo $note->getComment(); ?></td>
                       <td><input type="text" name="dnReceivedQuantity[<?php echo $note->getId(); ?>]"  value="<?php echo $note->getReceivedQuantity(); ?>"    class="form-control dnReceivedQuan"/></td>
                    <td> <textarea name="dnreplyComent[]"  id="<?php echo $note->getId(); ?>"    class="form-control"><?php echo $note->getReplyComment(); ?></textarea></td>
                </tr>
            <?php endforeach; ?>


        </tbody>

    </table><br />
 
</div>
  
</form>

</div>
