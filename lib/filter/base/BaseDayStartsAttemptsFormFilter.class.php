<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * DayStartsAttempts filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseDayStartsAttemptsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'total_amount'    => new sfWidgetFormFilterInput(),
      'expected_amount' => new sfWidgetFormFilterInput(),
      'day_start_id'    => new sfWidgetFormFilterInput(),
      'is_synce'        => new sfWidgetFormFilterInput(),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_by'      => new sfWidgetFormFilterInput(),
      'shop_id'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'total_amount'    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'expected_amount' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'day_start_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_synce'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('day_starts_attempts_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DayStartsAttempts';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'total_amount'    => 'Number',
      'expected_amount' => 'Number',
      'day_start_id'    => 'Number',
      'is_synce'        => 'Number',
      'created_at'      => 'Date',
      'updated_by'      => 'Number',
      'shop_id'         => 'Number',
    );
  }
}
