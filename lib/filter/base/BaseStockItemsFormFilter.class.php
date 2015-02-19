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
<<<<<<< HEAD
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
=======
<<<<<<< HEAD
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
      'shop_id'       => new sfWidgetFormFilterInput(),
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
      'shop_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
=======
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
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
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
<<<<<<< HEAD
=======
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
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
<<<<<<< HEAD
=======
<<<<<<< HEAD
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
      'shop_id'       => 'Number',
=======
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
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
<<<<<<< HEAD
=======
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    );
  }
}
