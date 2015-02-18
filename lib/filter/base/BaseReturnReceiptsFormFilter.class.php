<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * ReturnReceipts filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseReturnReceiptsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'shop_id'    => new sfWidgetFormFilterInput(),
<<<<<<< HEAD
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
=======
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
      'updated_by' => new sfWidgetFormFilterInput(),
      'receipt_id' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'shop_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
<<<<<<< HEAD
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by' => new sfValidatorPass(array('required' => false)),
=======
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
      'receipt_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('return_receipts_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ReturnReceipts';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'shop_id'    => 'Number',
<<<<<<< HEAD
      'created_at' => 'Date',
      'updated_at' => 'Date',
      'updated_by' => 'Text',
=======
      'updated_at' => 'Date',
      'created_at' => 'Date',
      'updated_by' => 'Number',
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
      'receipt_id' => 'Number',
    );
  }
}
