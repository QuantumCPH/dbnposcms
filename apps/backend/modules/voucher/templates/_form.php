<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<form action="<?php echo url_for('voucher/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="<?php echo url_for('voucher/index') ?>">Cancel</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'voucher/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['amount']->renderLabel() ?></th>
        <td>
          <?php echo $form['amount']->renderError() ?>
          <?php echo $form['amount'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['created_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['created_at']->renderError() ?>
          <?php echo $form['created_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['updated_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['updated_at']->renderError() ?>
          <?php echo $form['updated_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['used_amount']->renderLabel() ?></th>
        <td>
          <?php echo $form['used_amount']->renderError() ?>
          <?php echo $form['used_amount'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['created_shop_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['created_shop_id']->renderError() ?>
          <?php echo $form['created_shop_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['used_shop_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['used_shop_id']->renderError() ?>
          <?php echo $form['used_shop_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['created_shop_transaction_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['created_shop_transaction_id']->renderError() ?>
          <?php echo $form['created_shop_transaction_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['used_shop_transaction_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['used_shop_transaction_id']->renderError() ?>
          <?php echo $form['used_shop_transaction_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['parent_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['parent_id']->renderError() ?>
          <?php echo $form['parent_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['shop_created_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['shop_created_at']->renderError() ?>
          <?php echo $form['shop_created_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['shop_updated_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['shop_updated_at']->renderError() ?>
          <?php echo $form['shop_updated_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['shop_used_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['shop_used_at']->renderError() ?>
          <?php echo $form['shop_used_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['is_used']->renderLabel() ?></th>
        <td>
          <?php echo $form['is_used']->renderError() ?>
          <?php echo $form['is_used'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
