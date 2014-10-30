<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * VendorAlias filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BaseVendorAliasFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'vendor_id' => new sfWidgetFormPropelChoice(array('model' => 'Vendors', 'add_empty' => true)),
      'title'     => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'vendor_id' => new sfValidatorPropelChoice(array('required' => false, 'model' => 'Vendors', 'column' => 'id')),
      'title'     => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('vendor_alias_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'VendorAlias';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'vendor_id' => 'ForeignKey',
      'title'     => 'Text',
    );
  }
}
