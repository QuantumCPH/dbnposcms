<?php
class ResellerLoginForm extends sfForm {
    public function configure() {

        $this->setWidgets(array(
            'user_name' => new sfWidgetFormInput(),
            'password' => new sfWidgetFormInputPassword()));

        $this->setValidators(
            //new sfValidatorAnd(
                array('user_name' => new sfValidatorPropelChoice(array('model' => 'Reseller', 'column' => 'user_name', 'required' => true),
                          array('required' => 'Invalid username','invalid' => 'Invalid username')),
                      'password' => new sfValidatorPropelChoice(array('model' => 'Reseller', 'column' => 'password', 'required' => true),
                          array('required' => 'Invalid password','invalid' => 'Invalid password'))
                     )
              //  )
            );

        //$this->sfWidget->setLabel('email',__('email'));
        //$this->sfWidget->setLabel('password',__('password'));
        //$this->widgetSchema->setFormFormatterName('table');
        $this->widgetSchema->setNameFormat('login[%s]');

    }
}
?>