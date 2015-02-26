<?php

/**
 * Groups form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseGroupsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'    => new sfWidgetFormInputHidden(),
      'title' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'    => new sfValidatorPropelChoice(array('model' => 'Groups', 'column' => 'id', 'required' => false)),
      'title' => new sfValidatorString(array('max_length' => 45, 'required' => false)),
    ));

    $this->widgetSchema->setNameFormat('groups[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Groups';
  }


}
