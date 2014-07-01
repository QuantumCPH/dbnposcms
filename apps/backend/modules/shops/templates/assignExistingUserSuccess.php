<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="itemslist">
    <h1 class="items-head list-page" style="padding: 10px 17px 0;">
        <img src="<?php echo sfConfig::get('app_web_url') . 'images/shops_over.png' ?>" />&nbsp;Assign User to Branch: <?php echo $shop->getName(); ?>

    </h1>


    <br/>
    <br/>
    <form action="<?php echo url_for('shops/assignExistingUserCreate') ?>" method="post" id="frmUser">
        <div  class="backimgDiv"> 
            <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>shops/view?id=<?php echo $shop->getId(); ?>'" value="Back" class="btn btn-cancel"/>
            <input type="submit" value="Save" class="btn btn-primary" />
            <input type="hidden" value="<?php echo $shop->getId(); ?>" name='shop_id'/>
        </div>
        <div class="regForm listviewpadding">


            <div class="lblleft"> Users :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="user_id" class="form-control">
                        <?php foreach ($user_list as $user) { ?>
                            <option value='<?php echo $user->getId() ?>'><?php echo $user->getName(); ?></option>

                        <?php } ?> 
                    </select>
                </div>
            </div> 

            <div class="lblleft">POS Roles :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="role_id" class="form-control">
                        <?php foreach ($pos_roles_list as $pos_role) { ?>
                            <option value='<?php echo $pos_role->getId() ?>'><?php echo $pos_role->getName(); ?></option>

                        <?php } ?> 
                    </select>
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft"> POS Administrator :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="checkbox"    name="pos_super_user" />
                </div>
            </div> 


        </div>



    </form>
</div>