
<div class="itemslist">
<!--<h1 class="items-head">Items List <button type="button" class="btn btn-xs btn-primary">Add Item</button></h1>-->
<h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/user_over.png'?>" />&nbsp;<span>User Management</span> 
   </h1>
<div  class="backimgDiv"> <a href="<?php echo url_for('user/new', true) ?>" class="btn btn-primary">Add User</a></div>
<div class="regForm listviewpadding"><br />
    <?php if ($sf_user->hasFlash('message')): ?>
    <div class="alert alert-success">
        <?php echo $sf_user->getFlash('message') ?>
    </div>
    <?php endif;?>
    <?php if ($sf_user->hasFlash('err_message')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('err_message') ?>
    </div>
    <?php endif;?>
    <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
<table class="table table-striped">
<thead>
  <tr>
    <th>User Name</th>
    <th>Email</th>
    <th>Password</th>
    <th>Web Role</th>
     <th>POS Role</th>
    <th>Edit</th>
    <th>Delete</th>
  </tr>

</thead>
<tbody>
  <?php   $incrment=1;    ?>
  <?php foreach ($user_list as $user): ?>
    <?php
        if($incrment%2==0){
         $class= 'class="even"';
        }else{
         $class= 'class="odd"';
        }
        ?>
  <tr <?php echo $class;?>>
    <td><?php echo $user->getName(); ?></td>
    <td><?php echo $user->getEmail(); ?></td>
    <td><?php echo $user->getPassword(); ?></td>
    <td><?php echo $user->getRole(); ?></td>
      <td><?php echo $user->getPosUserRole(); ?></td>
    <td><a href="<?php echo url_for('user/edit')?>/id/<?php echo $user->getId()?>"><img src="<?php echo sfConfig::get("app_web_url")?>sf/sf_admin/images/edit_icon.png" /></a></td>
    <td><a href="<?php echo url_for('user/delete')?>/id/<?php echo $user->getId()?>" onclick="return confirm('Are you sure to delete user?');"><img src="<?php echo sfConfig::get("app_web_url")?>sf/sf_admin/images/delete_icon.png" /></a></td>
  </tr>
  <?php   $incrment++;    ?>
  <?php endforeach; ?>
</tbody>

</table>
</div>
</div>