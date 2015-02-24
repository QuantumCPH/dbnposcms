<?php include_stylesheets_for_form($form) ?>
<?php include_javascripts_for_form($form) ?>

<form action="<?php echo url_for('cron_jobs/'.($form->getObject()->isNew() ? 'create' : 'update').(!$form->getObject()->isNew() ? '?id='.$form->getObject()->getId() : '')) ?>" method="post" <?php $form->isMultipart() and print 'enctype="multipart/form-data" ' ?>>
<?php if (!$form->getObject()->isNew()): ?>
<input type="hidden" name="sf_method" value="put" />
<?php endif; ?>
  <table>
    <tfoot>
      <tr>
        <td colspan="2">
          <?php echo $form->renderHiddenFields() ?>
          &nbsp;<a href="<?php echo url_for('cron_jobs/index') ?>">Cancel</a>
          <?php if (!$form->getObject()->isNew()): ?>
            &nbsp;<?php echo link_to('Delete', 'cron_jobs/delete?id='.$form->getObject()->getId(), array('method' => 'delete', 'confirm' => 'Are you sure?')) ?>
          <?php endif; ?>
          <input type="submit" value="Save" />
        </td>
      </tr>
    </tfoot>
    <tbody>
      <?php echo $form->renderGlobalErrors() ?>
      <tr>
        <th><?php echo $form['custom_minutes']->renderLabel() ?></th>
        <td>
          <?php echo $form['custom_minutes']->renderError() ?>
          <?php echo $form['custom_minutes'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['minutes']->renderLabel() ?></th>
        <td>
          <?php echo $form['minutes']->renderError() ?>
          <?php echo $form['minutes'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['hours']->renderLabel() ?></th>
        <td>
          <?php echo $form['hours']->renderError() ?>
          <?php echo $form['hours'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['custom_hours']->renderLabel() ?></th>
        <td>
          <?php echo $form['custom_hours']->renderError() ?>
          <?php echo $form['custom_hours'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['days']->renderLabel() ?></th>
        <td>
          <?php echo $form['days']->renderError() ?>
          <?php echo $form['days'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['custom_days']->renderLabel() ?></th>
        <td>
          <?php echo $form['custom_days']->renderError() ?>
          <?php echo $form['custom_days'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['months']->renderLabel() ?></th>
        <td>
          <?php echo $form['months']->renderError() ?>
          <?php echo $form['months'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['custom_months']->renderLabel() ?></th>
        <td>
          <?php echo $form['custom_months']->renderError() ?>
          <?php echo $form['custom_months'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['weekdays']->renderLabel() ?></th>
        <td>
          <?php echo $form['weekdays']->renderError() ?>
          <?php echo $form['weekdays'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['custom_weekdays']->renderLabel() ?></th>
        <td>
          <?php echo $form['custom_weekdays']->renderError() ?>
          <?php echo $form['custom_weekdays'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['cron_type_id']->renderLabel() ?></th>
        <td>
          <?php echo $form['cron_type_id']->renderError() ?>
          <?php echo $form['cron_type_id'] ?>
        </td>
      </tr>
      <tr>
        <th><?php echo $form['job']->renderLabel() ?></th>
        <td>
          <?php echo $form['job']->renderError() ?>
          <?php echo $form['job'] ?>
        </td>
      </tr>
    </tbody>
  </table>
</form>
