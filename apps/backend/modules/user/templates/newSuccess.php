<div class="itemslist">
    <h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url') . 'images/user_add_title.png' ?>" />&nbsp;<span>Create User</span></h1>
    <?php if ($sf_user->hasFlash('error_user')): ?>
        <div class="alert alert-success">
            <h2><?php echo $sf_user->getFlash('error_user') ?></h2>
        </div>
    <?php endif; ?>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#frmUser").validate({
                rules: {
                    "email": {
                        required: true,
                        email: true
                        /*,
                        remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>user/validateEmail",
                            type: "get",
                            dataType: 'json'
                        }*/
                    },
                    "pin": {
                        required: true,
                        number: true,
                        remote: {url: "<?php echo sfConfig::get("app_admin_url"); ?>user/validatePin",
                            type: "get",
                            dataType: 'json'
                        }
                    },
                    "pos_user_role_id": {
                        required: function() {
                            return (!(jQuery("#branch-select option:selected").val() == ''));
                        }
                    }

                }, messages: {
                    "email": {
                        remote: "<?php echo __("Email already exist."); ?>"
                    },
                            "pin": {
                        remote: "<?php echo __("Pin already exist."); ?>"
                    }
                }
            });

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

                }

            });
<?php
if ($redirect_shop_id > 0) {
    ?>

                console.log($("#branch-select").val());
                jQuery("#branch-select").val('<?php echo $redirect_shop_id; ?>').trigger("change");


<?php } ?>
        });
    </script>
    <form action="<?php echo url_for('user/createUser') ?>" method="post" id="frmUser">
        <div  class="backimgDiv">

            <?php if ($redirect_shop_id != "" && $redirect_shop_id > 0) { ?>

                <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>shops/view?id=<?php echo $redirect_shop_id; ?>'" value="Cancel" class="btn btn-cancel" />
            <?php } else {
                ?>

                <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>user/index';" value="Cancel" class="btn btn-cancel"/>

            <?php } ?>

            <input type="submit" value="Save" class="btn btn-primary" />


            <input type="hidden" value="<?php echo $redirect_shop_id; ?>" name="redirect_shop_id" />
        </div>
        <div class="regForm listviewpadding"><br />
            <?php if ($sf_user->hasFlash('access_error')): ?>
                <div class="alert alert-warning">
                    <?php echo $sf_user->getFlash('access_error') ?>
                </div>
            <?php endif; ?>
            <input type="hidden" name="user[updated_by]" value="<?php echo $user_id; ?>" />
            <div class="lblleft">Name :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="username" value="" class="form-control" required="required" />
                </div>
            </div> 
            <div class="lblleft">Sur Name:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="sur_name" value="" class="form-control" required="required" />
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft">Address:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <textarea id="user_address" required="required" class="form-control" name="address" cols="30" rows="4"></textarea>
                </div> 
            </div>
            <div class="lblleft">Zip:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="zip" value="" class="form-control" required="required" />
                </div>
            </div> 

            <div class="lblleft">City:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="city" value="" class="form-control" required="required" /></div>
            </div> 
            <br clear="all" />
            <div class="lblleft">Country:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="country" value="" class="form-control" required="required" />        
                </div>
            </div> 

            <div class="lblleft">Tel:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="tel" value="" class="form-control" required="required" />
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft">Mobile:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="mobile" value="" class="form-control" required="required" />
                </div>
            </div> 

            <div class="lblleft">Pin:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="pin" value="" class="form-control digits" required="required" />      
                </div>
            </div> 
            <br clear="all" />
            <div class="lblleft">Password:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="password" value="" class="form-control" required="required" />
                </div>
            </div> 

            <div class="lblleft">Email:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="text" name="email" value="" class="form-control" required="required" />  
                </div>
            </div> 
            <br clear="all" />
            <?php
            if ($user_role == 1) {
                ?> 
                <div class="lblleft">User Web Role:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <select name="role_id" class="form-control" >
                            <option value="">Please Select</option>
                            <?php foreach ($roles as $role) { ?>
                                <option value="<?php echo $role->getId(); ?>"><?php echo $role->getName(); ?></option>
                            <?php } ?>

                        </select>
                    </div>
                </div> 

                <div class="lblleft">User POS Role:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <select name="pos_user_role_id" class="form-control"  id="pos_roles">
                            <option value="">Please select branch first</option>
                        </select>
                <!--        <input type="text" name="pos_user_role_id" value="" class="form-control" required="required" /> -->
                    </div>
                </div> 
                <br clear="all" />
            <?php } else {
                ?>
                <input type="hidden" name="role_id" value="<?php echo $user->getRoleId(); ?>" />
                <input type="hidden" name="pos_user_role_id" value="<?php echo $user->getPosUserRoleId(); ?>" />
            <?php }
            ?>  
            <div class="lblleft addedit">User Branch:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <div class="branchchoice">
                        <select data-placeholder="Choose a Branch..."   name="branches" class="form-control" id="branch-select" >
                            <option value="">Please Select a branch</option>
                            <?php foreach ($shops as $shop) { ?>                       
                                <option value="<?php echo $shop->getId(); ?>"   ><?php echo $shop->getBranchNumber(); ?></option> 
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div> 
            <?php if ($user->getIsSuperUser()): ?>

                <div class="lblleft addedit">Administrator:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <input type="checkbox" name="is_super_user" class="" /> 
                    </div>
                </div> 
                <br clear="all" />
                <div class="lblleft addedit">POS Administrator:&nbsp;</div>
                <div class="lblright">
                    <div class="col-lg-6">
                        <input type="checkbox" name="pos_super_user" class="" />
                    </div>
                </div> 
            <?php endif; ?>
            <br clear="all" />
            <?php //echo $form->renderHiddenFields() ?>          
            <ul class="sf_admin_actions"><li></li></ul>

        </div></form>
</div>
</div>