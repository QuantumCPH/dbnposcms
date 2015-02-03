<?php

/**
 * Promotion form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BasePromotionForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'promotion_title'      => new sfWidgetFormInput(),
      'start_date'           => new sfWidgetFormInput(),
      'end_date'             => new sfWidgetFormInput(),
      'on_all_item'          => new sfWidgetFormInput(),
      'promotion_value'      => new sfWidgetFormInput(),
      'promotion_type'       => new sfWidgetFormInput(),
      'on_all_branch'        => new sfWidgetFormInput(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_by'           => new sfWidgetFormInput(),
      'promotion_status'     => new sfWidgetFormInput(),
      'updated_at'           => new sfWidgetFormDateTime(),
      'item_id_type'         => new sfWidgetFormInput(),
      'item_id'              => new sfWidgetFormInput(),
      'item_id_to'           => new sfWidgetFormInput(),
      'item_id_from'         => new sfWidgetFormInput(),
      'description1'         => new sfWidgetFormInput(),
      'description2'         => new sfWidgetFormInput(),
      'description3'         => new sfWidgetFormInput(),
      'size'                 => new sfWidgetFormInput(),
      'color'                => new sfWidgetFormInput(),
      'group_type'           => new sfWidgetFormInput(),
      'group_name'           => new sfWidgetFormInput(),
      'group_to'             => new sfWidgetFormInput(),
      'group_from'           => new sfWidgetFormInput(),
      'price_type'           => new sfWidgetFormInput(),
      'price_less'           => new sfWidgetFormInput(),
      'price_greater'        => new sfWidgetFormInput(),
      'price_to'             => new sfWidgetFormInput(),
      'price_from'           => new sfWidgetFormInput(),
      'supplier_number'      => new sfWidgetFormInput(),
      'supplier_item_number' => new sfWidgetFormInput(),
      'branch_id'            => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorPropelChoice(array('model' => 'Promotion', 'column' => 'id', 'required' => false)),
      'promotion_title'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'start_date'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'end_date'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'on_all_item'          => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'promotion_value'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'promotion_type'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'on_all_branch'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'created_at'           => new sfValidatorDateTime(),
      'updated_by'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'promotion_status'     => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_at'           => new sfValidatorDateTime(),
      'item_id_type'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'item_id'              => new sfValidatorInteger(array('required' => false)),
      'item_id_to'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'item_id_from'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description1'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description2'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description3'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'size'                 => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'color'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'group_type'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'group_name'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'group_to'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'group_from'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'price_type'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'price_less'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'price_greater'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'price_to'             => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'price_from'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'supplier_number'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'supplier_item_number' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'branch_id'            => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('promotion[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Promotion';
  }


}
