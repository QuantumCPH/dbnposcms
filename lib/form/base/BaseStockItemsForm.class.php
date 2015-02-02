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
