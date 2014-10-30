<?php

/**
 * StockItems form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseStockItemsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
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
