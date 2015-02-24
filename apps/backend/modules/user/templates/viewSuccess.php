<div class="itemslist">
    <h1 class="items-head list-page" style="padding: 10px 17px 0;">
        <img src="<?php echo sfConfig::get('app_web_url') . 'images/shops_over.png' ?>" />&nbsp;User Detail

    </h1>

    <div class="backimgDiv">
        <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>user/index';" value="Back" class="btn btn-cancel"/>
        <input type="button" onclick="document.location.href = '<?php echo url_for(sfConfig::get("app_admin_url") . 'user/edit?id=' . $user->getId()); ?>';" value="Edit" class="btn btn-primary"/>
    </div>
    <div class="regForm listviewpadding"><br />

        <?php if ($sf_user->hasFlash('message')): ?>
            <div class="alert alert-success">
                <?php echo $sf_user->getFlash('message') ?>
            </div>
        <?php endif; ?>
        <?php if ($sf_user->hasFlash('access_error')): ?>
            <div class="alert alert-warning">
                <?php echo $sf_user->getFlash('access_error') ?>
            </div>
        <?php endif; ?>



        <div class="lblleft">Name :&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $user->getName() ?></div>
        </div> 
        <div class="lblleft">Sur Name:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $user->getSurName(); ?></div>
        </div> 
        <br clear="all" />        
        <div class="lblleft">Address:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $user->getAddress() ?></div>
        </div> 
        <div class="lblleft">Zip:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php
               echo $user->getZip();
                ?></div>
        </div>
        <br clear="all" />         
        <div class="lblleft">City:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $user->getCity() ?></div>
        </div>
        <div class="lblleft">Country:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $user->getCountry(); ?></div>
        </div> 
        <br clear="all" />
        <div class="lblleft">Tel:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $user->getTel() ?></div>
        </div>
        <div class="lblleft">Mobile:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $user->getMobile() ?></div>
        </div> 
        <br clear="all" />        
        <div class="lblleft">Pin:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $user->getPin() ?></div>
        </div>
        <div class="lblleft">Password:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $user->getPassword() ?></div>
        </div> 
        <br clear="all" />        
        <div class="lblleft">Email:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo $user->getEmail() ?></div>
        </div>
        <div class="lblleft">Web Role:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php
                if ($user->getRoleId()) {
                   echo  $user->getRole()->getName();
                }
                ?></div>
           
        </div> 
        <br clear="all" />        
        <div class="lblleft">Web Administrator:&nbsp;</div>
        <div class="lblright">
            <div class="inputValue col-lg-6"><?php echo ($user->getIsSuperUser()) ? "Yes" : "No" ?></div>
        </div>

    </div>
</div>
<br clear="all" /><br />
<h2 >User Branches</h2>
<div style="float:right;" >
    <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>user/assignBranch?user_id=<?php echo $user->getId() ?>';" value="Assign to Branch" class="btn btn-primary"/>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Branch Name</th>
            <th>POS Role</th>
            <th>POS Admin</th>
       <!--    <th>Edit</th>-->
            <th>Delete</th>
        </tr>

    </thead>
    <tbody>
        <?php $incrment = 1; ?>
        <?php foreach ($user_list as $user): ?>
            <?php
            if ($incrment % 2 == 0) {
                $class = 'class="even"';
            } else {
                $class = 'class="odd"';
            }
            ?>
            <tr <?php echo $class; ?>>
                <td><?php echo $user->getShops()->getName(); ?></td>
                <td><?php
                    if ($user->getPosRoleId()) {
                        $posrole = PosRolePeer::retrieveByPK($user->getPosRoleId());
                        if ($posrole)
                            echo $posrole->getName();
                    }
                    ?></td>

                <td><?php echo ($user->getPosSuperUser()) ? "Yes" : "No"; ?></td>
            <!--    <td><a href="<?php echo url_for('user/edit') ?>/id/<?php echo $user->getId() ?>"><img src="<?php echo sfConfig::get("app_web_url") ?>sf/sf_admin/images/edit_icon.png" /></a></td>-->
                <td><a href="<?php echo url_for('shops/shopUserDelete') ?>?userid=<?php echo $user->getUserId() ?>&shopid=<?php echo $user->getShopId(); ?>&return=user" onclick="return confirm('Are you sure to delete user from this shop?');"><img src="<?php echo sfConfig::get("app_web_url") ?>sf/sf_admin/images/delete_icon.png" /></a></td>
            </tr>
            <?php $incrment++; ?>
        <?php endforeach; ?>
    </tbody>

</table>

