<?php
use CRM_Mosaicomsgtpl_ExtensionUtil as E;

/**
 * Job.mosaico_msg_sync API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_job_mosaico_msg_sync_spec(&$spec) {
  $spec['id'] = [
    'description'  => 'If given, only this template is sync-ed, otherwise all Mosaico templates are processed.',
    'api.required' => 0,
  ];
  $spec['is_new'] = [
    'description' => 'If true, the msg_tpl_id will be set to zero so that using Copy to create a new template does not duplicate the msg_tpl_id.',
  ];
}

/**
 * Job.mosaico_msg_sync API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_job_mosaico_msg_sync($params) {
  $count = 0;
  CRM_Core_Transaction::create()->run(function () use ($params, &$count) {

    $existingMosTplParams = array('options' => array('limit' => 0));
    if (isset($params['id'])) {
      $existingMosTplParams['id'] = CRM_Utils_Type::validate($params['id'], 'Positive');
    }
    $existingMosTpls = civicrm_api3('MosaicoTemplate', 'get', $existingMosTplParams);

    $config = CRM_Mosaicomsgtpl_Config::singleton();
    $settings = $config->getSettings();

    foreach ($existingMosTpls['values'] as $existingMosTpl) {
      if (!empty($settings['mosaico_msg_template_name_filter'])) {
        $pattern = "/^{$settings['mosaico_msg_template_name_filter']}/";
        if (!preg_match ( $pattern , $existingMosTpl['title'], $matches )) {
          continue;
        }
      }

      // Handle common parameters for things that can be updated...
      if (!empty($settings[$existingMosTpl['title']])) {
        // Use specifically configured title/subject
        $createParams = [
          'msg_html'    => _civicrm_api3_job_mosaico_msg_filter($existingMosTpl['html']),
          'msg_title'   => $existingMosTpl['title'],
          'msg_subject' => $settings[$existingMosTpl['title']],
        ];
      } else {
        // Split the Mosaico message title into title and subject.
        //
        // This is a big ugly, but Mosaico templates do not store a subject.
        // Being able to edit the subject of a message template is essential, but
        // being able to administer templates by an internal name is also a very
        // cool feature ("Initial welcome email").
        //
        // We allow the Mosaico title to include a subject following the | symbol.
        preg_match('/^(.+?)\s*[|]\s*(.+)$/', $existingMosTpl['title'], $_);
        $createParams = [
          'msg_html'    => _civicrm_api3_job_mosaico_msg_filter($existingMosTpl['html']),
          'msg_title'   => empty($_[1]) ? $existingMosTpl['title'] : $_[1],
          'msg_subject' => empty($_[2]) ? $existingMosTpl['title'] : $_[2], // default to title, as before.
        ];
      }


      // When a template is created from a Mosaico Base Template, it will not have a msg_tpl_id.
      // However when a template is created from a Copy of a MosaicoTemplate, it will come in
      // with the original MosaicoTemplate's msg_tpl_id, which is not what we want. Consult
      // `is_new` to determine this case (which is set in the post hook if the op was 'create').
      $isNewTpl = !isset($existingMosTpl['msg_tpl_id']) || !empty($params['is_new']);

      if ($isNewTpl) {
        // Need to create a new MessageTemplate.
        $createParams['is_reserved'] = 1;
        $createParams['msg_tpl_id']  = 0; // We set this later.
      }
      else {
        // Editing an existing MosaicoTemplate.
        $createParams['id'] = $existingMosTpl['msg_tpl_id'];
      }

      $result = civicrm_api3('MessageTemplate', 'create', $createParams);

      if ($isNewTpl) {
        // We're likely called after updating a MosaicoTemplate... don't recurse...
        CRM_Core_DAO::executeQuery('UPDATE civicrm_mosaico_template SET msg_tpl_id = %1 WHERE id = %2', array(
          1 => array($result['id'], 'Positive'),
          2 => array($existingMosTpl['id'], 'Positive'),
        ));
      }

      $count++;
    }
  });

  return civicrm_api3_create_success(array('processed' => $count), $params, 'Job', 'mosaico_msg_sync');
}
/**
 * Filter the HTML content.
 *
 * @param string $html
 *   Template HTML, as generated by Mosaico.
 * @return string
 *   Template HTML, as appropriate for MessageTemplates.
 */
function _civicrm_api3_job_mosaico_msg_filter($html) {
  if (defined('CIVICRM_MAIL_SMARTY') && CIVICRM_MAIL_SMARTY == 1) {
    // keep head section in literal to avoid smarty errors. Specially when CIVICRM_MAIL_SMARTY is turned on.
    $html = str_ireplace(array('<head>', '</head>'),
      array('{literal}<head>', '</head>{/literal}'), $html);
  }
  elseif (defined('CIVICRM_MAIL_SMARTY') && CIVICRM_MAIL_SMARTY == 0) {
    // get rid of any injected literal tags to avoid them appearing in emails
    $html = str_ireplace(array('{literal}<head>', '</head>{/literal}'),
      array('<head>', '</head>'), $html);
  }
  return $html;
}
