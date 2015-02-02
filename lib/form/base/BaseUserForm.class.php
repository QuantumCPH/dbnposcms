<?php

/**
 * User form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
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
      'pos_super_user'            => new sfWidgetFormInput(),
      'pin_status'                => new sfWidgetFormInput(),
      'deliverynote_ok_email'     => new sfWidgetFormInput(),
      'bookout_ok_email'          => new sfWidgetFormInput(),
      'deliverynote_change_email' => new sfWidgetFormInput(),
      'bookout_change_email'      => new sfWidgetFormInput(),
      'sale_email'                => new sfWidgetFormInput(),
      'bookout_sync_email'        => new sfWidgetFormInput(),
      'daystart_email'            => new sfWidgetFormInput(),
      'setting_email'             => new sfWidgetFormInput(),
      'dayend_email'              => new sfWidgetFormInput(),
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
      'pos_super_user'            => new sfValidatorString(array('max_length' => 1, 'required' => false)),
      'pin_status'                => new sfValidatorString(array('max_length' => 11, 'required' => false)),
      'deliverynote_ok_email'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'bookout_ok_email'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'deliverynote_change_email' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'bookout_change_email'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'sale_email'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'bookout_sync_email'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'daystart_email'            => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'setting_email'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'dayend_email'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
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
