<?php
/*-------------------------------------------------------+
| Mosaicomsgtpl Extension                                |
| Author: P. Batroff (batroff@systopia.de)               |
+--------------------------------------------------------*/

use CRM_Mosaicomsgtpl_ExtensionUtil as E;

/**
 * Configurations
 */
class CRM_Mosaicomsgtpl_Config {

  private static $singleton = NULL;

  /**
   * get the config instance
   */
  public static function singleton() {
    if (self::$singleton === NULL) {
      self::$singleton = new CRM_Mosaicomsgtpl_Config();
    }
    return self::$singleton;
  }

  /**
   *
   * @return array
   */
  public function getSettings() {
    $settings = CRM_Core_BAO_Setting::getItem('org.civicrm.mosaicomsgtpl', 'Mosaico_msg_tpl');
    return $settings;
  }

  /**
   *
   * @param $settings array
   */
  public function setSettings($settings) {
    CRM_Core_BAO_Setting::setItem($settings, 'org.civicrm.mosaicomsgtpl', 'Mosaico_msg_tpl');
  }

}