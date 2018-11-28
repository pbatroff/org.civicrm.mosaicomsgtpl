<?php
/*-------------------------------------------------------+
| Mosaicomsgtpl Extension                                |
| Author: P. Batroff (batroff@systopia.de)               |
+--------------------------------------------------------*/

use CRM_Mosaicomsgtpl_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Mosaicomsgtpl_Form_Settings extends CRM_Core_Form {
  public function buildQuickForm() {

    // add form elements
    $this->add(
      'text',
      'mosaico_msg_template_name_filter',
      E::ts('Regex for Mosaico Message Filter'),
      array("class" => "huge"),
      FALSE
    );

    $this->add(
      'advcheckbox',
      'mosaico_global_sync_activated',
      E::ts('Activate Global Template Synchronization')
    );

    // submit
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    parent::buildQuickForm();
  }

  /**
   * set the default (=current) values in the form
   */
  public function setDefaultValues() {
    $config = CRM_Mosaicomsgtpl_Config::singleton();
    return $config->getSettings();
  }

  /**
   * Post process input values and save them to DB
   */
  public function postProcess() {
    $config = CRM_Mosaicomsgtpl_Config::singleton();
    $values = $this->exportValues();
    $config->setSettings($values);

    parent::postProcess();
  }

}
