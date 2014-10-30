<?php

class Items extends BaseItems
{
    public function configure()
  {
        $this->widgetSchema['original_pic'] = new sfWidgetFormInputFile(array(
      'label' => 'Image',
    ));
        
        $this->validatorSchema['original_pic'] = new sfValidatorFile(array(
    'required'   => false,
    'path'       => sfConfig::get('sf_upload_dir').'/images',
    'mime_types' => 'web_images',
    ));
  }
}
