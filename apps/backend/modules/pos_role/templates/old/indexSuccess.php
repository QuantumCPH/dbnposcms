
<div class="itemslist">
<!--<h1 class="items-head">Items List <button type="button" class="btn btn-xs btn-primary">Add Item</button></h1>-->
<h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/role_title.png'?>" />&nbsp;<span>Roles </span>
    </h1>
   <div  class="backimgDiv"><a href="<?php echo url_for('role/createRole', true) ?>" class="btn btn-primary">Add Role</a></div>
<div class="regForm listviewpadding"><br />
        <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>
<table class="table table-striped">
<thead>
  <tr>
    <th>Roles</th>
    <th>Edit</th>
  </tr>
</thead>
<tbody>
    <?php   $incrment=1;    ?>
  <?php foreach ($role_list as $role): ?>
    <?php
        if($incrment%2==0){
         $class= 'class="even"';
        }else{
         $class= 'class="odd"';
        }
        ?>
  <tr <?php echo $class;?>>
    <td><?php echo $role->getName(); ?></td>
    <td><a href="editRole/id/<?php echo $role->getId()?>"><img src="<?php echo sfConfig::get("app_web_url")?>sf/sf_admin/images/edit_icon.png" /></a></td>
  </tr>
  <?php   $incrment++;    ?>
  <?php endforeach; ?>
</tbody>

</table>
</div>
</div>