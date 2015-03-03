<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Journal filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseJournalFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'shop_id'      => new sfWidgetFormFilterInput(),
      'updated_at'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'created_at'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'created_date' => new sfWidgetFormFilterInput(),
      'updated_by'   => new sfWidgetFormFilterInput(),
      'journal_id'   => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'shop_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'updated_at'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'created_at'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'created_date' => new sfValidatorPass(array('required' => false)),
      'updated_by'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'journal_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('journal_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Journal';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'shop_id'      => 'Number',
      'updated_at'   => 'Date',
      'created_at'   => 'Date',
      'created_date' => 'Text',
      'updated_by'   => 'Number',
      'journal_id'   => 'Number',
    );
  }
}
