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

    $templates = $this->get_all_mosaico_templates();
    $template_form_elements = [];

    foreach ($templates as $id => $name) {
      $this->add(
        'text',
        str_replace(" ","_", $name),
        E::ts($name),
        array("class" => "huge"),
        FALSE
      );
      $template_form_elements[] = str_replace(" ","_", $name);
    }
    $this->assign('template_names', $template_form_elements);


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

    civicrm_api3('Job', 'mosaico_msg_sync');
    parent::postProcess();
  }


  /**
   * @return array
   */
  private function get_all_mosaico_templates() {

    $matched_tempates = [];
    $result = civicrm_api3('MosaicoTemplate', 'get', [
      'sequential' => 1,
      'return' => ["title"],
      'options' => ['limit' => 0],
    ]);

    $config = CRM_Mosaicomsgtpl_Config::singleton();
    $settings = $config->getSettings();
    if (!empty($settings['mosaico_msg_template_name_filter'])) {
      $pattern = "/^{$settings['mosaico_msg_template_name_filter']}/";
      foreach ($result['values'] as $key => $template) {
        if (!preg_match ( $pattern , str_replace(" ","_", $template['title']), $matches )) {
          // TODO: check preg match, if match add to return value array
          continue;
        }
        $matched_tempates[$template['id']] = $template['title'];
      }
    }

    return $matched_tempates;
  }
}
