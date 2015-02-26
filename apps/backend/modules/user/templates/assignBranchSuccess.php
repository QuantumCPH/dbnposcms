<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script type="text/javascript">
    jQuery(function() {
        
        $("#frmUser").validate();
    
        $("#branch-select").on('change', function() {
            selectedValue = $(this).val();
            if (selectedValue == "") {
                jQuery("#pos_roles").html("<option value=''>Please select a branch first</option>");
                console.log("no value selected");
            } else {
                $.ajax({
                    type: "POST",
                    url: "<?php echo sfConfig::get("app_admin_url"); ?>user/getPosRoles",
                    data: {shop_id: selectedValue},
                    cache: false
                }).done(function(data) {
                    jQuery("#pos_roles").html(data);
                });
                console.log(selectedValue);
            }

        });
    });
</script>
<div class="itemslist">
    <h1 class="items-head list-page" style="padding: 10px 17px 0;">
        <img src="<?php echo sfConfig::get('app_web_url') . 'images/shops_over.png' ?>" />&nbsp;Assign Branch to User: <?php echo $user->getName(); ?>

    </h1>


    <br/>
    <br/>
    <form action="<?php echo url_for('user/assignBranchCreate') ?>" method="post" id="frmUser">
        <div  class="backimgDiv"> 
            <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>user/view?id=<?php echo $user->getId(); ?>'" value="Back" class="btn btn-cancel"/>
            <input type="submit" value="Save" class="btn btn-primary" />
            <input type="hidden" value="<?php echo $user->getId(); ?>" name='user_id'/>
        </div>
        <div class="regForm listviewpadding">


            <div class="lblleft"> Branches :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="shop_id" class="form-control required" id="branch-select">
                        <option value=''>Please Select Branch</option>
                        <?php foreach ($shops_list as $branch) { ?>
                            <option value='<?php echo $branch->getId() ?>'><?php echo $branch->getBranchNumber(); ?></option>

                        <?php } ?> 
                    </select>
                </div>
            </div> 

            <div class="lblleft"> POS Roles :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <select name="role_id" class="form-control required" id="pos_roles">
                        <option value=''>Please select a branch first</option>
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