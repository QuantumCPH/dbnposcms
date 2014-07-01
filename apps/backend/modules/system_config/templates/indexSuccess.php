<div class="itemslist">
 
<h1 class="items-head list-page"><img src="<?php echo sfConfig::get('app_web_url').'images/settings_title.png'?>" />&nbsp;Global Settings
  
</h1>
</div>
<div  class="backimgDiv">
<!--      <input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>system_config/add';" value="Add" class="btn btn-primary"/>-->
  </div>
<div class="regForm listviewpadding"><br />
     <?php if ($sf_user->hasFlash('message')): ?>
    <div class="alert alert-success">
        <?php echo $sf_user->getFlash('message') ?>
    </div>
    <?php endif;?>
    <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?> 
<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="configTable" >
<thead>  
    <tr>
      <th>Keys</th>
      <th>Values</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($system_config_list as $system_config): ?>
    <tr>      
      <td><?php echo $system_config->getKeys() ?></td>
      <td><?php 
      if(strtolower($system_config->getKeys())=="language"){
          $language = LanguagesPeer::retrieveByPK($system_config->getValues());
          if($language){
              echo $language->getTitle();
          }           
      }else{
           echo $system_config->getValues();
      }
      ?></td>
      <td><a href="<?php echo url_for('system_config/edit?id='.$system_config->getId()) ?>"><img src='<?php echo sfConfig::get("app_web_url"); ?>sf/sf_admin/images/edit_icon.png' /></a></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
