<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * GcmRequest filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
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
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'status'         => new sfWidgetFormFilterInput(),
      'sent'           => new sfWidgetFormFilterInput(),
      'recieved'       => new sfWidgetFormFilterInput(),
      'updated_by'     => new sfWidgetFormFilterInput(),
      'shop_id'        => new sfWidgetFormFilterInput(),
      'action_name'    => new sfWidgetFormFilterInput(),
      'sent_count'     => new sfWidgetFormFilterInput(),
      'request_status' => new sfWidgetFormFilterInput(),
      'user_id'        => new sfWidgetFormFilterInput(),
      'received_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'receive_count'  => new sfWidgetFormFilterInput(),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
    ));

    $this->setValidators(array(
      'branch_number'  => new sfValidatorPass(array('required' => false)),
      'branch_name'    => new sfValidatorPass(array('required' => false)),
      'user'           => new sfValidatorPass(array('required' => false)),
      'actiion_name'   => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'status'         => new sfValidatorPass(array('required' => false)),
      'sent'           => new sfValidatorPass(array('required' => false)),
      'recieved'       => new sfValidatorPass(array('required' => false)),
      'updated_by'     => new sfValidatorPass(array('required' => false)),
      'shop_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'action_name'    => new sfValidatorPass(array('required' => false)),
      'sent_count'     => new sfValidatorPass(array('required' => false)),
      'request_status' => new sfValidatorPass(array('required' => false)),
      'user_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'received_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'receive_count'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
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
      'created_at'     => 'Date',
      'status'         => 'Text',
      'sent'           => 'Text',
      'recieved'       => 'Text',
      'updated_by'     => 'Text',
      'shop_id'        => 'Number',
      'action_name'    => 'Text',
      'sent_count'     => 'Text',
      'request_status' => 'Text',
      'user_id'        => 'Number',
      'received_at'    => 'Date',
      'receive_count'  => 'Number',
      'updated_at'     => 'Date',
    );
  }
}
