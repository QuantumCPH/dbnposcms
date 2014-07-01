<?php

/**
 * PosModules form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BasePosModulesForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'title'      => new sfWidgetFormInput(),
      'created_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorPropelChoice(array('model' => 'PosModules', 'column' => 'id', 'required' => false)),
      'title'      => new sfValidatorString(array('max_length' => 250)),
      'created_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('pos_modules[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PosModules';
  }


}
