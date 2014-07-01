<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<form action="<?php echo url_for('inventory/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="<?php echo url_for('inventory/index') ?>">Cancel</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'inventory/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['shop_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['shop_id']->renderError() ?>
          <?php echo $form['shop_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['cms_item_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['cms_item_id']->renderError() ?>
          <?php echo $form['cms_item_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['total']->renderLabel() ?></th>
        <td>
          <?php echo $form['total']->renderError() ?>
          <?php echo $form['total'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['sold']->renderLabel() ?></th>
        <td>
          <?php echo $form['sold']->renderError() ?>
          <?php echo $form['sold'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['book_out']->renderLabel() ?></th>
        <td>
          <?php echo $form['book_out']->renderError() ?>
          <?php echo $form['book_out'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['returned']->renderLabel() ?></th>
        <td>
          <?php echo $form['returned']->renderError() ?>
          <?php echo $form['returned'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['available']->renderLabel() ?></th>
        <td>
          <?php echo $form['available']->renderError() ?>
          <?php echo $form['available'] ?>
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
        <th><?php echo $form['item_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['item_id']->renderError() ?>
          <?php echo $form['item_id'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
