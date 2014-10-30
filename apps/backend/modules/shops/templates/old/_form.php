<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<form action="<?php echo url_for('shops/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="<?php echo url_for('shops/index') ?>">Cancel</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'shops/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['name']->renderLabel() ?></th>
        <td>
          <?php echo $form['name']->renderError() ?>
          <?php echo $form['name'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['branch_number']->renderLabel() ?></th>
        <td>
          <?php echo $form['branch_number']->renderError() ?>
          <?php echo $form['branch_number'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['company_number']->renderLabel() ?></th>
        <td>
          <?php echo $form['company_number']->renderError() ?>
          <?php echo $form['company_number'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['is_configured']->renderLabel() ?></th>
        <td>
          <?php echo $form['is_configured']->renderError() ?>
          <?php echo $form['is_configured'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['created_by']->renderLabel() ?></th>
        <td>
          <?php echo $form['created_by']->renderError() ?>
          <?php echo $form['created_by'] ?>
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
        <th><?php echo $form['configured_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['configured_at']->renderError() ?>
          <?php echo $form['configured_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['first_login']->renderLabel() ?></th>
        <td>
          <?php echo $form['first_login']->renderError() ?>
          <?php echo $form['first_login'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['password']->renderLabel() ?></th>
        <td>
          <?php echo $form['password']->renderError() ?>
          <?php echo $form['password'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['status_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['status_id']->renderError() ?>
          <?php echo $form['status_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['address']->renderLabel() ?></th>
        <td>
          <?php echo $form['address']->renderError() ?>
          <?php echo $form['address'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['zip']->renderLabel() ?></th>
        <td>
          <?php echo $form['zip']->renderError() ?>
          <?php echo $form['zip'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['place']->renderLabel() ?></th>
        <td>
          <?php echo $form['place']->renderError() ?>
          <?php echo $form['place'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['country']->renderLabel() ?></th>
        <td>
          <?php echo $form['country']->renderError() ?>
          <?php echo $form['country'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['tel']->renderLabel() ?></th>
        <td>
          <?php echo $form['tel']->renderError() ?>
          <?php echo $form['tel'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['fax']->renderLabel() ?></th>
        <td>
          <?php echo $form['fax']->renderError() ?>
          <?php echo $form['fax'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['item_sync_requested_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['item_sync_requested_at']->renderError() ?>
          <?php echo $form['item_sync_requested_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['item_sync_synced_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['item_sync_synced_at']->renderError() ?>
          <?php echo $form['item_sync_synced_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['pic_sync_requested_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['pic_sync_requested_at']->renderError() ?>
          <?php echo $form['pic_sync_requested_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['pic_sync_synced_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['pic_sync_synced_at']->renderError() ?>
          <?php echo $form['pic_sync_synced_at'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
