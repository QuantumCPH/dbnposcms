<?php

/**
 * GcmRequest form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseGcmRequestForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'branch_number'  => new sfWidgetFormInput(),
      'branch_name'    => new sfWidgetFormInput(),
      'user'           => new sfWidgetFormInput(),
      'actiion_name'   => new sfWidgetFormInput(),
      'created_at'     => new sfWidgetFormDateTime(),
      'status'         => new sfWidgetFormInput(),
      'sent'           => new sfWidgetFormInput(),
      'recieved'       => new sfWidgetFormInput(),
      'updated_by'     => new sfWidgetFormInput(),
      'shop_id'        => new sfWidgetFormInput(),
      'action_name'    => new sfWidgetFormInput(),
      'sent_count'     => new sfWidgetFormInput(),
      'request_status' => new sfWidgetFormInput(),
      'user_id'        => new sfWidgetFormInput(),
      'received_at'    => new sfWidgetFormDateTime(),
      'receive_count'  => new sfWidgetFormInput(),
      'updated_at'     => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorPropelChoice(array('model' => 'GcmRequest', 'column' => 'id', 'required' => false)),
      'branch_number'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'branch_name'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'user'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'actiion_name'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'     => new sfValidatorDateTime(array('required' => false)),
      'status'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sent'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'recieved'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_by'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'shop_id'        => new sfValidatorInteger(array('required' => false)),
      'action_name'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sent_count'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'request_status' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'user_id'        => new sfValidatorInteger(array('required' => false)),
      'received_at'    => new sfValidatorDateTime(array('required' => false)),
      'receive_count'  => new sfValidatorInteger(array('required' => false)),
      'updated_at'     => new sfValidatorDateTime(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('gcm_request[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'GcmRequest';
  }


}
