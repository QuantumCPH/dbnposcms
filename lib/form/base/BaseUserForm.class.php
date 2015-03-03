<?php

/**
 * User form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseUserForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                        => new sfWidgetFormInputHidden(),
      'name'                      => new sfWidgetFormInput(),
      'email'                     => new sfWidgetFormInput(),
      'password'                  => new sfWidgetFormInput(),
      'role_id'                   => new sfWidgetFormPropelChoice(array('model' => 'Role', 'add_empty' => true)),
      'is_super_user'             => new sfWidgetFormInputCheckbox(),
      'status_id'                 => new sfWidgetFormPropelChoice(array('model' => 'Statuses', 'add_empty' => true)),
      'created_at'                => new sfWidgetFormDateTime(),
      'sur_name'                  => new sfWidgetFormInput(),
      'address'                   => new sfWidgetFormTextarea(),
      'zip'                       => new sfWidgetFormInput(),
      'city'                      => new sfWidgetFormInput(),
      'country'                   => new sfWidgetFormInput(),
      'tel'                       => new sfWidgetFormInput(),
      'mobile'                    => new sfWidgetFormInput(),
      'updated_at'                => new sfWidgetFormDateTime(),
      'pin'                       => new sfWidgetFormInput(),
      'pos_user_role_id'          => new sfWidgetFormPropelChoice(array('model' => 'PosRole', 'add_empty' => true)),
      'reset_password_token'      => new sfWidgetFormInput(),
      'updated_by'                => new sfWidgetFormInput(),
      'branch_request_id'         => new sfWidgetFormInput(),
      'pos_super_user'            => new sfWidgetFormInputCheckbox(),
      'pin_status'                => new sfWidgetFormInput(),
      'deliverynote_ok_email'     => new sfWidgetFormInputCheckbox(),
      'deliverynote_change_email' => new sfWidgetFormInputCheckbox(),
      'bookout_ok_email'          => new sfWidgetFormInputCheckbox(),
      'bookout_change_email'      => new sfWidgetFormInputCheckbox(),
      'sale_email'                => new sfWidgetFormInputCheckbox(),
      'daystart_email'            => new sfWidgetFormInputCheckbox(),
      'dayend_email'              => new sfWidgetFormInputCheckbox(),
      'setting_email'             => new sfWidgetFormInputCheckbox(),
      'bookout_sync_email'        => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'                        => new sfValidatorPropelChoice(array('model' => 'User', 'column' => 'id', 'required' => false)),
      'name'                      => new sfValidatorString(array('max_length' => 150)),
      'email'                     => new sfValidatorString(array('max_length' => 150)),
      'password'                  => new sfValidatorString(array('max_length' => 150)),
      'role_id'                   => new sfValidatorPropelChoice(array('model' => 'Role', 'column' => 'id', 'required' => false)),
      'is_super_user'             => new sfValidatorBoolean(array('required' => false)),
      'status_id'                 => new sfValidatorPropelChoice(array('model' => 'Statuses', 'column' => 'id', 'required' => false)),
      'created_at'                => new sfValidatorDateTime(array('required' => false)),
      'sur_name'                  => new sfValidatorString(array('max_length' => 2555, 'required' => false)),
      'address'                   => new sfValidatorString(array('required' => false)),
      'zip'                       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'city'                      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'country'                   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'tel'                       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'mobile'                    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_at'                => new sfValidatorDateTime(array('required' => false)),
      'pin'                       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'pos_user_role_id'          => new sfValidatorPropelChoice(array('model' => 'PosRole', 'column' => 'id', 'required' => false)),
      'reset_password_token'      => new sfValidatorString(array('max_length' => 225, 'required' => false)),
      'updated_by'                => new sfValidatorInteger(array('required' => false)),
      'branch_request_id'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'pos_super_user'            => new sfValidatorBoolean(array('required' => false)),
      'pin_status'                => new sfValidatorInteger(array('required' => false)),
      'deliverynote_ok_email'     => new sfValidatorBoolean(array('required' => false)),
      'deliverynote_change_email' => new sfValidatorBoolean(array('required' => false)),
      'bookout_ok_email'          => new sfValidatorBoolean(array('required' => false)),
      'bookout_change_email'      => new sfValidatorBoolean(array('required' => false)),
      'sale_email'                => new sfValidatorBoolean(array('required' => false)),
      'daystart_email'            => new sfValidatorBoolean(array('required' => false)),
      'dayend_email'              => new sfValidatorBoolean(array('required' => false)),
      'setting_email'             => new sfValidatorBoolean(array('required' => false)),
      'bookout_sync_email'        => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('user[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'User';
  }


}
