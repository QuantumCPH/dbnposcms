<?php

/**
 * DibsCall form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseDibsCallForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'callurl'        => new sfWidgetFormTextarea(),
      'created_at'     => new sfWidgetFormDateTime(),
      'decrypted_data' => new sfWidgetFormTextarea(),
      'call_response'  => new sfWidgetFormTextarea(),
      'call_post_data' => new sfWidgetFormTextarea(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorPropelChoice(array('model' => 'DibsCall', 'column' => 'id', 'required' => false)),
      'callurl'        => new sfValidatorString(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'decrypted_data' => new sfValidatorString(array('required' => false)),
      'call_response'  => new sfValidatorString(array('required' => false)),
      'call_post_data' => new sfValidatorString(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('dibs_call[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'DibsCall';
  }


}
