<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<form action="<?php echo url_for('bookoutNotes/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="<?php echo url_for('bookoutNotes/index') ?>">Cancel</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'bookoutNotes/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['note_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['note_id']->renderError() ?>
          <?php echo $form['note_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['group_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['group_id']->renderError() ?>
          <?php echo $form['group_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['item_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['item_id']->renderError() ?>
          <?php echo $form['item_id'] ?>
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
        <th><?php echo $form['quantity']->renderLabel() ?></th>
        <td>
          <?php echo $form['quantity']->renderError() ?>
          <?php echo $form['quantity'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['delivery_date']->renderLabel() ?></th>
        <td>
          <?php echo $form['delivery_date']->renderError() ?>
          <?php echo $form['delivery_date'] ?>
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
        <th><?php echo $form['status_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['status_id']->renderError() ?>
          <?php echo $form['status_id'] ?>
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
        <th><?php echo $form['received_quantity']->renderLabel() ?></th>
        <td>
          <?php echo $form['received_quantity']->renderError() ?>
          <?php echo $form['received_quantity'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['comment']->renderLabel() ?></th>
        <td>
          <?php echo $form['comment']->renderError() ?>
          <?php echo $form['comment'] ?>
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
        <th><?php echo $form['shop_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['shop_id']->renderError() ?>
          <?php echo $form['shop_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['is_synced']->renderLabel() ?></th>
        <td>
          <?php echo $form['is_synced']->renderError() ?>
          <?php echo $form['is_synced'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['is_received']->renderLabel() ?></th>
        <td>
          <?php echo $form['is_received']->renderError() ?>
          <?php echo $form['is_received'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['synced_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['synced_at']->renderError() ?>
          <?php echo $form['synced_at'] ?>
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
        <th><?php echo $form['shop_responded_at']->renderLabel() ?></th>
        <td>
          <?php echo $form['shop_responded_at']->renderError() ?>
          <?php echo $form['shop_responded_at'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['updated_by']->renderLabel() ?></th>
        <td>
          <?php echo $form['updated_by']->renderError() ?>
          <?php echo $form['updated_by'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['synced_day_start_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['synced_day_start_id']->renderError() ?>
          <?php echo $form['synced_day_start_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['received_day_start_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['received_day_start_id']->renderError() ?>
          <?php echo $form['received_day_start_id'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
