<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<script>
  $().ready(function() {
      $("#frmUser").validate();
  });
</script>
<div class="regForm">
<form action="<?php echo url_for('user/'.($form->getObject()->isNew() ? 'createUser' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?> id="frmUser">
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table width="100%" cellspacing="0" cellpadding="2" class="table table-striped user" border='0'>    
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr class="odd">
        <td><?php echo $form['name']->renderLabel() ?></td>
        <td>
          <div class="col-lg-6">
              <?php echo $form['name']->renderError() ?>
              <?php echo $form['name']->render(array('class'=>"form-control", 'required'=>'required')) ?>
          </div>
        </td>
      </tr>
      <tr class="even">
        <td><?php echo $form['password']->renderLabel() ?></td>
        <td>
          <div class="col-lg-6">
              <?php echo $form['password']->renderError() ?>
              <?php echo $form['password']->render(array('class'=>"form-control", 'required'=>'required')) ?>
          </div>
        </td>
      </tr>
      <tr class="odd">
        <td><?php echo $form['email']->renderLabel() ?></td>
        <td>
          <div class="col-lg-6">
              <?php echo $form['email']->renderError() ?>
              <?php echo $form['email']->render(array('type'=>"email", 'class'=>"form-control", 'required'=>'required')) ?>
          </div>
        </td>
      </tr>  
    <?php 
     // if($user_role ==1){
    ?>  
      <tr class="even">
        <td><?php echo $form['role_id']->renderLabel("Role") ?></td>
        <td>
          <div class="col-lg-6">
              <?php echo $form['role_id']->renderError() ?>
              <?php echo $form['role_id']->render(array('class'=>"form-control", 'required'=>'required')) ?>
          </div>
        </td>
      </tr>
    <?php //} ?>  
    </tbody>
  </table>
<?php echo $form->renderHiddenFields() ?>          
<ul class="sf_admin_actions"><li><input type="button" onclick="document.location.href='<?php echo sfConfig::get("app_admin_url");?>user/index';" value="list" class="btn btn-primary"/>&nbsp;</li><li><input type="submit" value="Save" class="btn btn-primary" /></li></ul>
</form>
</div>