<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * PaymentTypes filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 */
class BasePaymentTypesFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'title' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'title' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('payment_types_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PaymentTypes';
  }

  public function getFields()
  {
    return array(
      'id'    => 'Number',
      'title' => 'Text',
    );
  }
}
