<?php

use CRM_PdfContributionReceipt_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_PdfContributionReceipt_Form_NewTemplate extends CRM_Core_Form
{
    public function buildQuickForm()
    {
        /*
         * Add plugin stylesheet to view.
         */
        CRM_Core_Resources::singleton()->addStyleFile('eu.commondo.pdf-contribution-receipt', 'css/style.css');

        $titleValue = '';
        CRM_Utils_System::setTitle(E::ts('Create new contribution PDF Template'));

        /*
         * If EDIT flag in GET params, edit contribution template and show it's data in the form.
         */
        if (isset($_GET['edit'])) {
            $templateId = (int)$_GET['edit'];
        }

        if (isset($templateId) AND is_int($templateId)) {

            $query = "SELECT * FROM civicrm_pdf_donation_receipt_templates WHERE id = %1";
            $sqlParams = array(
                1 => array($templateId, 'Integer'));
            $templatesTable = CRM_Core_DAO::executeQuery($query, $sqlParams);
            $template = $templatesTable->fetchAll();

            $titleValue = $template[0]['title'];

            CRM_Utils_System::setTitle(E::ts('Editing: ' . $titleValue));

            $this->add('hidden', 'template_id', $templateId);
            $this->assign('templateId', $templateId);

            CRM_Core_Resources::singleton()->addScriptFile('eu.commondo.pdf-contribution-receipt', 'js/editCKEditor.js');
        }

        /*
         * Add 'Title' text field to the form.
         */
        $this->add('text', 'title', ts('Template title:'), array('value' => $titleValue, 'size' => 50, 'class' => "title-text-field"), TRUE);

        /*
         * Add 'Wysiwyg html editor'  text field to the form.
         */
        $this->addWysiwyg(
            'html_template',
            ts('PDF template body:'),
            array(
                'cols' => '80',
                'rows' => '120'
            )
        );

        /*
         * Add 'Submit' button to the form.
         */
        $this->addButtons(array(
            array(
                'type' => 'submit',
                'name' => E::ts('Save template'),
                'isDefault' => TRUE,
            ),
        ));

        // export form elements
        $this->assign('elementNames', $this->getRenderableElementNames());

        parent::buildQuickForm();
    }

    public function postProcess()
    {
        $values = $this->exportValues();
        $title = $values['title'];
        $html = $values['html_template'];

        /*
         * If templateId is present, update template, otherwise create new template.
         */
        if (!isset($_POST['template_id'])) {

            $query = "INSERT INTO civicrm_pdf_donation_receipt_templates (title, html) VALUES (%1, %2)";
            $sqlParams = array(
                1 => array($title, 'String'),
                2 => array($html, 'String'));
            CRM_Core_DAO::executeQuery($query, $sqlParams);
            $templateId = CRM_Core_DAO::singleValueQuery('SELECT LAST_INSERT_ID()');

            CRM_Core_Session::setStatus(E::ts('Contribution PDF template has been successfully created!'), '', 'success');

        } else {

            $templateId = $_POST['template_id'];

            $query = "UPDATE civicrm_pdf_donation_receipt_templates SET title = %1, html=  %2 WHERE id = %3";
            $sqlParams = array(
                1 => array($title, 'String'),
                2 => array($html, 'String'),
                3 => array($templateId, 'Integer'));
            CRM_Core_DAO::executeQuery($query, $sqlParams);

            CRM_Core_Session::setStatus(E::ts('Contribution PDF template has been successfully edited!'), '', 'success');

        }

        parent::postProcess();

        CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/pdf-contribution-receipt/new-template', 'edit=' . $templateId));
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

}
