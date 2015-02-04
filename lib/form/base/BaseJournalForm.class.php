<?php

/**
 * Journal form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseJournalForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                 => new sfWidgetFormInputHidden(),
      'shop_id'            => new sfWidgetFormInput(),
      'day_starts_journal' => new sfWidgetFormInput(),
      'journal_id'         => new sfWidgetFormInput(),
      'created_at'         => new sfWidgetFormDateTime(),
      'updated_by'         => new sfWidgetFormInput(),
      'updated_at'         => new sfWidgetFormDateTime(),
      'created_date'       => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'                 => new sfValidatorPropelChoice(array('model' => 'Journal', 'column' => 'id', 'required' => false)),
      'shop_id'            => new sfValidatorInteger(array('required' => false)),
      'day_starts_journal' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'journal_id'         => new sfValidatorInteger(array('required' => false)),
      'created_at'         => new sfValidatorDateTime(array('required' => false)),
      'updated_by'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_at'         => new sfValidatorDateTime(array('required' => false)),
      'created_date'       => new sfValidatorDateTime(array('required' => false)),
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
