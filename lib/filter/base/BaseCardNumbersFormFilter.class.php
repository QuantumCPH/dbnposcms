<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * CardNumbers filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseCardNumbersFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'card_number'         => new sfWidgetFormFilterInput(),
      'card_serial'         => new sfWidgetFormFilterInput(),
      'card_price'          => new sfWidgetFormFilterInput(),
      'status'              => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'created_at'          => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'used_at'             => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'customer_mobile'     => new sfWidgetFormFilterInput(),
      'product_id'          => new sfWidgetFormPropelChoice(array('model' => 'Product', 'add_empty' => true)),
      'comments'            => new sfWidgetFormFilterInput(),
      'card_type_id'        => new sfWidgetFormPropelChoice(array('model' => 'CardTypes', 'add_empty' => true)),
      'agent_company_id'    => new sfWidgetFormPropelChoice(array('model' => 'AgentCompany', 'add_empty' => true)),
      'agent_user_id'       => new sfWidgetFormPropelChoice(array('model' => 'AgentUser', 'add_empty' => true)),
      'used_by'             => new sfWidgetFormFilterInput(),
      'customer_id'         => new sfWidgetFormFilterInput(),
      'card_purchase_price' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'card_number'         => new sfValidatorPass(array('required' => false)),
      'card_serial'         => new sfValidatorPass(array('required' => false)),
      'card_price'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'status'              => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'created_at'          => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'used_at'             => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'customer_mobile'     => new sfValidatorPass(array('required' => false)),
      'product_id'          => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Product', 'column' => 'id')),
      'comments'            => new sfValidatorPass(array('required' => false)),
      'card_type_id'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'CardTypes', 'column' => 'id')),
      'agent_company_id'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'AgentCompany', 'column' => 'id')),
      'agent_user_id'       => new sfValidatorPropelChoice(array('required' => false, 'model' => 'AgentUser', 'column' => 'id')),
      'used_by'             => new sfValidatorPass(array('required' => false)),
      'customer_id'         => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'card_purchase_price' => new sfValidatorSchemaFilter('text', new sfValidatorNumber(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('card_numbers_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'CardNumbers';
  }

  public function getFields()
  {
    return array(
      'id'                  => 'Number',
      'card_number'         => 'Text',
      'card_serial'         => 'Text',
      'card_price'          => 'Number',
      'status'              => 'Boolean',
      'created_at'          => 'Date',
      'used_at'             => 'Date',
      'customer_mobile'     => 'Text',
      'product_id'          => 'ForeignKey',
      'comments'            => 'Text',
      'card_type_id'        => 'ForeignKey',
      'agent_company_id'    => 'ForeignKey',
      'agent_user_id'       => 'ForeignKey',
      'used_by'             => 'Text',
      'customer_id'         => 'Number',
      'card_purchase_price' => 'Number',
    );
  }
}
