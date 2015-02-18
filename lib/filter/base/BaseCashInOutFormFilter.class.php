<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * CashInOut filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseCashInOutFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
<<<<<<< HEAD
      'created_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
=======
      'day_start_id'  => new sfWidgetFormFilterInput(),
      'is_synced'     => new sfWidgetFormFilterInput(),
      'description'   => new sfWidgetFormFilterInput(),
      'amount'        => new sfWidgetFormFilterInput(),
      'created_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
      'updated_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_by'    => new sfWidgetFormFilterInput(),
      'shop_id'       => new sfWidgetFormFilterInput(),
      'cash_inout_id' => new sfWidgetFormFilterInput(),
<<<<<<< HEAD
      'day_start_id'  => new sfWidgetFormFilterInput(),
      'amount'        => new sfWidgetFormFilterInput(),
      'description'   => new sfWidgetFormFilterInput(),
      'is_synced'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'created_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by'    => new sfValidatorPass(array('required' => false)),
      'shop_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'cash_inout_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'day_start_id'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'amount'        => new sfValidatorPass(array('required' => false)),
      'description'   => new sfValidatorPass(array('required' => false)),
      'is_synced'     => new sfValidatorPass(array('required' => false)),
=======
    ));

    $this->setValidators(array(
      'day_start_id'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_synced'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'description'   => new sfValidatorPass(array('required' => false)),
      'amount'        => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'created_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'cash_inout_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
    ));

    $this->widgetSchema->setNameFormat('cash_in_out_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CashInOut';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
<<<<<<< HEAD
      'created_at'    => 'Date',
      'updated_at'    => 'Date',
      'updated_by'    => 'Text',
      'shop_id'       => 'Number',
      'cash_inout_id' => 'Number',
      'day_start_id'  => 'Number',
      'amount'        => 'Text',
      'description'   => 'Text',
      'is_synced'     => 'Text',
=======
      'day_start_id'  => 'Number',
      'is_synced'     => 'Number',
      'description'   => 'Text',
      'amount'        => 'Number',
      'created_at'    => 'Date',
      'updated_at'    => 'Date',
      'updated_by'    => 'Number',
      'shop_id'       => 'Number',
      'cash_inout_id' => 'Number',
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
    );
  }
}
