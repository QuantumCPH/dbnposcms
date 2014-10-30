<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Users filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseUsersFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'login'        => new sfWidgetFormFilterInput(),
      'password'     => new sfWidgetFormFilterInput(),
      'first_name'   => new sfWidgetFormFilterInput(),
      'last_name'    => new sfWidgetFormFilterInput(),
      'email'        => new sfWidgetFormFilterInput(),
      'mobile'       => new sfWidgetFormFilterInput(),
      'user_type_id' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'login'        => new sfValidatorPass(array('required' => false)),
      'password'     => new sfValidatorPass(array('required' => false)),
      'first_name'   => new sfValidatorPass(array('required' => false)),
      'last_name'    => new sfValidatorPass(array('required' => false)),
      'email'        => new sfValidatorPass(array('required' => false)),
      'mobile'       => new sfValidatorPass(array('required' => false)),
      'user_type_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('users_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Users';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'login'        => 'Text',
      'password'     => 'Text',
      'first_name'   => 'Text',
      'last_name'    => 'Text',
      'email'        => 'Text',
      'mobile'       => 'Text',
      'user_type_id' => 'Number',
    );
  }
}
