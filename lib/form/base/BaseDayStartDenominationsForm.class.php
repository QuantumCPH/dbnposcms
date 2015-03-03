<?php

/**
 * DayStartDenominations form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseDayStartDenominationsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'denomination_id' => new sfWidgetFormInput(),
      'day_start_id'    => new sfWidgetFormInput(),
      'count'           => new sfWidgetFormInput(),
      'amount'          => new sfWidgetFormInput(),
      'day_attempt_id'  => new sfWidgetFormInput(),
      'created_at'      => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorPropelChoice(array('model' => 'DayStartDenominations', 'column' => 'id', 'required' => false)),
      'denomination_id' => new sfValidatorInteger(array('required' => false)),
      'day_start_id'    => new sfValidatorInteger(array('required' => false)),
      'count'           => new sfValidatorInteger(array('required' => false)),
      'amount'          => new sfValidatorNumber(array('required' => false)),
      'day_attempt_id'  => new sfValidatorInteger(array('required' => false)),
      'created_at'      => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('day_start_denominations[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DayStartDenominations';
  }


}
