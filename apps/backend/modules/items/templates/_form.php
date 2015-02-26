<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>
<script type="text/javascript">
 
    $(document).ready(function() {
        $(":file").filestyle({classButton: "btn btn-primary",classInput: "filenamefld"});
        
   } );
</script>
<form action="<?php echo url_for('items/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<div  class="backimgDiv">
          <input type="button" onclick="document.location.href='<?php echo $cancel_url //sfConfig::get("app_admin_url")."shops/view/id/".$shop->getId();?>'" value="Cancel" class="btn btn-cancel"/>
          <input type="submit" value="Update" class="btn btn-primary" />
      </div>
 <div class="regForm listviewpadding"><br />
     <?php if ($sf_user->hasFlash('access_error')): ?>
    <div class="alert alert-warning">
        <?php echo $sf_user->getFlash('access_error') ?>
    </div>
    <?php endif;?>

    <?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
<?php echo $form->renderGlobalErrors() ?>
     <div class="itemsfields">  
        <div class="left"><?php echo $form['description1']->renderLabel() ?> :&nbsp;</div>
    <div class="rightDesc">
            <div class="inputValueNobdr"><?php echo $form['description1']->renderError() ?>
          <?php echo $form['description1']->render(array("class"=>"form-control required")); ?>
        </div>
    </div> 
    <div class="left"><?php echo $form['description2']->renderLabel() ?> :&nbsp;</div>
    <div class="rightDesc">
        <div class="inputValueNobdr"><?php echo $form['description2']->renderError() ?>
          <?php echo $form['description2']->render(array("class"=>"form-control required")); ?>
        </div>
    </div> 
    <div class="left"><?php echo $form['description3']->renderLabel() ?> :&nbsp;</div>
    <div class="rightDesc">
        <div class="inputValueNobdr"><?php echo $form['description3']->renderError() ?>
          <?php echo $form['description2']->render(array("class"=>"form-control required")); ?>
        </div>
    </div> 
    <br clear="all" />
    <div class="left"><?php echo $form['supplier_number']->renderLabel("Supplier No.") ?> :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr"><?php echo $form['supplier_number']->renderError() ?>
          <?php echo $form['supplier_number']->render(array("class"=>"form-control required")); ?>
        </div>
    </div> 
    <div class="left"><?php echo $form['supplier_item_number']->renderLabel("Sup Item No.") ?> :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr"><?php echo $form['supplier_item_number']->renderError() ?>
          <?php echo $form['supplier_item_number']->render(array("class"=>"form-control required")); ?>
        </div>
    </div>
    <br clear="all" />
    <div class="left"><?php echo $form['ean']->renderLabel() ?> :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr"><?php echo $form['ean']->renderError() ?>
          <?php echo $form['ean']->render(array("class"=>"form-control required")); ?>
        </div>
    </div> 
    <div class="left"><?php echo $form['group']->renderLabel() ?> :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr"><?php echo $form['group']->renderError() ?>
          <?php echo $form['group']->render(array("class"=>"form-control required")); ?>
        </div>
    </div>
    <br clear="all" />
    <div class="left"><?php echo $form['color']->renderLabel() ?> :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr"><?php echo $form['color']->renderError() ?>
          <?php echo $form['color']->render(array("class"=>"form-control required")); ?>
        </div>
    </div> 
    <div class="left"><?php echo $form['size']->renderLabel() ?> :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr"><?php echo $form['size']->renderError() ?>
          <?php echo $form['size']->render(array("class"=>"form-control required")); ?>
        </div>
    </div>
    <br clear="all" />
    <div class="left"><?php echo $form['buying_price']->renderLabel() ?> :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr"><?php echo $form['buying_price']->renderError() ?>
          <?php echo $form['buying_price']->render(array("class"=>"form-control required")); ?>
        </div>
    </div> 
    <div class="left"><?php echo $form['selling_price']->renderLabel() ?> :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr"><?php echo $form['selling_price']->renderError() ?>
          <?php echo $form['selling_price']->render(array("class"=>"form-control required")); ?>
        </div>
    </div>
    <br clear="all" />
    <div class="left"><?php echo $form['taxation_code']->renderLabel("Tax Code") ?> :&nbsp;</div>
    <div class="right">
        <div class="inputValueNobdr"><?php echo $form['taxation_code']->renderError() ?>
          <?php echo $form['taxation_code']->render(array("class"=>"form-control required")); ?>
        </div>
    </div>     
    <br clear="all" />
    <div class="left">Image :&nbsp;</div>
    <div class="rightDesc">
        <div class="inputValueNobdr">
            <input type="file" name="itempic" class="btn btn-default" />
        </div>
    </div>    
    <br clear="all" />  
 </div><div class="itemslargePic">
        <img src="<?php  echo gf::checkLargeImage($item->getItemId());  ?>" />
    </div>
 </div>
</form>
