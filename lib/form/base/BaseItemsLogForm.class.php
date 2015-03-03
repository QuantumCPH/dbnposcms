<?php

/**
 * ItemsLog form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 */
class BaseItemsLogForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'                   => new sfWidgetFormInputHidden(),
      'item_id'              => new sfWidgetFormInput(),
      'description1'         => new sfWidgetFormTextarea(),
      'description2'         => new sfWidgetFormTextarea(),
      'description3'         => new sfWidgetFormInput(),
      'supplier_number'      => new sfWidgetFormInput(),
      'supplier_item_number' => new sfWidgetFormInput(),
      'ean'                  => new sfWidgetFormInput(),
      'group'                => new sfWidgetFormInput(),
      'color'                => new sfWidgetFormInput(),
      'size'                 => new sfWidgetFormInput(),
      'buying_price'         => new sfWidgetFormInput(),
      'selling_price'        => new sfWidgetFormInput(),
      'taxation_code'        => new sfWidgetFormInput(),
      'available_quantity'   => new sfWidgetFormInput(),
      'small_pic'            => new sfWidgetFormInput(),
      'large_pic'            => new sfWidgetFormInput(),
      'created_at'           => new sfWidgetFormDateTime(),
      'updated_at'           => new sfWidgetFormDateTime(),
      'item_status_id'       => new sfWidgetFormInput(),
      'updated_by'           => new sfWidgetFormInput(),
      'image_name'           => new sfWidgetFormInput(),
      'image_status'         => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorPropelChoice(array('model' => 'ItemsLog', 'column' => 'id', 'required' => false)),
      'item_id'              => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'description1'         => new sfValidatorString(array('required' => false)),
      'description2'         => new sfValidatorString(array('required' => false)),
      'description3'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'supplier_number'      => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'supplier_item_number' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'ean'                  => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'group'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'color'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'size'                 => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'buying_price'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'selling_price'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'taxation_code'        => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'available_quantity'   => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'small_pic'            => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'large_pic'            => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'created_at'           => new sfValidatorDateTime(array('required' => false)),
      'updated_at'           => new sfValidatorDateTime(array('required' => false)),
      'item_status_id'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'updated_by'           => new sfValidatorInteger(array('required' => false)),
      'image_name'           => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'image_status'         => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorPropelUnique(array('model' => 'ItemsLog', 'column' => array('id')))
    );

    $this->widgetSchema->setNameFormat('items_log[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ItemsLog';
  }


}
