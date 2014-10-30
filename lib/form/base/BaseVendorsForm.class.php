<?php

/**
 * Vendors form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseVendorsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'title'      => new sfWidgetFormInput(),
      'logo'       => new sfWidgetFormInput(),
      'status'     => new sfWidgetFormInputCheckbox(),
      'created_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorPropelChoice(array('model' => 'Vendors', 'column' => 'id', 'required' => false)),
      'title'      => new sfValidatorString(array('max_length' => 100)),
      'logo'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'     => new sfValidatorBoolean(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('vendors[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Vendors';
  }


}
