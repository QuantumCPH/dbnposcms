<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * BookoutNotes filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseBookoutNotesFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'note_id'               => new sfWidgetFormFilterInput(),
      'group_id'              => new sfWidgetFormFilterInput(),
      'item_id'               => new sfWidgetFormFilterInput(),
      'branch_number'         => new sfWidgetFormFilterInput(),
      'company_number'        => new sfWidgetFormFilterInput(),
      'quantity'              => new sfWidgetFormFilterInput(),
      'delivery_date'         => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'created_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'status_id'             => new sfWidgetFormFilterInput(),
      'received_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'received_quantity'     => new sfWidgetFormFilterInput(),
      'comment'               => new sfWidgetFormFilterInput(),
      'user_id'               => new sfWidgetFormFilterInput(),
      'shop_id'               => new sfWidgetFormFilterInput(),
      'is_synced'             => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'is_received'           => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'synced_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'            => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'shop_responded_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_by'            => new sfWidgetFormFilterInput(),
      'synced_day_start_id'   => new sfWidgetFormFilterInput(),
      'received_day_start_id' => new sfWidgetFormFilterInput(),
      'reply_comment'         => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'note_id'               => new sfValidatorPass(array('required' => false)),
      'group_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'item_id'               => new sfValidatorPass(array('required' => false)),
      'branch_number'         => new sfValidatorPass(array('required' => false)),
      'company_number'        => new sfValidatorPass(array('required' => false)),
      'quantity'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'delivery_date'         => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'created_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'status_id'             => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'received_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'received_quantity'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'comment'               => new sfValidatorPass(array('required' => false)),
      'user_id'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_id'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_synced'             => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'is_received'           => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'synced_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'            => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'shop_responded_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'synced_day_start_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'received_day_start_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'reply_comment'         => new sfValidatorPass(array('required' => false)),
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
      'id'                    => 'Number',
      'note_id'               => 'Text',
      'group_id'              => 'Number',
      'item_id'               => 'Text',
      'branch_number'         => 'Text',
      'company_number'        => 'Text',
      'quantity'              => 'Number',
      'delivery_date'         => 'Date',
      'created_at'            => 'Date',
      'status_id'             => 'Number',
      'received_at'           => 'Date',
      'received_quantity'     => 'Number',
      'comment'               => 'Text',
      'user_id'               => 'Number',
      'shop_id'               => 'Number',
      'is_synced'             => 'Boolean',
      'is_received'           => 'Boolean',
      'synced_at'             => 'Date',
      'updated_at'            => 'Date',
      'shop_responded_at'     => 'Date',
      'updated_by'            => 'Number',
      'synced_day_start_id'   => 'Number',
      'received_day_start_id' => 'Number',
      'reply_comment'         => 'Text',
    );
  }
}
