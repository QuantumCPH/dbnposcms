<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * ReturnNotes filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseReturnNotesFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'item_id'    => new sfWidgetFormFilterInput(),
      'quantity'   => new sfWidgetFormFilterInput(),
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'status_id'  => new sfWidgetFormFilterInput(),
      'comment'    => new sfWidgetFormFilterInput(),
      'user_id'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'item_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'quantity'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'status_id'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'comment'    => new sfValidatorPass(array('required' => false)),
      'user_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('return_notes_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ReturnNotes';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'item_id'    => 'Number',
      'quantity'   => 'Number',
      'created_at' => 'Date',
      'status_id'  => 'Number',
      'comment'    => 'Text',
      'user_id'    => 'Number',
    );
  }
}
