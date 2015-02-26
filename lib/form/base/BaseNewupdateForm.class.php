<?php

/**
 * Newupdate form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseNewupdateForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'            => new sfWidgetFormInputHidden(),
      'created_at'    => new sfWidgetFormDateTime(),
      'message'       => new sfWidgetFormTextarea(),
      'heading'       => new sfWidgetFormInput(),
      'starting_date' => new sfWidgetFormDate(),
      'expire_date'   => new sfWidgetFormDate(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorPropelChoice(array('model' => 'Newupdate', 'column' => 'id', 'required' => false)),
      'created_at'    => new sfValidatorDateTime(),
      'message'       => new sfValidatorString(),
      'heading'       => new sfValidatorString(array('max_length' => 50)),
      'starting_date' => new sfValidatorDate(),
      'expire_date'   => new sfValidatorDate(),
    ));

    $this->widgetSchema->setNameFormat('newupdate[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Newupdate';
  }


}
