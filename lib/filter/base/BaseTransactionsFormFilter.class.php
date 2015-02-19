<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Transactions filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseTransactionsFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'transaction_type_id'    => new sfWidgetFormFilterInput(),
      'quantity'               => new sfWidgetFormFilterInput(),
      'item_id'                => new sfWidgetFormFilterInput(),
      'shop_receipt_number_id' => new sfWidgetFormFilterInput(),
      'shop_order_number_id'   => new sfWidgetFormFilterInput(),
      'status_id'              => new sfWidgetFormFilterInput(),
      'created_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'updated_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'parent_type'            => new sfWidgetFormFilterInput(),
      'parent_type_id'         => new sfWidgetFormFilterInput(),
      'sold_price'             => new sfWidgetFormFilterInput(),
      'discount_value'         => new sfWidgetFormFilterInput(),
      'discount_type_id'       => new sfWidgetFormFilterInput(),
      'shop_transaction_id'    => new sfWidgetFormFilterInput(),
      'shop_id'                => new sfWidgetFormFilterInput(),
      'description1'           => new sfWidgetFormFilterInput(),
      'description2'           => new sfWidgetFormFilterInput(),
      'description3'           => new sfWidgetFormFilterInput(),
      'supplier_number'        => new sfWidgetFormFilterInput(),
      'supplier_item_number'   => new sfWidgetFormFilterInput(),
      'ean'                    => new sfWidgetFormFilterInput(),
      'group'                  => new sfWidgetFormFilterInput(),
      'color'                  => new sfWidgetFormFilterInput(),
      'size'                   => new sfWidgetFormFilterInput(),
      'buying_price'           => new sfWidgetFormFilterInput(),
      'selling_price'          => new sfWidgetFormFilterInput(),
      'taxation_code'          => new sfWidgetFormFilterInput(),
      'down_sync'              => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'user_id'                => new sfWidgetFormFilterInput(),
      'cms_item_id'            => new sfWidgetFormFilterInput(),
      'order_id'               => new sfWidgetFormFilterInput(),
      'day_start_id'           => new sfWidgetFormFilterInput(),
      'promotion_ids'          => new sfWidgetFormFilterInput(),
<<<<<<< HEAD
=======
<<<<<<< HEAD
      'stock_id'               => new sfWidgetFormFilterInput(),
=======
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    ));

    $this->setValidators(array(
      'transaction_type_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'quantity'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'item_id'                => new sfValidatorPass(array('required' => false)),
      'shop_receipt_number_id' => new sfValidatorPass(array('required' => false)),
      'shop_order_number_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'status_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'parent_type'            => new sfValidatorPass(array('required' => false)),
      'parent_type_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'sold_price'             => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'discount_value'         => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'discount_type_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_transaction_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'shop_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'description1'           => new sfValidatorPass(array('required' => false)),
      'description2'           => new sfValidatorPass(array('required' => false)),
      'description3'           => new sfValidatorPass(array('required' => false)),
      'supplier_number'        => new sfValidatorPass(array('required' => false)),
      'supplier_item_number'   => new sfValidatorPass(array('required' => false)),
      'ean'                    => new sfValidatorPass(array('required' => false)),
      'group'                  => new sfValidatorPass(array('required' => false)),
      'color'                  => new sfValidatorPass(array('required' => false)),
      'size'                   => new sfValidatorPass(array('required' => false)),
      'buying_price'           => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'selling_price'          => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'taxation_code'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'down_sync'              => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'user_id'                => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'cms_item_id'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'order_id'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'day_start_id'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
<<<<<<< HEAD
      'promotion_ids'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
=======
<<<<<<< HEAD
      'promotion_ids'          => new sfValidatorPass(array('required' => false)),
      'stock_id'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
=======
      'promotion_ids'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    ));

    $this->widgetSchema->setNameFormat('transactions_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Transactions';
  }

  public function getFields()
  {
    return array(
      'id'                     => 'Number',
      'transaction_type_id'    => 'Number',
      'quantity'               => 'Number',
      'item_id'                => 'Text',
      'shop_receipt_number_id' => 'Text',
      'shop_order_number_id'   => 'Number',
      'status_id'              => 'Number',
      'created_at'             => 'Date',
      'updated_at'             => 'Date',
      'parent_type'            => 'Text',
      'parent_type_id'         => 'Number',
      'sold_price'             => 'Number',
      'discount_value'         => 'Number',
      'discount_type_id'       => 'Number',
      'shop_transaction_id'    => 'Number',
      'shop_id'                => 'Number',
      'description1'           => 'Text',
      'description2'           => 'Text',
      'description3'           => 'Text',
      'supplier_number'        => 'Text',
      'supplier_item_number'   => 'Text',
      'ean'                    => 'Text',
      'group'                  => 'Text',
      'color'                  => 'Text',
      'size'                   => 'Text',
      'buying_price'           => 'Number',
      'selling_price'          => 'Number',
      'taxation_code'          => 'Number',
      'down_sync'              => 'Boolean',
      'user_id'                => 'Number',
      'cms_item_id'            => 'Number',
      'order_id'               => 'Number',
      'day_start_id'           => 'Number',
<<<<<<< HEAD
      'promotion_ids'          => 'Number',
=======
<<<<<<< HEAD
      'promotion_ids'          => 'Text',
      'stock_id'               => 'Number',
=======
      'promotion_ids'          => 'Number',
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    );
  }
}
