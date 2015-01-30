<?php

/**
 * ItemsSync form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseItemsSyncForm extends BaseFormPropel
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
      'small_pic'            => new sfWidgetFormInput(),
      'large_pic'            => new sfWidgetFormInput(),
      'created_at'           => new sfWidgetFormDateTime(),
      'synced_at'            => new sfWidgetFormDateTime(),
      'shop_id'              => new sfWidgetFormInput(),
      'is_synced'            => new sfWidgetFormInputCheckbox(),
    ));

    $this->setValidators(array(
      'id'                   => new sfValidatorPropelChoice(array('model' => 'ItemsSync', 'column' => 'id', 'required' => false)),
      'item_id'              => new sfValidatorInteger(array('required' => false)),
      'description1'         => new sfValidatorString(array('required' => false)),
      'description2'         => new sfValidatorString(array('required' => false)),
      'description3'         => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'supplier_number'      => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'supplier_item_number' => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'ean'                  => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'group'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'color'                => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'size'                 => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'buying_price'         => new sfValidatorNumber(array('required' => false)),
      'selling_price'        => new sfValidatorNumber(array('required' => false)),
      'taxation_code'        => new sfValidatorInteger(array('required' => false)),
      'small_pic'            => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'large_pic'            => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'created_at'           => new sfValidatorDateTime(array('required' => false)),
      'synced_at'            => new sfValidatorDateTime(array('required' => false)),
      'shop_id'              => new sfValidatorInteger(array('required' => false)),
      'is_synced'            => new sfValidatorBoolean(array('required' => false)),
    ));

    $this->validatorSchema->setPostValidator(
      new sfValidatorPropelUnique(array('model' => 'ItemsSync', 'column' => array('id')))
    );

    $this->widgetSchema->setNameFormat('items_sync[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'ItemsSync';
  }


}
