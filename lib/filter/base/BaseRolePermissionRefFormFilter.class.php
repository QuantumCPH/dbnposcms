<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * RolePermissionRef filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseRolePermissionRefFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'role_id'       => new sfWidgetFormFilterInput(),
      'permission_id' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'role_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'permission_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('role_permission_ref_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'RolePermissionRef';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'role_id'       => 'Number',
      'permission_id' => 'Number',
    );
  }
}
