<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<form action="<?php echo url_for('transactions/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="<?php echo url_for('transactions/index') ?>">Cancel</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'transactions/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['transaction_type_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['transaction_type_id']->renderError() ?>
          <?php echo $form['transaction_type_id'] ?>
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
        <th><?php echo $form['item_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['item_id']->renderError() ?>
          <?php echo $form['item_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['shop_receipt_number_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['shop_receipt_number_id']->renderError() ?>
          <?php echo $form['shop_receipt_number_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['shop_order_number_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['shop_order_number_id']->renderError() ?>
          <?php echo $form['shop_order_number_id'] ?>
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
        <th><?php echo $form['parent_type']->renderLabel() ?></th>
        <td>
          <?php echo $form['parent_type']->renderError() ?>
          <?php echo $form['parent_type'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['parent_type_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['parent_type_id']->renderError() ?>
          <?php echo $form['parent_type_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['sold_price']->renderLabel() ?></th>
        <td>
          <?php echo $form['sold_price']->renderError() ?>
          <?php echo $form['sold_price'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['discount_value']->renderLabel() ?></th>
        <td>
          <?php echo $form['discount_value']->renderError() ?>
          <?php echo $form['discount_value'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['discount_type_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['discount_type_id']->renderError() ?>
          <?php echo $form['discount_type_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['shop_transaction_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['shop_transaction_id']->renderError() ?>
          <?php echo $form['shop_transaction_id'] ?>
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
        <th><?php echo $form['description1']->renderLabel() ?></th>
        <td>
          <?php echo $form['description1']->renderError() ?>
          <?php echo $form['description1'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['description2']->renderLabel() ?></th>
        <td>
          <?php echo $form['description2']->renderError() ?>
          <?php echo $form['description2'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['description3']->renderLabel() ?></th>
        <td>
          <?php echo $form['description3']->renderError() ?>
          <?php echo $form['description3'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['supplier_number']->renderLabel() ?></th>
        <td>
          <?php echo $form['supplier_number']->renderError() ?>
          <?php echo $form['supplier_number'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['supplier_item_number']->renderLabel() ?></th>
        <td>
          <?php echo $form['supplier_item_number']->renderError() ?>
          <?php echo $form['supplier_item_number'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['ean']->renderLabel() ?></th>
        <td>
          <?php echo $form['ean']->renderError() ?>
          <?php echo $form['ean'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['group']->renderLabel() ?></th>
        <td>
          <?php echo $form['group']->renderError() ?>
          <?php echo $form['group'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['color']->renderLabel() ?></th>
        <td>
          <?php echo $form['color']->renderError() ?>
          <?php echo $form['color'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['size']->renderLabel() ?></th>
        <td>
          <?php echo $form['size']->renderError() ?>
          <?php echo $form['size'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['buying_price']->renderLabel() ?></th>
        <td>
          <?php echo $form['buying_price']->renderError() ?>
          <?php echo $form['buying_price'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['selling_price']->renderLabel() ?></th>
        <td>
          <?php echo $form['selling_price']->renderError() ?>
          <?php echo $form['selling_price'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['taxation_code']->renderLabel() ?></th>
        <td>
          <?php echo $form['taxation_code']->renderError() ?>
          <?php echo $form['taxation_code'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['down_sync']->renderLabel() ?></th>
        <td>
          <?php echo $form['down_sync']->renderError() ?>
          <?php echo $form['down_sync'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['user_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['user_id']->renderError() ?>
          <?php echo $form['user_id'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
