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
    ));

    $this->setValidators(array(
      'cms_item_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'item_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'total_qty'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'sold_qty'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'return_qty'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'remaining_qty' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'bookout_qty'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'stock_qty'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'stock_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'total_qty'     => 'Number',
      'sold_qty'      => 'Number',
      'return_qty'    => 'Number',
      'remaining_qty' => 'Number',
      'bookout_qty'   => 'Number',
      'stock_qty'     => 'Number',
      'stock_id'      => 'Number',
    );
  }
}
