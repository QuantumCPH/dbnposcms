<?php

/**
 * SmsApi form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseSmsApiForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'       => new sfWidgetFormInputHidden(),
      'name'     => new sfWidgetFormInput(),
      'status'   => new sfWidgetFormInputCheckbox(),
      'priority' => new sfWidgetFormInput(),
      'detail'   => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'       => new sfValidatorPropelChoice(array('model' => 'SmsApi', 'column' => 'id', 'required' => false)),
      'name'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'   => new sfValidatorBoolean(array('required' => false)),
      'priority' => new sfValidatorInteger(array('required' => false)),
      'detail'   => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sms_api[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SmsApi';
  }


}
