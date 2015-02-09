<?php

/**
 * BookoutNotes form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseBookoutNotesForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'bookout_number' => new sfWidgetFormInput(),
      'branch_number'  => new sfWidgetFormInput(),
      'status'         => new sfWidgetFormInput(),
      'synced_at'      => new sfWidgetFormInput(),
      'received_at'    => new sfWidgetFormInput(),
      'action'         => new sfWidgetFormInput(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'updated_by'     => new sfWidgetFormInput(),
      'note_id'        => new sfWidgetFormInput(),
      'status_id'      => new sfWidgetFormInput(),
      'item_id'        => new sfWidgetFormInput(),
      'company_number' => new sfWidgetFormInput(),
      'shop_id'        => new sfWidgetFormInput(),
      'quantity'       => new sfWidgetFormInput(),
      'delivery_date'  => new sfWidgetFormInput(),
      'comment'        => new sfWidgetFormInput(),
      'group_id'       => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorPropelChoice(array('model' => 'BookoutNotes', 'column' => 'id', 'required' => false)),
      'bookout_number' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'branch_number'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'status'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'synced_at'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'received_at'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'action'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'     => new sfValidatorDateTime(array('required' => false)),
      'updated_at'     => new sfValidatorDateTime(array('required' => false)),
      'updated_by'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'note_id'        => new sfValidatorInteger(array('required' => false)),
      'status_id'      => new sfValidatorInteger(array('required' => false)),
      'item_id'        => new sfValidatorInteger(array('required' => false)),
      'company_number' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'shop_id'        => new sfValidatorInteger(array('required' => false)),
      'quantity'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'delivery_date'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'comment'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'group_id'       => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('bookout_notes[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'BookoutNotes';
  }


}
