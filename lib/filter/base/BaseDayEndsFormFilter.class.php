<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * DayEnds filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseDayEndsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'day_ended_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'day_ended_by'    => new sfWidgetFormFilterInput(),
      'shop_id'         => new sfWidgetFormFilterInput(),
      'day_start_id'    => new sfWidgetFormFilterInput(),
      'created_at'      => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'total_amount'    => new sfWidgetFormFilterInput(),
      'success'         => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'expected_amount' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'day_ended_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'day_ended_by'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'day_start_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'      => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'total_amount'    => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'success'         => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'expected_amount' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('day_ends_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DayEnds';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'day_ended_at'    => 'Date',
      'day_ended_by'    => 'Number',
      'shop_id'         => 'Number',
      'day_start_id'    => 'Number',
      'created_at'      => 'Date',
      'total_amount'    => 'Number',
      'success'         => 'Boolean',
      'expected_amount' => 'Number',
    );
  }
}
