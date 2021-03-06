<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * ShopUsers filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseShopUsersFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'user_id'        => new sfWidgetFormPropelChoice(array('model' => 'User', 'add_empty' => true)),
      'shop_id'        => new sfWidgetFormPropelChoice(array('model' => 'Shops', 'add_empty' => true)),
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'status_id'      => new sfWidgetFormFilterInput(),
      'log_histry'     => new sfWidgetFormFilterInput(),
      'pos_role_id'    => new sfWidgetFormFilterInput(),
      'pos_super_user' => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'is_primary'     => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
    ));

    $this->setValidators(array(
      'user_id'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'User', 'column' => 'id')),
      'shop_id'        => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Shops', 'column' => 'id')),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'status_id'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'log_histry'     => new sfValidatorPass(array('required' => false)),
      'pos_role_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'pos_super_user' => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'is_primary'     => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
    ));

    $this->widgetSchema->setNameFormat('shop_users_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ShopUsers';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'user_id'        => 'ForeignKey',
      'shop_id'        => 'ForeignKey',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
      'status_id'      => 'Number',
      'log_histry'     => 'Text',
      'pos_role_id'    => 'Number',
      'pos_super_user' => 'Boolean',
      'is_primary'     => 'Boolean',
    );
  }
}
