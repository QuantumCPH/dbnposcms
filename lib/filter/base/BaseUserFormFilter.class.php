<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * User filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseUserFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'name'                 => new sfWidgetFormFilterInput(),
      'email'                => new sfWidgetFormFilterInput(),
      'password'             => new sfWidgetFormFilterInput(),
      'role_id'              => new sfWidgetFormFilterInput(),
      'is_super_user'        => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'status_id'            => new sfWidgetFormFilterInput(),
      'created_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'sur_name'             => new sfWidgetFormFilterInput(),
      'address'              => new sfWidgetFormFilterInput(),
      'zip'                  => new sfWidgetFormFilterInput(),
      'city'                 => new sfWidgetFormFilterInput(),
      'country'              => new sfWidgetFormFilterInput(),
      'tel'                  => new sfWidgetFormFilterInput(),
      'mobile'               => new sfWidgetFormFilterInput(),
      'updated_at'           => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => true)),
      'pin'                  => new sfWidgetFormFilterInput(),
      'pos_user_role_id'     => new sfWidgetFormFilterInput(),
      'reset_password_token' => new sfWidgetFormFilterInput(),
      'updated_by'           => new sfWidgetFormFilterInput(),
      'branch_request_id'    => new sfWidgetFormFilterInput(),
      'pos_super_user'       => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'pin_status'           => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'name'                 => new sfValidatorPass(array('required' => false)),
      'email'                => new sfValidatorPass(array('required' => false)),
      'password'             => new sfValidatorPass(array('required' => false)),
      'role_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'is_super_user'        => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'status_id'            => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'sur_name'             => new sfValidatorPass(array('required' => false)),
      'address'              => new sfValidatorPass(array('required' => false)),
      'zip'                  => new sfValidatorPass(array('required' => false)),
      'city'                 => new sfValidatorPass(array('required' => false)),
      'country'              => new sfValidatorPass(array('required' => false)),
      'tel'                  => new sfValidatorPass(array('required' => false)),
      'mobile'               => new sfValidatorPass(array('required' => false)),
      'updated_at'           => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'pin'                  => new sfValidatorPass(array('required' => false)),
      'pos_user_role_id'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'reset_password_token' => new sfValidatorPass(array('required' => false)),
      'updated_by'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'branch_request_id'    => new sfValidatorPass(array('required' => false)),
      'pos_super_user'       => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'pin_status'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('user_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'User';
  }

  public function getFields()
  {
    return array(
      'id'                   => 'Number',
      'name'                 => 'Text',
      'email'                => 'Text',
      'password'             => 'Text',
      'role_id'              => 'Number',
      'is_super_user'        => 'Boolean',
      'status_id'            => 'Number',
      'created_at'           => 'Date',
      'sur_name'             => 'Text',
      'address'              => 'Text',
      'zip'                  => 'Text',
      'city'                 => 'Text',
      'country'              => 'Text',
      'tel'                  => 'Text',
      'mobile'               => 'Text',
      'updated_at'           => 'Date',
      'pin'                  => 'Text',
      'pos_user_role_id'     => 'Number',
      'reset_password_token' => 'Text',
      'updated_by'           => 'Number',
      'branch_request_id'    => 'Text',
      'pos_super_user'       => 'Boolean',
      'pin_status'           => 'Number',
    );
  }
}
