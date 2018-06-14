<?php

require_once 'pdf_contribution_receipt.civix.php';

use CRM_PdfContributionReceipt_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function pdf_contribution_receipt_civicrm_config(&$config)
{
    _pdf_contribution_receipt_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function pdf_contribution_receipt_civicrm_xmlMenu(&$files)
{
    _pdf_contribution_receipt_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function pdf_contribution_receipt_civicrm_install()
{
    _pdf_contribution_receipt_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function pdf_contribution_receipt_civicrm_postInstall()
{
    _pdf_contribution_receipt_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function pdf_contribution_receipt_civicrm_uninstall()
{
    _pdf_contribution_receipt_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function pdf_contribution_receipt_civicrm_enable()
{
    _pdf_contribution_receipt_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function pdf_contribution_receipt_civicrm_disable()
{
    _pdf_contribution_receipt_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function pdf_contribution_receipt_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL)
{
    return _pdf_contribution_receipt_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function pdf_contribution_receipt_civicrm_managed(&$entities)
{
    _pdf_contribution_receipt_civix_civicrm_managed($entities);
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
function pdf_contribution_receipt_civicrm_caseTypes(&$caseTypes)
{
    _pdf_contribution_receipt_civix_civicrm_caseTypes($caseTypes);
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
function pdf_contribution_receipt_civicrm_angularModules(&$angularModules)
{
    _pdf_contribution_receipt_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function pdf_contribution_receipt_civicrm_alterSettingsFolders(&$metaDataFolders = NULL)
{
    _pdf_contribution_receipt_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
 * function pdf_contribution_receipt_civicrm_preProcess($formName, &$form) {
 *
 * } // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function pdf_contribution_receipt_civicrm_navigationMenu(&$menu)
{

    /*
     * Add 'Contribution receipt templates' to 'Contribution' navigation menu
     */
    _pdf_contribution_receipt_civix_insert_navigation_menu($menu, 'Contributions', array(
        'label' => E::ts('PDF Receipt Templates'),
        'name' => 'pdf_contribution_receipt_templates',
        'url' => 'civicrm/pdf-contribution-receipt/templates',
        'permission' => 'administer CiviCRM',
        'operator' => 'OR',
        'separator' => 1,
    ));
}


/*
 * Add 'PDF contribution receipt' to contact 'Actions' in contact's profile summary page
 */
function pdf_contribution_receipt_civicrm_summaryActions(&$actions, $contactID)
{
    $actions['pdfreceipt'] = array(
        'title' => 'Create contribution receipt',
        'weight' => 999,
        'ref' => 'pdf-contribution-receipt',
        'key' => 'pdfdonre',
        'class' => 'no-popup',
        'href' => CRM_Utils_System::url('civicrm/pdf-contribution-receipt/generate/')
    );

    return $actions;
}


function pdf_contribution_receipt_civicrm_summary($contactID, &$content, &$contentPlacement = CRM_Utils_Hook::SUMMARY_BELOW)
{

    /*
     * Add plugin javascript to the view.
     */
    CRM_Core_Resources::singleton()->addScriptFile('eu.commondo.pdf-contribution-receipt', 'js/script.js');

    /*
     * Add plugin stylesheet to the view.
     */
    CRM_Core_Resources::singleton()->addStyleFile('eu.commondo.pdf-contribution-receipt', 'css/style.css');

}