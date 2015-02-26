<?php
class LoginForm extends sfForm {
    public function configure() {

        $this->setWidgets(array(
            'pin' => new sfWidgetFormInput(),
            'password' => new sfWidgetFormInputPassword()));

        $this->setValidators(
            //new sfValidatorAnd(
                array('pin' => new sfValidatorPropelChoice(array('model' => 'User', 'column' => 'pin', 'required' => true),
                          array('required' => 'Invalid Pin','invalid' => 'Invalid Pin')),
                      'password' => new sfValidatorPropelChoice(array('model' => 'User', 'column' => 'password', 'required' => true),
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