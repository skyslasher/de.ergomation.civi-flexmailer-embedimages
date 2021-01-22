<?php

require_once 'civi_flexmailer_embedimages.civix.php';
// phpcs:disable
use CRM_CiviFlexmailerEmbedimages_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function civi_flexmailer_embedimages_civicrm_config(&$config) {
  _civi_flexmailer_embedimages_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function civi_flexmailer_embedimages_civicrm_xmlMenu(&$files) {
  _civi_flexmailer_embedimages_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function civi_flexmailer_embedimages_civicrm_install() {
  _civi_flexmailer_embedimages_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function civi_flexmailer_embedimages_civicrm_postInstall() {
  _civi_flexmailer_embedimages_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function civi_flexmailer_embedimages_civicrm_uninstall() {
  _civi_flexmailer_embedimages_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function civi_flexmailer_embedimages_civicrm_enable() {
  _civi_flexmailer_embedimages_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function civi_flexmailer_embedimages_civicrm_disable() {
  _civi_flexmailer_embedimages_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function civi_flexmailer_embedimages_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civi_flexmailer_embedimages_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function civi_flexmailer_embedimages_civicrm_managed(&$entities) {
  _civi_flexmailer_embedimages_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function civi_flexmailer_embedimages_civicrm_caseTypes(&$caseTypes) {
  _civi_flexmailer_embedimages_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function civi_flexmailer_embedimages_civicrm_angularModules(&$angularModules) {
  _civi_flexmailer_embedimages_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function civi_flexmailer_embedimages_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civi_flexmailer_embedimages_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function civi_flexmailer_embedimages_civicrm_entityTypes(&$entityTypes) {
  _civi_flexmailer_embedimages_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_themes().
 */
function civi_flexmailer_embedimages_civicrm_themes(&$themes) {
  _civi_flexmailer_embedimages_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function civi_flexmailer_embedimages_civicrm_preProcess($formName, &$form) {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
function civi_flexmailer_embedimages_civicrm_navigationMenu( &$menu ) {
  _civi_flexmailer_embedimages_civix_insert_navigation_menu(
    $menu, 'Mailings', array(
      'label' => E::ts( 'Advanced E-Mail settings' ),
      'name' => 'civi_flexmailer_embedimages_settings',
      'url' => 'civicrm/admin/flexmailer_embedimages',
      'permission' => 'access CiviMail',
      'operator' => 'OR',
      'separator' => 0,
    )
  );
  _civi_flexmailer_embedimages_civix_navigationMenu( $menu );
}

/*
 * hook into the send function to embed images
 */
function civi_flexmailer_embedimages_civicrm_container( $container )
{
  $container->addResource( new \Symfony\Component\Config\Resource\FileResource( __FILE__ ) );
  $container->findDefinition( 'dispatcher' )->addMethodCall(
    'addListener',
    array( \Civi\FlexMailer\FlexMailer::EVENT_SEND, '_civi_flexmailer_embedimages_send_batch' )
  );
}

function _civi_flexmailer_embedimages_send_batch( \Civi\FlexMailer\Event\SendBatchEvent $event)
{
  $line = "Listener _civi_flexmailer_embedimages_send_batch called";
  $dt = new DateTime();
  file_put_contents( '/var/www/vhosts/upgrade-jetzt.de/httpdocs/log/civi_flexmailer_embedimages.log',  "[" . $dt->format('Y-m-d\TH:i:s.u') . "] " . $line . "\n", FILE_APPEND | LOCK_EX );
  $EmbedSender = new CRM_CiviFlexmailerEmbedimages_EmbedSender();
  $EmbedSender->onSend( $event );
}
