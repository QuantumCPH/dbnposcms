<?php

/**
 * Transactions form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseTransactionsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                     => new sfWidgetFormInputHidden(),
      'transaction_type_id'    => new sfWidgetFormInput(),
      'quantity'               => new sfWidgetFormInput(),
      'item_id'                => new sfWidgetFormInput(),
      'shop_receipt_number_id' => new sfWidgetFormInput(),
      'shop_order_number_id'   => new sfWidgetFormInput(),
      'status_id'              => new sfWidgetFormInput(),
      'created_at'             => new sfWidgetFormDateTime(),
      'updated_at'             => new sfWidgetFormDateTime(),
      'parent_type'            => new sfWidgetFormInput(),
      'parent_type_id'         => new sfWidgetFormInput(),
      'sold_price'             => new sfWidgetFormInput(),
      'discount_value'         => new sfWidgetFormInput(),
      'discount_type_id'       => new sfWidgetFormInput(),
      'shop_transaction_id'    => new sfWidgetFormInput(),
      'shop_id'                => new sfWidgetFormInput(),
      'description1'           => new sfWidgetFormTextarea(),
      'description2'           => new sfWidgetFormTextarea(),
      'description3'           => new sfWidgetFormTextarea(),
      'supplier_number'        => new sfWidgetFormInput(),
      'supplier_item_number'   => new sfWidgetFormInput(),
      'ean'                    => new sfWidgetFormInput(),
      'group'                  => new sfWidgetFormInput(),
      'color'                  => new sfWidgetFormInput(),
      'size'                   => new sfWidgetFormInput(),
      'buying_price'           => new sfWidgetFormInput(),
      'selling_price'          => new sfWidgetFormInput(),
      'taxation_code'          => new sfWidgetFormInput(),
      'down_sync'              => new sfWidgetFormInputCheckbox(),
      'user_id'                => new sfWidgetFormInput(),
      'cms_item_id'            => new sfWidgetFormInput(),
      'order_id'               => new sfWidgetFormInput(),
      'day_start_id'           => new sfWidgetFormInput(),
      'promotion_ids'          => new sfWidgetFormInput(),
<<<<<<< HEAD
      'stock_id'               => new sfWidgetFormInput(),
=======
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
    ));

    $this->setValidators(array(
      'id'                     => new sfValidatorPropelChoice(array('model' => 'Transactions', 'column' => 'id', 'required' => false)),
      'transaction_type_id'    => new sfValidatorInteger(array('required' => false)),
      'quantity'               => new sfValidatorInteger(array('required' => false)),
      'item_id'                => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'shop_receipt_number_id' => new sfValidatorString(array('max_length' => 250, 'required' => false)),
      'shop_order_number_id'   => new sfValidatorInteger(array('required' => false)),
      'status_id'              => new sfValidatorInteger(array('required' => false)),
      'created_at'             => new sfValidatorDateTime(array('required' => false)),
      'updated_at'             => new sfValidatorDateTime(array('required' => false)),
      'parent_type'            => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'parent_type_id'         => new sfValidatorInteger(array('required' => false)),
      'sold_price'             => new sfValidatorNumber(array('required' => false)),
      'discount_value'         => new sfValidatorNumber(array('required' => false)),
      'discount_type_id'       => new sfValidatorInteger(array('required' => false)),
      'shop_transaction_id'    => new sfValidatorInteger(array('required' => false)),
      'shop_id'                => new sfValidatorInteger(array('required' => false)),
      'description1'           => new sfValidatorString(array('required' => false)),
      'description2'           => new sfValidatorString(array('required' => false)),
      'description3'           => new sfValidatorString(array('required' => false)),
      'supplier_number'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'supplier_item_number'   => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'ean'                    => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'group'                  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'color'                  => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'size'                   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'buying_price'           => new sfValidatorNumber(array('required' => false)),
      'selling_price'          => new sfValidatorNumber(array('required' => false)),
      'taxation_code'          => new sfValidatorInteger(array('required' => false)),
      'down_sync'              => new sfValidatorBoolean(array('required' => false)),
      'user_id'                => new sfValidatorInteger(array('required' => false)),
      'cms_item_id'            => new sfValidatorInteger(array('required' => false)),
      'order_id'               => new sfValidatorInteger(array('required' => false)),
      'day_start_id'           => new sfValidatorInteger(array('required' => false)),
<<<<<<< HEAD
      'promotion_ids'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'stock_id'               => new sfValidatorInteger(array('required' => false)),
=======
      'promotion_ids'          => new sfValidatorInteger(array('required' => false)),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
    ));

    $this->widgetSchema->setNameFormat('transactions[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Transactions';
  }


}
