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
      'created_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'     => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
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
      'book_out'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'returned'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'available'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'updated_at'     => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDate(array('required' => false)))),
      'item_id'        => new sfValidatorPass(array('required' => false)),
      'delivery_count' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
<<<<<<< HEAD
      'stock_in'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'stock_out'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
=======
<<<<<<< HEAD
      'stock_in'       => new sfValidatorPass(array('required' => false)),
      'stock_out'      => new sfValidatorPass(array('required' => false)),
=======
      'stock_in'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'stock_out'      => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
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
      'book_out'       => 'Number',
      'returned'       => 'Number',
      'available'      => 'Number',
      'created_at'     => 'Date',
      'updated_at'     => 'Date',
      'item_id'        => 'Text',
      'delivery_count' => 'Number',
<<<<<<< HEAD
      'stock_in'       => 'Number',
      'stock_out'      => 'Number',
=======
<<<<<<< HEAD
      'stock_in'       => 'Text',
      'stock_out'      => 'Text',
=======
      'stock_in'       => 'Number',
      'stock_out'      => 'Number',
>>>>>>> b799f6effd83b9aae0363e84c6d3a2dc50eae23c
>>>>>>> b7ab7e902388d4ed3cc2a72d76c598b91bc5b602
    );
  }
}
