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

  public function preProcess() {
    // Perform any setup tasks you may need
    // often involves grabbing args from the url and storing them in class variables
    $doFlush = CRM_Utils_Array::value('flush', $_GET, '0');
    if ( 1 == $doFlush ) {
      // this is just a dummy to get the file with the static class containing flushCache() loaded
      $temp = new CRM_CiviFlexmailerEmbedimages_EmbedSender();
      CRM_CiviFlexmailerEmbedimages_EmbedHTMLImages::flushCache();
      CRM_Core_Session::setStatus(ts('Image cache flushed'), '', 'no-popup');
    }
  }

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
