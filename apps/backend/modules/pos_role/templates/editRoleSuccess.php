<script>
  $().ready(function() {
      $("#frmAddRole").validate({
          rules: {
           "permissions[]": {
              required: true,
              minlength: 1
          }
         },messages: {
            "permissions[]":{
                minlength: "<?php echo __("Select Permission."); ?>"
            }
         }
      });
      
         $('#optgroup').multiSelect({ selectableOptgroup: true,
         selectableHeader: "<div class='custom-header'>Available</div>",
         selectionHeader: "<div class='custom-header'>Included</div>"
         });

  });
   $(document).ready(function() {
      
      $(".shopsno").chosen({no_results_text: "Oops, nothing found!",max_selected_options: 1}); 
  });
</script>
<div class="itemslist">
<!--<h1 class="items-head">Items List <button type="button" class="btn btn-xs btn-primary">Add Item</button></h1>-->
<h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/role_edit_title.png'?>" />&nbsp;<span>Edit POS Role</span></h1>
<form name="frmEditRole" action="<?php echo sfConfig::get("app_admin_url")?>pos_role/editProcess" method="post" id="frmEditRole">
<div  class="backimgDiv">
    <?php if($redirect_shop_id!="" && $redirect_shop_id>0){ ?>
        <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>shops/view?id=<?php echo $redirect_shop_id;?>'" value="Cancel" class="btn btn-cancel" />
    <?php
        }else{ ?>
    <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>pos_role/index';" value="Cancel" class="btn btn-cancel"/>
    <?php } ?>
    
    <button type="submit" class="btn btn-primary">&nbsp;Save&nbsp;</button>
    <input type="hidden" value="<?php echo $redirect_shop_id; ?>" name=redirect_shop_id />
</div>
<div class="regForm listviewpadding"><br />
     <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
    <?php if ($sf_user->hasFlash('role_message')): ?>
    <div class="alert alert-success">
        <?php echo $sf_user->getFlash('role_message') ?>
    </div>
    <?php endif;?>
    <input type="hidden" name="role_id" value="<?php echo $role->getId();?>" />
    <table class="table table-striped">
    <tbody>
        <tr class="odd"><th class="lbl"><strong>Role *</strong></th><th><input type="text" name="roleName" class="form-control" required value="<?php echo $role->getName()?>" /></th></tr>
        <tr class="odd"><th class="lbl"><strong>Branch Number *</strong></th>
            <th class="shopsnoselect">
                <select name="shop_id[]" class="shopsno form-control" multiple>
                    <?php  foreach ($shoproles as $shoprole){
                            $fieldsArray[]=$shoprole->getShopId();
                    } ?>
                    <?php 
                     foreach($shops as $shop){
                    ?>
                    <?php   if(in_array($shop->getId(), $fieldsArray)){   ?>                  
                             <option value="<?php  echo $shop->getId(); ?>"  selected="selected"><?php  echo $shop->getBranchNumber(); ?></option> 
                    <?php   }else{ ?>                  
                             <option value="<?php echo $shop->getId()?>"><?php echo $shop->getBranchNumber();?></option>                   
                    <?php } ?>
                    
                    <?php      
                     }
                    ?>
                </select>
            </th></tr>
        <tr><th colspan="2" class="permissionlbl"><strong>Permissions </strong></th></tr>
        <tr><td colspan="2" class="permissionstd">
          <div class="seldesAll"> 
<!--              <label class="">
                  <input type="checkbox" name="" id="selectAll" />&nbsp;Select All                      
              </label>-->
          </div> 
      
                 <select id='optgroup' multiple='multiple' name="permissions[]">
  

         <?php

 
            foreach($modules as $module){ ?>
                 <optgroup label='<?php echo $module->getTitle();?>'>
                       <?php
                    $cp = new Criteria();
                    $cp->add(PosPermissionPeer::POS_MODULE_ID,$module->getId());
                    $cp->addAscendingOrderByColumn(PosPermissionPeer::POSITION);
                    $permissions = PosPermissionPeer::doSelect($cp);
                    ?>
                    <?php
                    foreach($permissions as $permission){ ?>
                     
                      <option value='<?php echo $permission->getId()?>'  <?php echo ($permission->getIsChecked($permission->getId(),$role->getId()))?'selected':""; ?>><?php echo $permission->getActionTitle()?></option>
                        <?php   }       ?>
                 </optgroup>
            <?php  } ?>
                
                
                
               </select> 
                
                
        </td></tr>
    </tbody>
    </table>
    <p></p>
    <p>&nbsp;</p><p>&nbsp;</p>

</div></form>
</div>

 