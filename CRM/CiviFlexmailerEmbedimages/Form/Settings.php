<?php

use CRM_CiviFlexmailerEmbedimages_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_CiviFlexmailerEmbedimages_Form_Settings extends CRM_Admin_Form_Setting {

  protected $_settings = array(
    'civi_flexmailer_embedimages' => 'Embed images into mail',
    'civi_flexmailer_embedimageslocal' => 'Embed only local images'
  );

  public function postProcess() {
    $formValues = $this->controller->exportValues( $this->_name );
    Civi::settings()->set(
      'civi_flexmailer_embedimages',
      ( !empty( $formValues[ 'civi_flexmailer_embedimages' ] ) )
    );
    Civi::settings()->set(
      'civi_flexmailer_embedimageslocal',
      ( !empty( $formValues[ 'civi_flexmailer_embedimageslocal' ] ) )
    );
  }

}
