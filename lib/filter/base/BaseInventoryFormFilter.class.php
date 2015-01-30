<?php

require_once(sfConfig::get('sf_lib_dir').'/filter/base/BaseFormFilterPropel.class.php');

/**
 * Inventory filter form base class.
 *
 * @package    zapnacrm
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfPropelFormFilterGeneratedTemplate.php 13459 2008-11-28 14:48:12Z fabien $
 */
class BaseInventoryFormFilter extends BaseFormFilterPropel
{
  public function setup()
  {
    $this->setWidgets(array(
      'shop_id'        => new sfWidgetFormFilterInput(),
      'cms_item_id'    => new sfWidgetFormFilterInput(),
      'total'          => new sfWidgetFormFilterInput(),
      'sold'           => new sfWidgetFormFilterInput(),
      'book_out'       => new sfWidgetFormFilterInput(),
      'returned'       => new sfWidgetFormFilterInput(),
      'available'      => new sfWidgetFormFilterInput(),
      'created_at'     => new sfWidgetFormFilterInput(),
      'updated_at'     => new sfWidgetFormFilterInput(),
      'item_id'        => new sfWidgetFormFilterInput(),
      'delivery_count' => new sfWidgetFormFilterInput(),
      'stock_in'       => new sfWidgetFormFilterInput(),
      'stock_out'      => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'shop_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'cms_item_id'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'total'          => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'sold'           => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'book_out'       => new sfValidatorPass(array('required' => false)),
      'returned'       => new sfValidatorPass(array('required' => false)),
      'available'      => new sfValidatorPass(array('required' => false)),
      'created_at'     => new sfValidatorPass(array('required' => false)),
      'updated_at'     => new sfValidatorPass(array('required' => false)),
      'item_id'        => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'delivery_count' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'stock_in'       => new sfValidatorPass(array('required' => false)),
      'stock_out'      => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('inventory_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    parent::setup();
  }

  public function getModelName()
  {
    return 'Inventory';
  }

  public function getFields()
  {
    return array(
      'id'             => 'Number',
      'shop_id'        => 'Number',
      'cms_item_id'    => 'Number',
      'total'          => 'Number',
      'sold'           => 'Number',
      'book_out'       => 'Text',
      'returned'       => 'Text',
      'available'      => 'Text',
      'created_at'     => 'Text',
      'updated_at'     => 'Text',
      'item_id'        => 'Number',
      'delivery_count' => 'Number',
      'stock_in'       => 'Text',
      'stock_out'      => 'Text',
    );
  }
}
