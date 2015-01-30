<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * GcmRequest filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseGcmRequestFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'branch_number'  => new sfWidgetFormFilterInput(),
      'branch_name'    => new sfWidgetFormFilterInput(),
      'user'           => new sfWidgetFormFilterInput(),
      'actiion_name'   => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterInput(),
      'status'         => new sfWidgetFormFilterInput(),
      'sent'           => new sfWidgetFormFilterInput(),
      'recieved'       => new sfWidgetFormFilterInput(),
      'updaed_at'      => new sfWidgetFormFilterInput(),
      'updated_by'     => new sfWidgetFormFilterInput(),
      'shop_id'        => new sfWidgetFormFilterInput(),
      'action_name'    => new sfWidgetFormFilterInput(),
      'sent_count'     => new sfWidgetFormFilterInput(),
      'request_status' => new sfWidgetFormFilterInput(),
      'user_id'        => new sfWidgetFormFilterInput(),
      'received_at'    => new sfWidgetFormFilterInput(),
      'receive_count'  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'branch_number'  => new sfValidatorPass(array('required' => false)),
      'branch_name'    => new sfValidatorPass(array('required' => false)),
      'user'           => new sfValidatorPass(array('required' => false)),
      'actiion_name'   => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorPass(array('required' => false)),
      'status'         => new sfValidatorPass(array('required' => false)),
      'sent'           => new sfValidatorPass(array('required' => false)),
      'recieved'       => new sfValidatorPass(array('required' => false)),
      'updaed_at'      => new sfValidatorPass(array('required' => false)),
      'updated_by'     => new sfValidatorPass(array('required' => false)),
      'shop_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'action_name'    => new sfValidatorPass(array('required' => false)),
      'sent_count'     => new sfValidatorPass(array('required' => false)),
      'request_status' => new sfValidatorPass(array('required' => false)),
      'user_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'received_at'    => new sfValidatorPass(array('required' => false)),
      'receive_count'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('gcm_request_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'GcmRequest';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'branch_number'  => 'Text',
      'branch_name'    => 'Text',
      'user'           => 'Text',
      'actiion_name'   => 'Text',
      'created_at'     => 'Text',
      'status'         => 'Text',
      'sent'           => 'Text',
      'recieved'       => 'Text',
      'updaed_at'      => 'Text',
      'updated_by'     => 'Text',
      'shop_id'        => 'Number',
      'action_name'    => 'Text',
      'sent_count'     => 'Text',
      'request_status' => 'Text',
      'user_id'        => 'Number',
      'received_at'    => 'Text',
      'receive_count'  => 'Number',
    );
  }
}
