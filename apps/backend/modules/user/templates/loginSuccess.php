<?php use_helper('I18N') ?><center>
		<?php if($sf_user->hasFlash('message')): ?>
			<div class="message">
				<?php echo $sf_user->getFlash('message'); ?>
			</div>
		<?php endif; ?>
                 <?php if ($sf_user->hasFlash('role_changed')): ?>
                    <div class="alert alert-warning" style="width:50%;">
                        <?php echo $sf_user->getFlash('role_changed') ?>
                    </div>
                    <?php endif;?>
                   <?php if ($sf_user->hasFlash('reset_message')): ?>
                    <div class="alert alert-success" style="width:50%;">
                        <?php echo $sf_user->getFlash('reset_message') ?>
                    </div>
                    <?php endif;?>
                    <?php include_partial('loginform', array('form' => $loginForm)) ?>
                
               

</center>