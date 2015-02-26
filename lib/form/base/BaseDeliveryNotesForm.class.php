<?php

/**
 * DeliveryNotes form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseDeliveryNotesForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                    => new sfWidgetFormInputHidden(),
      'note_id'               => new sfWidgetFormInput(),
      'group_id'              => new sfWidgetFormInput(),
      'item_id'               => new sfWidgetFormInput(),
      'branch_number'         => new sfWidgetFormInput(),
      'company_number'        => new sfWidgetFormInput(),
      'quantity'              => new sfWidgetFormInput(),
      'delivery_date'         => new sfWidgetFormDateTime(),
      'created_at'            => new sfWidgetFormDateTime(),
      'status_id'             => new sfWidgetFormInput(),
      'received_at'           => new sfWidgetFormDateTime(),
      'received_quantity'     => new sfWidgetFormInput(),
      'comment'               => new sfWidgetFormInput(),
      'user_id'               => new sfWidgetFormInput(),
      'shop_id'               => new sfWidgetFormInput(),
      'is_synced'             => new sfWidgetFormInputCheckbox(),
      'is_received'           => new sfWidgetFormInputCheckbox(),
      'synced_at'             => new sfWidgetFormDateTime(),
      'updated_at'            => new sfWidgetFormDateTime(),
      'shop_responded_at'     => new sfWidgetFormDateTime(),
      'updated_by'            => new sfWidgetFormInput(),
      'synced_day_start_id'   => new sfWidgetFormInput(),
      'received_day_start_id' => new sfWidgetFormInput(),
      'delivery_note_type_id' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                    => new sfValidatorPropelChoice(array('model' => 'DeliveryNotes', 'column' => 'id', 'required' => false)),
      'note_id'               => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'group_id'              => new sfValidatorInteger(array('required' => false)),
      'item_id'               => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'branch_number'         => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'company_number'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'quantity'              => new sfValidatorInteger(array('required' => false)),
      'delivery_date'         => new sfValidatorDateTime(array('required' => false)),
      'created_at'            => new sfValidatorDateTime(array('required' => false)),
      'status_id'             => new sfValidatorInteger(array('required' => false)),
      'received_at'           => new sfValidatorDateTime(array('required' => false)),
      'received_quantity'     => new sfValidatorInteger(array('required' => false)),
      'comment'               => new sfValidatorString(array('max_length' => 500, 'required' => false)),
      'user_id'               => new sfValidatorInteger(array('required' => false)),
      'shop_id'               => new sfValidatorInteger(array('required' => false)),
      'is_synced'             => new sfValidatorBoolean(array('required' => false)),
      'is_received'           => new sfValidatorBoolean(array('required' => false)),
      'synced_at'             => new sfValidatorDateTime(array('required' => false)),
      'updated_at'            => new sfValidatorDateTime(array('required' => false)),
      'shop_responded_at'     => new sfValidatorDateTime(array('required' => false)),
      'updated_by'            => new sfValidatorInteger(array('required' => false)),
      'synced_day_start_id'   => new sfValidatorInteger(array('required' => false)),
      'received_day_start_id' => new sfValidatorInteger(array('required' => false)),
      'delivery_note_type_id' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('delivery_notes[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DeliveryNotes';
  }


}
