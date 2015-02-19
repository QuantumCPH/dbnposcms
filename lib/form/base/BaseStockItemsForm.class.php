<?php

/**
 * StockItems form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseStockItemsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
<<<<<<< HEAD
      'id'             => new sfWidgetFormInputHidden(),
      'cms_item_id'    => new sfWidgetFormInput(),
      'item_id'        => new sfWidgetFormInput(),
      'total_qty'      => new sfWidgetFormInput(),
      'sold_qty'       => new sfWidgetFormInput(),
      'return_qty'     => new sfWidgetFormInput(),
      'remaining_qty'  => new sfWidgetFormInput(),
      'bookout_qty'    => new sfWidgetFormInput(),
      'stock_qty'      => new sfWidgetFormInput(),
      'stock_id'       => new sfWidgetFormInput(),
      'shop_id'        => new sfWidgetFormInput(),
      'stock_type'     => new sfWidgetFormInput(),
      'stock_value'    => new sfWidgetFormInput(),
      'process_status' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
=======
<<<<<<< HEAD
      'id'            => new sfWidgetFormInputHidden(),
      'cms_item_id'   => new sfWidgetFormInput(),
      'item_id'       => new sfWidgetFormInput(),
      'total_qty'     => new sfWidgetFormInput(),
      'sold_qty'      => new sfWidgetFormInput(),
      'return_qty'    => new sfWidgetFormInput(),
      'remaining_qty' => new sfWidgetFormInput(),
      'bookout_qty'   => new sfWidgetFormInput(),
      'stock_qty'     => new sfWidgetFormInput(),
      'stock_id'      => new sfWidgetFormInput(),
      'created_at'    => new sfWidgetFormDateTime(),
      'updated_at'    => new sfWidgetFormDateTime(),
      'updated_by'    => new sfWidgetFormInput(),
      'shop_id'       => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'            => new sfValidatorPropelChoice(array('model' => 'StockItems', 'column' => 'id', 'required' => false)),
      'cms_item_id'   => new sfValidatorInteger(array('required' => false)),
      'item_id'       => new sfValidatorInteger(array('required' => false)),
      'total_qty'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sold_qty'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'return_qty'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'remaining_qty' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'bookout_qty'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'stock_qty'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'stock_id'      => new sfValidatorInteger(array('required' => false)),
      'created_at'    => new sfValidatorDateTime(array('required' => false)),
      'updated_at'    => new sfValidatorDateTime(array('required' => false)),
      'updated_by'    => new sfValidatorInteger(array('required' => false)),
      'shop_id'       => new sfValidatorInteger(array('required' => false)),
=======
      'id'             => new sfWidgetFormInputHidden(),
      'cms_item_id'    => new sfWidgetFormInput(),
      'item_id'        => new sfWidgetFormInput(),
      'total_qty'      => new sfWidgetFormInput(),
      'sold_qty'       => new sfWidgetFormInput(),
      'return_qty'     => new sfWidgetFormInput(),
      'remaining_qty'  => new sfWidgetFormInput(),
      'bookout_qty'    => new sfWidgetFormInput(),
      'stock_qty'      => new sfWidgetFormInput(),
      'stock_id'       => new sfWidgetFormInput(),
      'shop_id'        => new sfWidgetFormInput(),
      'stock_type'     => new sfWidgetFormInput(),
      'stock_value'    => new sfWidgetFormInput(),
      'process_status' => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
      'id'             => new sfValidatorPropelChoice(array('model' => 'StockItems', 'column' => 'id', 'required' => false)),
      'cms_item_id'    => new sfValidatorInteger(array('required' => false)),
      'item_id'        => new sfValidatorInteger(array('required' => false)),
      'total_qty'      => new sfValidatorInteger(array('required' => false)),
      'sold_qty'       => new sfValidatorInteger(array('required' => false)),
      'return_qty'     => new sfValidatorInteger(array('required' => false)),
      'remaining_qty'  => new sfValidatorInteger(array('required' => false)),
      'bookout_qty'    => new sfValidatorInteger(array('required' => false)),
      'stock_qty'      => new sfValidatorInteger(array('required' => false)),
      'stock_id'       => new sfValidatorInteger(array('required' => false)),
      'shop_id'        => new sfValidatorInteger(array('required' => false)),
      'stock_type'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'stock_value'    => new sfValidatorInteger(array('required' => false)),
      'process_status' => new sfValidatorInteger(array('required' => false)),
<<<<<<< HEAD
=======
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    ));

    $this->widgetSchema->setNameFormat('stock_items[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'StockItems';
  }


}
