<?php

/**
 * DayStartsAttempts form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseDayStartsAttemptsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'total_amount'    => new sfWidgetFormInput(),
      'expected_amount' => new sfWidgetFormInput(),
      'day_start_id'    => new sfWidgetFormInput(),
      'is_synce'        => new sfWidgetFormInput(),
      'created_at'      => new sfWidgetFormDateTime(),
      'updated_by'      => new sfWidgetFormInput(),
      'shop_id'         => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorPropelChoice(array('model' => 'DayStartsAttempts', 'column' => 'id', 'required' => false)),
      'total_amount'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'expected_amount' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'day_start_id'    => new sfValidatorInteger(array('required' => false)),
      'is_synce'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'      => new sfValidatorDateTime(),
      'updated_by'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'shop_id'         => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('day_starts_attempts[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DayStartsAttempts';
  }


}
