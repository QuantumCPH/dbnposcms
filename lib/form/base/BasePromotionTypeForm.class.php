<?php

/**
 * PromotionType form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BasePromotionTypeForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'promotion_type_title' => new sfWidgetFormInput(),
      'created_at'           => new sfWidgetFormDate(),
      'updated_at'           => new sfWidgetFormDateTime(),
      'updated_by'           => new sfWidgetFormInput(),
      'status_id'            => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorPropelChoice(array('model' => 'PromotionType', 'column' => 'id', 'required' => false)),
      'promotion_type_title' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'           => new sfValidatorDate(array('required' => false)),
      'updated_at'           => new sfValidatorDateTime(),
      'updated_by'           => new sfValidatorInteger(array('required' => false)),
      'status_id'            => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('promotion_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'PromotionType';
  }


}
