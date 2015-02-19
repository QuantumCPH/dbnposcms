<?php

/**
 * Inventory form base class.
 *
 * @package    zapnacrm
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormGeneratedTemplate.php 12815 2008-11-09 10:43:58Z fabien $
 */
class BaseInventoryForm extends BaseFormPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'shop_id'        => new sfWidgetFormInput(),
      'cms_item_id'    => new sfWidgetFormInput(),
      'total'          => new sfWidgetFormInput(),
      'sold'           => new sfWidgetFormInput(),
      'book_out'       => new sfWidgetFormInput(),
      'returned'       => new sfWidgetFormInput(),
      'available'      => new sfWidgetFormInput(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'item_id'        => new sfWidgetFormInput(),
      'delivery_count' => new sfWidgetFormInput(),
      'stock_in'       => new sfWidgetFormInput(),
      'stock_out'      => new sfWidgetFormInput(),
    ));

    $this->setValidators(array(
      'id'             => new sfValidatorPropelChoice(array('model' => 'Inventory', 'column' => 'id', 'required' => false)),
      'shop_id'        => new sfValidatorInteger(array('required' => false)),
      'cms_item_id'    => new sfValidatorInteger(array('required' => false)),
      'total'          => new sfValidatorInteger(array('required' => false)),
      'sold'           => new sfValidatorInteger(array('required' => false)),
      'book_out'       => new sfValidatorInteger(array('required' => false)),
      'returned'       => new sfValidatorInteger(array('required' => false)),
      'available'      => new sfValidatorInteger(array('required' => false)),
      'created_at'     => new sfValidatorDateTime(),
      'updated_at'     => new sfValidatorDateTime(),
      'item_id'        => new sfValidatorString(array('max_length' => 100, 'required' => false)),
      'delivery_count' => new sfValidatorInteger(array('required' => false)),
<<<<<<< HEAD
      'stock_in'       => new sfValidatorInteger(array('required' => false)),
      'stock_out'      => new sfValidatorInteger(array('required' => false)),
=======
<<<<<<< HEAD
      'stock_in'       => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'stock_out'      => new sfValidatorString(array('max_length' => 255, 'required' => false)),
=======
      'stock_in'       => new sfValidatorInteger(array('required' => false)),
      'stock_out'      => new sfValidatorInteger(array('required' => false)),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    ));

    $this->widgetSchema->setNameFormat('inventory[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Inventory';
  }


}
