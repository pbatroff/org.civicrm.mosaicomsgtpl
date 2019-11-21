<?php

require_once 'mosaicomsgtpl.civix.php';
use CRM_Mosaicomsgtpl_ExtensionUtil as E;


function mosaicomsgtpl_civicrm_post($op, $objectName, $objectId, &$objectRef = NULL) {
  $config = CRM_Mosaicomsgtpl_Config::singleton();
  $settings = $config->getSettings();
  if (!$settings['mosaico_global_sync_activated']) {
    return;
  }

  if (($op === 'create' || $op === 'edit') && $objectName === 'MosaicoTemplate') {
    if (Civi::settings()->get('mosaicomsgtpl_suspend')) {
      return;
    }
    civicrm_api3('Job', 'mosaico_msg_sync', array(
      'id'     => $objectId,
      'is_new' => ($op === 'create'),
    ));
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function mosaicomsgtpl_civicrm_config(&$config) {
  _mosaicomsgtpl_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function mosaicomsgtpl_civicrm_xmlMenu(&$files) {
  _mosaicomsgtpl_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function mosaicomsgtpl_civicrm_install() {
  _mosaicomsgtpl_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function mosaicomsgtpl_civicrm_postInstall() {
  _mosaicomsgtpl_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function mosaicomsgtpl_civicrm_uninstall() {
  _mosaicomsgtpl_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function mosaicomsgtpl_civicrm_enable() {
  _mosaicomsgtpl_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function mosaicomsgtpl_civicrm_disable() {
  _mosaicomsgtpl_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function mosaicomsgtpl_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mosaicomsgtpl_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function mosaicomsgtpl_civicrm_managed(&$entities) {
  _mosaicomsgtpl_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mosaicomsgtpl_civicrm_caseTypes(&$caseTypes) {
  _mosaicomsgtpl_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules
 */
function mosaicomsgtpl_civicrm_angularModules(&$angularModules) {
  _mosaicomsgtpl_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function mosaicomsgtpl_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _mosaicomsgtpl_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_alterMailContent
 *
 * Replaces Mosaico template custom tokens with proper CiviMail tokens.
 *
 * Note that this can still result in broken links if the mail is not sent
 * using CiviMail, but that's a user-error not a coding one. (Anyone can put
 * the {action.unsubscribeUrl} token in a message template but it's not going
 * to work since there's nothing to unsubscribe *from*.)
 *
 * @see https://github.com/civicrm/org.civicrm.mosaicomsgtpl/issues/6
 * @see https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterMailContent/
 *
 * @param Array $content - fields include: html, text, subject, groupName, valueName, messageTemplateID
 *
 */
function mosaicomsgtpl_civicrm_alterMailContent(&$content) {

  if (!empty($content['messageTemplateID'])) {
    // This mailing was created by a Message Template, so it could have the Mosaico-based custom tokens in it.
    $replacements = [
      '[unsubscribe_link]' => '{action.unsubscribeUrl}',
      '[show_link]'        => '{mailing.viewUrl}',
    ];
    $content['html'] = strtr($content['html'], $replacements);
    $content['text'] = strtr($content['text'], $replacements);
  }

}
// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function mosaicomsgtpl_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 *
function mosaicomsgtpl_civicrm_navigationMenu(&$menu) {
  _mosaicomsgtpl_civix_insert_navigation_menu($menu, NULL, array(
    'label' => E::ts('The Page'),
    'name' => 'the_page',
    'url' => 'civicrm/the-page',
    'permission' => 'access CiviReport,access CiviContribute',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _mosaicomsgtpl_civix_navigationMenu($menu);
} // */
