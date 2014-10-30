<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<form action="<?php echo url_for('gcmRequest/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="<?php echo url_for('gcmRequest/index') ?>">Cancel</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'gcmRequest/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
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
        <th><?php echo $form['user_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['user_id']->renderError() ?>
          <?php echo $form['user_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['action_name']->renderLabel() ?></th>
        <td>
          <?php echo $form['action_name']->renderError() ?>
          <?php echo $form['action_name'] ?>
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
        <th><?php echo $form['received_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['received_at']->renderError() ?>
          <?php echo $form['received_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['request_status']->renderLabel() ?></th>
        <td>
          <?php echo $form['request_status']->renderError() ?>
          <?php echo $form['request_status'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['sent_count']->renderLabel() ?></th>
        <td>
          <?php echo $form['sent_count']->renderError() ?>
          <?php echo $form['sent_count'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['receive_count']->renderLabel() ?></th>
        <td>
          <?php echo $form['receive_count']->renderError() ?>
          <?php echo $form['receive_count'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
