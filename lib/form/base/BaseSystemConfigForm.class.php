<?php

/**
 * SystemConfig form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseSystemConfigForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'     => new sfWidgetFormInputHidden(),
      'keys'   => new sfWidgetFormInput(),
      'values' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'     => new sfValidatorPropelChoice(array('model' => 'SystemConfig', 'column' => 'id', 'required' => false)),
      'keys'   => new sfValidatorString(array('max_length' => 255)),
      'values' => new sfValidatorString(array('max_length' => 50)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorPropelUnique(array('model' => 'SystemConfig', 'column' => array('keys')))
    );

    $this->widgetSchema->setNameFormat('system_config[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SystemConfig';
  }


}
