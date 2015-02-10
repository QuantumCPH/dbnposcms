<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * BookoutNotes filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseBookoutNotesFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'bookout_number'    => new sfWidgetFormFilterInput(),
      'branch_number'     => new sfWidgetFormFilterInput(),
      'status'            => new sfWidgetFormFilterInput(),
      'synced_at'         => new sfWidgetFormFilterInput(),
      'received_at'       => new sfWidgetFormFilterInput(),
      'action'            => new sfWidgetFormFilterInput(),
      'created_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'        => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_by'        => new sfWidgetFormFilterInput(),
      'note_id'           => new sfWidgetFormFilterInput(),
      'status_id'         => new sfWidgetFormFilterInput(),
      'item_id'           => new sfWidgetFormFilterInput(),
      'company_number'    => new sfWidgetFormFilterInput(),
      'shop_id'           => new sfWidgetFormFilterInput(),
      'quantity'          => new sfWidgetFormFilterInput(),
      'delivery_date'     => new sfWidgetFormFilterInput(),
      'comment'           => new sfWidgetFormFilterInput(),
      'group_id'          => new sfWidgetFormFilterInput(),
      'is_received'       => new sfWidgetFormFilterInput(),
      'is_synced'         => new sfWidgetFormFilterInput(),
      'shop_responded_at' => new sfWidgetFormFilterInput(),
      'received_quantity' => new sfWidgetFormFilterInput(),
      'reply_comment'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'bookout_number'    => new sfValidatorPass(array('required' => false)),
      'branch_number'     => new sfValidatorPass(array('required' => false)),
      'status'            => new sfValidatorPass(array('required' => false)),
      'synced_at'         => new sfValidatorPass(array('required' => false)),
      'received_at'       => new sfValidatorPass(array('required' => false)),
      'action'            => new sfValidatorPass(array('required' => false)),
      'created_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'        => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by'        => new sfValidatorPass(array('required' => false)),
      'note_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'status_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'item_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'company_number'    => new sfValidatorPass(array('required' => false)),
      'shop_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'quantity'          => new sfValidatorPass(array('required' => false)),
      'delivery_date'     => new sfValidatorPass(array('required' => false)),
      'comment'           => new sfValidatorPass(array('required' => false)),
      'group_id'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_received'       => new sfValidatorPass(array('required' => false)),
      'is_synced'         => new sfValidatorPass(array('required' => false)),
      'shop_responded_at' => new sfValidatorPass(array('required' => false)),
      'received_quantity' => new sfValidatorPass(array('required' => false)),
      'reply_comment'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('bookout_notes_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'BookoutNotes';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'bookout_number'    => 'Text',
      'branch_number'     => 'Text',
      'status'            => 'Text',
      'synced_at'         => 'Text',
      'received_at'       => 'Text',
      'action'            => 'Text',
      'created_at'        => 'Date',
      'updated_at'        => 'Date',
      'updated_by'        => 'Text',
      'note_id'           => 'Number',
      'status_id'         => 'Number',
      'item_id'           => 'Number',
      'company_number'    => 'Text',
      'shop_id'           => 'Number',
      'quantity'          => 'Text',
      'delivery_date'     => 'Text',
      'comment'           => 'Text',
      'group_id'          => 'Number',
      'is_received'       => 'Text',
      'is_synced'         => 'Text',
      'shop_responded_at' => 'Text',
      'received_quantity' => 'Text',
      'reply_comment'     => 'Text',
    );
  }
}
