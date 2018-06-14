<?php

use CRM_PdfContributionReceipt_ExtensionUtil as E;

/**
 * DonationTemplate.GetHtml API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_contributiontemplate_gethtml_spec(&$spec)
{
    $spec['templateId']['api.required'] = 1;
}

/**
 * DonationTemplate.GetHtml API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_contributiontemplate_gethtml($params)
{
    if (array_key_exists('templateId', $params) && is_int($params['templateId'])) {

        $query = "SELECT * FROM civicrm_pdf_donation_receipt_templates WHERE id = %1";
        $sqlParams = array(
            1 => array($params['templateId'], 'Integer'));
        $templatesTable = CRM_Core_DAO::executeQuery($query, $sqlParams);

        $template = $templatesTable->fetchAll();

        return civicrm_api3_create_success($template[0]);
    } else {
        throw new API_Exception(/*errorMessage*/
            'Contribution PDf template error."', /*errorCode*/
            1234);
    }
}
