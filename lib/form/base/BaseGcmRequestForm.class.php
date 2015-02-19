<?php

/**
 * GcmRequest form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseGcmRequestForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
<<<<<<< HEAD
=======
<<<<<<< HEAD
      'branch_number'  => new sfWidgetFormInput(),
      'branch_name'    => new sfWidgetFormInput(),
      'user'           => new sfWidgetFormInput(),
      'actiion_name'   => new sfWidgetFormInput(),
      'created_at'     => new sfWidgetFormDateTime(),
      'status'         => new sfWidgetFormInput(),
      'sent'           => new sfWidgetFormInput(),
      'recieved'       => new sfWidgetFormInput(),
      'updated_by'     => new sfWidgetFormInput(),
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
      'shop_id'        => new sfWidgetFormInput(),
      'user_id'        => new sfWidgetFormInput(),
      'action_name'    => new sfWidgetFormInput(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'received_at'    => new sfWidgetFormDateTime(),
      'request_status' => new sfWidgetFormInput(),
<<<<<<< HEAD
      'sent_count'     => new sfWidgetFormInput(),
      'receive_count'  => new sfWidgetFormInput(),
=======
      'user_id'        => new sfWidgetFormInput(),
      'received_at'    => new sfWidgetFormDateTime(),
      'receive_count'  => new sfWidgetFormInput(),
      'updated_at'     => new sfWidgetFormDateTime(),
=======
      'shop_id'        => new sfWidgetFormInput(),
      'user_id'        => new sfWidgetFormInput(),
      'action_name'    => new sfWidgetFormInput(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'received_at'    => new sfWidgetFormDateTime(),
      'request_status' => new sfWidgetFormInput(),
      'sent_count'     => new sfWidgetFormInput(),
      'receive_count'  => new sfWidgetFormInput(),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorPropelChoice(array('model' => 'GcmRequest', 'column' => 'id', 'required' => false)),
<<<<<<< HEAD
=======
<<<<<<< HEAD
      'branch_number'  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'branch_name'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'user'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'actiion_name'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'     => new sfValidatorDateTime(array('required' => false)),
      'status'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sent'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'recieved'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_by'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
      'shop_id'        => new sfValidatorInteger(array('required' => false)),
      'user_id'        => new sfValidatorInteger(array('required' => false)),
<<<<<<< HEAD
      'action_name'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'     => new sfValidatorDateTime(array('required' => false)),
      'updated_at'     => new sfValidatorDateTime(),
      'received_at'    => new sfValidatorDateTime(array('required' => false)),
      'request_status' => new sfValidatorInteger(array('required' => false)),
      'sent_count'     => new sfValidatorInteger(array('required' => false)),
      'receive_count'  => new sfValidatorInteger(array('required' => false)),
=======
      'received_at'    => new sfValidatorDateTime(array('required' => false)),
      'receive_count'  => new sfValidatorInteger(array('required' => false)),
      'updated_at'     => new sfValidatorDateTime(array('required' => false)),
=======
      'shop_id'        => new sfValidatorInteger(array('required' => false)),
      'user_id'        => new sfValidatorInteger(array('required' => false)),
      'action_name'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'     => new sfValidatorDateTime(array('required' => false)),
      'updated_at'     => new sfValidatorDateTime(),
      'received_at'    => new sfValidatorDateTime(array('required' => false)),
      'request_status' => new sfValidatorInteger(array('required' => false)),
      'sent_count'     => new sfValidatorInteger(array('required' => false)),
      'receive_count'  => new sfValidatorInteger(array('required' => false)),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
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
