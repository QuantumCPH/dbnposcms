<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * ResellerOrder filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseResellerOrderFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'reseller_order_id'   => new sfWidgetFormFilterInput(),
      'reseller_id'         => new sfWidgetFormPropelChoice(array('model' => 'Reseller', 'add_empty' => true)),
      'amount'              => new sfWidgetFormFilterInput(),
      'status'              => new sfWidgetFormFilterInput(),
      'created_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'order_description'   => new sfWidgetFormFilterInput(),
      'receipt_no'          => new sfWidgetFormFilterInput(),
      'payment_histry_id'   => new sfWidgetFormFilterInput(),
      'transaction_from_id' => new sfWidgetFormPropelChoice(array('model' => 'TransactionFrom', 'add_empty' => true)),
      'transaction_id'      => new sfWidgetFormFilterInput(),
      'transaction_type_id' => new sfWidgetFormFilterInput(),
      'comments'            => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'reseller_order_id'   => new sfValidatorPass(array('required' => false)),
      'reseller_id'         => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Reseller', 'column' => 'id')),
      'amount'              => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
      'status'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'order_description'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'receipt_no'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'payment_histry_id'   => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'transaction_from_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'TransactionFrom', 'column' => 'id')),
      'transaction_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'transaction_type_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'comments'            => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('reseller_order_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ResellerOrder';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'reseller_order_id'   => 'Text',
      'reseller_id'         => 'ForeignKey',
      'amount'              => 'Number',
      'status'              => 'Number',
      'created_at'          => 'Date',
      'order_description'   => 'Number',
      'receipt_no'          => 'Number',
      'payment_histry_id'   => 'Number',
      'transaction_from_id' => 'ForeignKey',
      'transaction_id'      => 'Number',
      'transaction_type_id' => 'Number',
      'comments'            => 'Text',
    );
  }
}
