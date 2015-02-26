<?php

/**
 * ReturnNotes form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseReturnNotesForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'item_id'    => new sfWidgetFormInput(),
      'quantity'   => new sfWidgetFormInput(),
      'created_at' => new sfWidgetFormDateTime(),
      'status_id'  => new sfWidgetFormInput(),
      'comment'    => new sfWidgetFormInput(),
      'user_id'    => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorPropelChoice(array('model' => 'ReturnNotes', 'column' => 'id', 'required' => false)),
      'item_id'    => new sfValidatorInteger(array('required' => false)),
      'quantity'   => new sfValidatorInteger(array('required' => false)),
      'created_at' => new sfValidatorDateTime(array('required' => false)),
      'status_id'  => new sfValidatorInteger(array('required' => false)),
      'comment'    => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'user_id'    => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('return_notes[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ReturnNotes';
  }


}
