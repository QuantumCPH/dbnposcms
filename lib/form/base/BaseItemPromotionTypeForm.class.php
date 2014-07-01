<?php

/**
 * ItemPromotionType form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseItemPromotionTypeForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'item_promotion_title' => new sfWidgetFormInput(),
      'created_at'           => new sfWidgetFormDate(),
      'updated_at'           => new sfWidgetFormDateTime(),
      'updated_by'           => new sfWidgetFormInput(),
      'status_id'            => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorPropelChoice(array('model' => 'ItemPromotionType', 'column' => 'id', 'required' => false)),
      'item_promotion_title' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'           => new sfValidatorDate(array('required' => false)),
      'updated_at'           => new sfValidatorDateTime(),
      'updated_by'           => new sfValidatorInteger(array('required' => false)),
      'status_id'            => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('item_promotion_type[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ItemPromotionType';
  }


}
