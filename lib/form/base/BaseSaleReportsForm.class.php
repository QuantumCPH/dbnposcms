<?php

/**
 * SaleReports form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseSaleReportsForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'               => new sfWidgetFormInputHidden(),
      'report_date_from' => new sfWidgetFormDateTime(),
      'report_date_to'   => new sfWidgetFormDateTime(),
      'status_id'        => new sfWidgetFormInput(),
      'created_at'       => new sfWidgetFormDateTime(),
      'updated_at'       => new sfWidgetFormDateTime(),
      'data_xml'         => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'               => new sfValidatorPropelChoice(array('model' => 'SaleReports', 'column' => 'id', 'required' => false)),
      'report_date_from' => new sfValidatorDateTime(array('required' => false)),
      'report_date_to'   => new sfValidatorDateTime(array('required' => false)),
      'status_id'        => new sfValidatorInteger(array('required' => false)),
      'created_at'       => new sfValidatorDateTime(),
      'updated_at'       => new sfValidatorDateTime(array('required' => false)),
      'data_xml'         => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('sale_reports[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'SaleReports';
  }


}
