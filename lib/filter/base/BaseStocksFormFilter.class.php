<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Stocks filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseStocksFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'shop_id'    => new sfWidgetFormFilterInput(),
      'stock_id'   => new sfWidgetFormFilterInput(),
      'created_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at' => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_by' => new sfWidgetFormFilterInput(),
      'stock_type' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'shop_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'stock_id'   => new sfValidatorPass(array('required' => false)),
      'created_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at' => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'stock_type' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('stocks_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Stocks';
  }

  public function getFields()
  {
    return array(
      'id'         => 'Number',
      'shop_id'    => 'Number',
      'stock_id'   => 'Text',
      'created_at' => 'Date',
      'updated_at' => 'Date',
      'updated_by' => 'Number',
      'stock_type' => 'Text',
    );
  }
}
