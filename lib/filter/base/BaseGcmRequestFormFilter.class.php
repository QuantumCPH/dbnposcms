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
<<<<<<< HEAD
=======
<<<<<<< HEAD
      'branch_number'  => new sfWidgetFormFilterInput(),
      'branch_name'    => new sfWidgetFormFilterInput(),
      'user'           => new sfWidgetFormFilterInput(),
      'actiion_name'   => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'status'         => new sfWidgetFormFilterInput(),
      'sent'           => new sfWidgetFormFilterInput(),
      'recieved'       => new sfWidgetFormFilterInput(),
      'updated_by'     => new sfWidgetFormFilterInput(),
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
      'shop_id'        => new sfWidgetFormFilterInput(),
      'user_id'        => new sfWidgetFormFilterInput(),
      'action_name'    => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'received_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'request_status' => new sfWidgetFormFilterInput(),
<<<<<<< HEAD
      'sent_count'     => new sfWidgetFormFilterInput(),
=======
      'user_id'        => new sfWidgetFormFilterInput(),
      'received_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
      'receive_count'  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
<<<<<<< HEAD
=======
      'branch_number'  => new sfValidatorPass(array('required' => false)),
      'branch_name'    => new sfValidatorPass(array('required' => false)),
      'user'           => new sfValidatorPass(array('required' => false)),
      'actiion_name'   => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'status'         => new sfValidatorPass(array('required' => false)),
      'sent'           => new sfValidatorPass(array('required' => false)),
      'recieved'       => new sfValidatorPass(array('required' => false)),
      'updated_by'     => new sfValidatorPass(array('required' => false)),
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
      'shop_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'user_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
<<<<<<< HEAD
      'action_name'    => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
=======
      'received_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'receive_count'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
=======
      'shop_id'        => new sfWidgetFormFilterInput(),
      'user_id'        => new sfWidgetFormFilterInput(),
      'action_name'    => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'received_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'request_status' => new sfWidgetFormFilterInput(),
      'sent_count'     => new sfWidgetFormFilterInput(),
      'receive_count'  => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'shop_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'user_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'action_name'    => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
      'received_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'request_status' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'sent_count'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'receive_count'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
<<<<<<< HEAD
=======
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
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
<<<<<<< HEAD
=======
<<<<<<< HEAD
      'branch_number'  => 'Text',
      'branch_name'    => 'Text',
      'user'           => 'Text',
      'actiion_name'   => 'Text',
      'created_at'     => 'Date',
      'status'         => 'Text',
      'sent'           => 'Text',
      'recieved'       => 'Text',
      'updated_by'     => 'Text',
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
      'shop_id'        => 'Number',
      'user_id'        => 'Number',
<<<<<<< HEAD
      'action_name'    => 'Text',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
=======
      'received_at'    => 'Date',
      'receive_count'  => 'Number',
      'updated_at'     => 'Date',
=======
      'shop_id'        => 'Number',
      'user_id'        => 'Number',
      'action_name'    => 'Text',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
      'received_at'    => 'Date',
      'request_status' => 'Number',
      'sent_count'     => 'Number',
      'receive_count'  => 'Number',
<<<<<<< HEAD
=======
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    );
  }
}
