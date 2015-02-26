<?php

/**
 * VendorAlias form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseVendorAliasForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'vendor_id' => new sfWidgetFormPropelChoice(array('model' => 'Vendors', 'add_empty' => false)),
      'title'     => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorPropelChoice(array('model' => 'VendorAlias', 'column' => 'id', 'required' => false)),
      'vendor_id' => new sfValidatorPropelChoice(array('model' => 'Vendors', 'column' => 'id')),
      'title'     => new sfValidatorString(array('max_length' => 220)),
    ));

    $this->widgetSchema->setNameFormat('vendor_alias[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'VendorAlias';
  }


}
