<?php


use CRM_PdfContributionReceipt_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_PdfContributionReceipt_Form_GeneratePdf extends CRM_Core_Form
{

    public function buildQuickForm()
    {

        CRM_Utils_System::setTitle(E::ts('Generate PDF Contribution Receipt'));

        $contactId = CRM_Utils_Request::retrieve('cid', 'Integer');

        /*
         * Add 'Donation period' select field to the form.
         */
        $this->add(
            'select', // field type
            'donation-period', // field name
            'Donation period', // field label
            array(
                '' => '- select period - ',
                'current-year-contributions' => 'Current year contributions',
                'last-year-contributions' => 'Last year contributions',
                'most-recent-contribution' => 'Most recent contribution',
                'before-last-year-contribution' => 'Before last year contributions',
                'total-lifetime-contributions' => 'Total lifetime contributions',
                'custom' => 'Custom donation period'),
            TRUE
        );

        $this->addDateRange('custom-date');

        /*
         * Pull list of created templates from the database and show it as a select field.
         */
        $query = "SELECT * FROM civicrm_pdf_donation_receipt_templates";
        $templatesTable = CRM_Core_DAO::executeQuery($query);
        $templates = $templatesTable->fetchAll();

        $templateTitles = array(
            '' => '- select template -'
        );

        foreach ($templates as $template) {
            $templateTitles[$template['id']] = $template['title'];
        }

        /*
         * Add templates list select field to the form.
         */
        $this->add(
            'select', // field type
            'template', // field name
            'Receipt template', // field label
            $templateTitles,
            TRUE
        );

        /*
         * Add hidden field of contactId to the form so it can be used in post request.
         */
        $this->add(
            'hidden', // field type
            'contactId', // field name
            $contactId // field label
        );

        /*
         * Add submit button to the form.
         */
        $this->addButtons(array(
            array(
                'type' => 'submit',
                'name' => E::ts('Download PDF receipt'),
                'isDefault' => TRUE,
            ),
        ));

        $this->assign('elementNames', $this->getRenderableElementNames());
        $this->assign('contact', $this->getContactData($contactId));

        parent::buildQuickForm();
    }


    /*
     * Post process function to build and show PDF form according to selected options in the form.
     */
    public function postProcess()
    {
        $pdf = $this->getPdf();

        $values = $this->exportValues();
        $templateId = $values['template'];
        $contactId = $values['contactId'];
        $donationPeriod = $values['donation-period'];

        /*
         * Pull data for selected template (Title and HTML) to be filled in with custom data and transferred to PDF.
         */
        $query = "SELECT * FROM civicrm_pdf_donation_receipt_templates where id = %1";
        $sqlParams = array(
            1 => array($templateId, 'Integer'),
        );
        $templatesTable = CRM_Core_DAO::executeQuery($query, $sqlParams);
        $templates = $templatesTable->fetchAll();

        $html = $templates[0]['html'];

        /*
         * Go through html and replace short codes
         */
        $contact = $this->getContactData($contactId, $donationPeriod);


        /*
         * Available short codes. Pair short codes with contact data.
         */
        $shortcodes = array(
            'first_name' => $contact['first_name'],
            'last_name' => $contact['last_name'],
            'gender' => $contact['gender'],
            'contribution_amount' => $contact['amount'],
            'street_address' => $contact['street_address'],
            'postal_code' => $contact['postal_code'],
            'city' => $contact['city'],
            'donation_period' => str_replace('-', ' ', $donationPeriod),
            'date' => date('j.n.Y.'),
            'prefix' => $contact['individual_prefix'],
            'suffix' => $contact['individual_suffix']
        );


        /*
         * Replace short codes with data.
         */
        foreach ($shortcodes as $key => $shortcode){
            $sh_code = '[[' . $key . ']]';
            $html = str_replace($sh_code, $shortcode, $html);
        }

        /*
         * Output HTML content as a PDF and show/push for download to the user.
         */
        $pdf->writeHTML($html, true, false, true, false, '');
        $documentName = 'Donation receipt for ' . $contact['first_name'] . ' ' . $contact['last_name'] . ' - ' . ucfirst(str_replace('-', ' ', $donationPeriod));
        $pdf->SetTitle($documentName);
        $pdf->Output($documentName . '.pdf', 'D');
        die();

        parent::postProcess();
    }


    /**
     * Get the fields/elements defined in this form.
     *
     * @return array (string)
     */
    public function getRenderableElementNames()
    {
        // The _elements list includes some items which should not be
        // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
        // items don't have labels.  We'll identify renderable by filtering on
        // the 'label'.
        $elementNames = array();
        foreach ($this->_elements as $element) {
            /** @var HTML_QuickForm_Element $element */
            $label = $element->getLabel();
            if (!empty($label)) {
                $elementNames[] = $element->getName();
            }
        }
        return $elementNames;
    }


    /**
     * If your form requires special validation, add one or more callbacks here
     */
    public function addRules()
    {
        $this->addFormRule(array('CRM_PdfContributionReceipt_Form_GeneratePdf', 'myRules'));
    }


    /**
     * Here's our custom validation callback
     */
    public static function myRules($values)
    {

        if($values["donation-period"] == 'custom' and (empty($values["custom-date_from"]) and empty($values["custom-date_to"]))) {
            return array("custom-date_from" => "Custom date field must not be empty",
                        "custom-date_to" => "Custom date field must not be empty");
        }else if($values["donation-period"] == 'custom' and empty($values["custom-date_from"])) {
            return array("custom-date_from" => "Custom date field must not be empty");
        } else if ($values["donation-period"] == 'custom' and empty($values["custom-date_to"])) {
            return array("custom-date_to" => "Custom date field must not be empty");
        }

        return true;
    }


    /*
     * Return data about selected contact so that it can be used as a filler data to PDF template.
     */
    private function getContactData($contactId, $donationPeriod = 'most-recent-contribution')
    {
        $year = array(
            'current' => date('Y'),
            'last' => date('Y', strtotime("-1 year")),
            'before-last' => date('Y', strtotime("-2 year")),
            'before-before-last' => date('Y', strtotime("-3 year")),
            'next' => date('Y', strtotime("+1 year")),
        );

        $customDateTo = '';
        $customDateFrom = '';

        $values = $this->exportValues();

        if (isset($values['custom-date_to']) and isset($values['custom-date_from']))  {
            $customDateTo = $values['custom-date_to'];
            $customDateFrom = $values['custom-date_from'];
        }

        $donationTime = array(
            'total-lifetime-contributions' => NULL,
            'current-year-contributions' => array(
                'receive_date' => array('BETWEEN' => array($year['last'] . "-12-31", $year['next'] . "-01-01")),
            ),
            'last-year-contributions' => array(
                'receive_date' => array('BETWEEN' => array($year['before-last'] . "-12-31", $year['current'] . "-01-01")),
            ),
            'before-last-year-contribution' => array(
                'receive_date' => array('BETWEEN' => array($year['before-before-last'] . "-12-31", $year['before-last'] . "-01-01")),
            ),
            'most-recent-contribution' => array(
                'options' => array('sort' => "receive_date DESC")
            ),
            'custom' => array(
                'receive_date' => array('BETWEEN' => array($customDateFrom, $customDateTo)),
            )
        );


        $apiParams = array(
            'sequential' => 1,
            'return' => "total_amount, receive_date",
            'contact_id' => $contactId,
            'contribution_status_id' => "Completed");

        if($donationTime[$donationPeriod] != NULL) {
            $apiParams = array_merge($apiParams, $donationTime[$donationPeriod]);
        }


        // Api call to get Contact data
        $result = civicrm_api3('Contact', 'get', array(
            'sequential' => 1,
            'return' => "first_name,middle_name,last_name,gender_id,street_address,city, postal_code, prefix_id, suffix_id",
            'id' => $contactId,
        ));

        $resultContribution = civicrm_api3('Contribution', 'get', $apiParams);

        if ($donationPeriod == 'most-recent-contribution') {
            $result['values'][0]['amount'] = $resultContribution['values'][0]['total_amount'];
        } else {

            $totalAmount = 0;

            foreach ($resultContribution['values'] as $contribution){
                $totalAmount = $totalAmount + $contribution['total_amount'];
            }

            $result['values'][0]['amount'] = number_format((float)$totalAmount, 2, '.', '');;
        }

        $contact = $result['values'][0];
        $contact['donationPeriod'] = $donationPeriod;

        return $contact;

    }

    private function getPdf () {

        /*
         * Include tcpdf HTML to PDF library so it can be used in processing data.
         */
        $tcpdfPath = CRM_Core_Resources::singleton()->getPath('eu.commondo.pdf-contribution-receipt') . '/CRM/PdfContributionReceipt/Form/tcpdf/';
        \Composer\Autoload\includeFile($tcpdfPath . 'tcpdf.php');

        /*
         * Start building PDF file. Add all required data by library so that PDF file can be rendered correctly.
         */
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);

        // set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT, 15);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont('dejavusans', '', 12);

        // add a page
        $pdf->AddPage();

        return $pdf;

    }

}
