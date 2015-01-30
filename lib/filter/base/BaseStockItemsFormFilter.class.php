<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * StockItems filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseStockItemsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'cms_item_id'   => new sfWidgetFormFilterInput(),
      'item_id'       => new sfWidgetFormFilterInput(),
      'total_qty'     => new sfWidgetFormFilterInput(),
      'sold_qty'      => new sfWidgetFormFilterInput(),
      'return_qty'    => new sfWidgetFormFilterInput(),
      'remaining_qty' => new sfWidgetFormFilterInput(),
      'bookout_qty'   => new sfWidgetFormFilterInput(),
      'stock_qty'     => new sfWidgetFormFilterInput(),
      'stock_id'      => new sfWidgetFormFilterInput(),
      'created_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'    => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_by'    => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'cms_item_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'item_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'total_qty'     => new sfValidatorPass(array('required' => false)),
      'sold_qty'      => new sfValidatorPass(array('required' => false)),
      'return_qty'    => new sfValidatorPass(array('required' => false)),
      'remaining_qty' => new sfValidatorPass(array('required' => false)),
      'bookout_qty'   => new sfValidatorPass(array('required' => false)),
      'stock_qty'     => new sfValidatorPass(array('required' => false)),
      'stock_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'    => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_by'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('stock_items_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'StockItems';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'cms_item_id'   => 'Number',
      'item_id'       => 'Number',
      'total_qty'     => 'Text',
      'sold_qty'      => 'Text',
      'return_qty'    => 'Text',
      'remaining_qty' => 'Text',
      'bookout_qty'   => 'Text',
      'stock_qty'     => 'Text',
      'stock_id'      => 'Number',
      'created_at'    => 'Date',
      'updated_at'    => 'Date',
      'updated_by'    => 'Number',
    );
  }
}
