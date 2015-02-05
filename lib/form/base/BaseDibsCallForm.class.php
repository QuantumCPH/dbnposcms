<?php

/**
 * DibsCall form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseDibsCallForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'callurl'        => new sfWidgetFormTextarea(),
      'created_at'     => new sfWidgetFormDateTime(),
      'decrypted_data' => new sfWidgetFormTextarea(),
      'call_response'  => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorPropelChoice(array('model' => 'DibsCall', 'column' => 'id', 'required' => false)),
      'callurl'        => new sfValidatorString(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'decrypted_data' => new sfValidatorString(array('required' => false)),
      'call_response'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dibs_call[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DibsCall';
  }


}
