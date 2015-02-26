<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * StockItems filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseStockItemsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'cms_item_id'    => new sfWidgetFormFilterInput(),
      'item_id'        => new sfWidgetFormFilterInput(),
      'total_qty'      => new sfWidgetFormFilterInput(),
      'sold_qty'       => new sfWidgetFormFilterInput(),
      'return_qty'     => new sfWidgetFormFilterInput(),
      'remaining_qty'  => new sfWidgetFormFilterInput(),
      'bookout_qty'    => new sfWidgetFormFilterInput(),
      'stock_qty'      => new sfWidgetFormFilterInput(),
      'stock_id'       => new sfWidgetFormFilterInput(),
      'shop_id'        => new sfWidgetFormFilterInput(),
      'stock_type'     => new sfWidgetFormFilterInput(),
      'stock_value'    => new sfWidgetFormFilterInput(),
      'process_status' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'cms_item_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'item_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'total_qty'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'sold_qty'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'return_qty'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'remaining_qty'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'bookout_qty'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'stock_qty'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'stock_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'stock_type'     => new sfValidatorPass(array('required' => false)),
      'stock_value'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'process_status' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
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
      'id'             => 'Number',
      'cms_item_id'    => 'Number',
      'item_id'        => 'Number',
      'total_qty'      => 'Number',
      'sold_qty'       => 'Number',
      'return_qty'     => 'Number',
      'remaining_qty'  => 'Number',
      'bookout_qty'    => 'Number',
      'stock_qty'      => 'Number',
      'stock_id'       => 'Number',
      'shop_id'        => 'Number',
      'stock_type'     => 'Text',
      'stock_value'    => 'Number',
      'process_status' => 'Number',
    );
  }
}
