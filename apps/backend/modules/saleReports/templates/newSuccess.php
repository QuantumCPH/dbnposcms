<h1>New SaleReports</h1>

<?php include_partial('form', array('form' => $form)) ?>


<form action="<?php echo url_for('saleReports/create') ?>" method="post" id="frmUser">
        <div  class="backimgDiv">

            <?php if ($redirect_shop_id != "" && $redirect_shop_id > 0) { ?>

                <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>saleReports/view?id=<?php echo $redirect_shop_id; ?>'" value="Cancel" class="btn btn-cancel" />
            <?php } else {
                ?>

                <input type="button" onclick="document.location.href = '<?php echo sfConfig::get("app_admin_url"); ?>saleReports/index';" value="Cancel" class="btn btn-cancel"/>

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
            <div class="lblleft">Report Date From :&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="date" name="report_date_from" value="" class="form-control" required="required" />
                </div>
            </div> 
            <div class="lblleft">Report Date To:&nbsp;</div>
            <div class="lblright">
                <div class="col-lg-6">
                    <input type="date" name="report_date_to" value="" class="form-control" required="required" />
                </div>
            </div> 
            <br clear="all" />
           
            <br clear="all" />
            <?php //echo $form->renderHiddenFields() ?>          
            <ul class="sf_admin_actions"><li></li></ul>

        </div></form>