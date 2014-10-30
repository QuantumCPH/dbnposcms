<?php

/**
 * Journal form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseJournalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'shop_id'      => new sfWidgetFormInput(),
      'updated_at'   => new sfWidgetFormDateTime(),
      'created_at'   => new sfWidgetFormDateTime(),
      'created_date' => new sfWidgetFormInput(),
      'updated_by'   => new sfWidgetFormInput(),
      'journal_id'   => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorPropelChoice(array('model' => 'Journal', 'column' => 'id', 'required' => false)),
      'shop_id'      => new sfValidatorInteger(array('required' => false)),
      'updated_at'   => new sfValidatorDateTime(),
      'created_at'   => new sfValidatorDateTime(array('required' => false)),
      'created_date' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_by'   => new sfValidatorInteger(array('required' => false)),
      'journal_id'   => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('journal[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Journal';
  }


}
