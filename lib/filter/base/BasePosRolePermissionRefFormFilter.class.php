<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * PosRolePermissionRef filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BasePosRolePermissionRefFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'pos_role_id'       => new sfWidgetFormFilterInput(),
      'pos_permission_id' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'pos_role_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'pos_permission_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('pos_role_permission_ref_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PosRolePermissionRef';
  }

  public function getFields()
  {
    return array(
      'id'                => 'Number',
      'pos_role_id'       => 'Number',
      'pos_permission_id' => 'Number',
    );
  }
}
