<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * PosPermission filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BasePosPermissionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'pos_module_id' => new sfWidgetFormFilterInput(),
      'action_name'   => new sfWidgetFormFilterInput(),
      'action_title'  => new sfWidgetFormFilterInput(),
      'position'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'pos_module_id' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'action_name'   => new sfValidatorPass(array('required' => false)),
      'action_title'  => new sfValidatorPass(array('required' => false)),
      'position'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('pos_permission_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PosPermission';
  }

  public function getFields()
  {
    return array(
      'id'            => 'Number',
      'pos_module_id' => 'Number',
      'action_name'   => 'Text',
      'action_title'  => 'Text',
      'position'      => 'Number',
    );
  }
}
