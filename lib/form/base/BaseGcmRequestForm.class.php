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
      'shop_id'        => new sfWidgetFormInput(),
      'user_id'        => new sfWidgetFormInput(),
      'action_name'    => new sfWidgetFormInput(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'received_at'    => new sfWidgetFormDateTime(),
      'request_status' => new sfWidgetFormInput(),
      'sent_count'     => new sfWidgetFormInput(),
      'receive_count'  => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorPropelChoice(array('model' => 'GcmRequest', 'column' => 'id', 'required' => false)),
      'shop_id'        => new sfValidatorInteger(array('required' => false)),
      'user_id'        => new sfValidatorInteger(array('required' => false)),
      'action_name'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'     => new sfValidatorDateTime(array('required' => false)),
      'updated_at'     => new sfValidatorDateTime(),
      'received_at'    => new sfValidatorDateTime(array('required' => false)),
      'request_status' => new sfValidatorInteger(array('required' => false)),
      'sent_count'     => new sfValidatorInteger(array('required' => false)),
      'receive_count'  => new sfValidatorInteger(array('required' => false)),
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
