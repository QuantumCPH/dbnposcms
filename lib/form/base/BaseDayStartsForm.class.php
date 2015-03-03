<?php

/**
 * DayStarts form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseDayStartsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'day_started_at'  => new sfWidgetFormDateTime(),
      'day_started_by'  => new sfWidgetFormInput(),
      'shop_id'         => new sfWidgetFormInput(),
      'is_day_closed'   => new sfWidgetFormInputCheckbox(),
      'created_at'      => new sfWidgetFormDateTime(),
      'total_amount'    => new sfWidgetFormInput(),
      'success'         => new sfWidgetFormInputCheckbox(),
      'expected_amount' => new sfWidgetFormInput(),
      'journal_id'      => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorPropelChoice(array('model' => 'DayStarts', 'column' => 'id', 'required' => false)),
      'day_started_at'  => new sfValidatorDateTime(array('required' => false)),
      'day_started_by'  => new sfValidatorInteger(array('required' => false)),
      'shop_id'         => new sfValidatorInteger(array('required' => false)),
      'is_day_closed'   => new sfValidatorBoolean(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(array('required' => false)),
      'total_amount'    => new sfValidatorNumber(array('required' => false)),
      'success'         => new sfValidatorBoolean(array('required' => false)),
      'expected_amount' => new sfValidatorNumber(array('required' => false)),
      'journal_id'      => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('day_starts[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DayStarts';
  }


}
