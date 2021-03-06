<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * DayStartDenominations filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseDayStartDenominationsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'denomination_id' => new sfWidgetFormFilterInput(),
      'day_start_id'    => new sfWidgetFormFilterInput(),
      'count'           => new sfWidgetFormFilterInput(),
      'amount'          => new sfWidgetFormFilterInput(),
      'day_attempt_id'  => new sfWidgetFormFilterInput(),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
    ));

    $this->setValidators(array(
      'denomination_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'day_start_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'count'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'amount'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'day_attempt_id'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
    ));

    $this->widgetSchema->setNameFormat('day_start_denominations_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DayStartDenominations';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'denomination_id' => 'Number',
      'day_start_id'    => 'Number',
      'count'           => 'Number',
      'amount'          => 'Number',
      'day_attempt_id'  => 'Number',
      'created_at'      => 'Date',
    );
  }
}
