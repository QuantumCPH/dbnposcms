<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Permission filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BasePermissionFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'module_id'    => new sfWidgetFormPropelChoice(array('model' => 'Modules', 'add_empty' => true)),
      'action_name'  => new sfWidgetFormFilterInput(),
      'action_title' => new sfWidgetFormFilterInput(),
      'position'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'module_id'    => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Modules', 'column' => 'id')),
      'action_name'  => new sfValidatorPass(array('required' => false)),
      'action_title' => new sfValidatorPass(array('required' => false)),
      'position'     => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('permission_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Permission';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'module_id'    => 'ForeignKey',
      'action_name'  => 'Text',
      'action_title' => 'Text',
      'position'     => 'Number',
    );
  }
}
