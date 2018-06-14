<?php
use CRM_PdfContributionReceipt_ExtensionUtil as E;

class CRM_PdfContributionReceipt_Page_Templates extends CRM_Core_Page {

  public function run() {

      CRM_Utils_System::setTitle(E::ts('Contribution PDF Receipt Templates'));

      /*
       * If DELETE flag in GET params, delete contribution template
       */
      if(isset($_GET['delete'])){
          $delete = $_GET['delete'];

          $query = "DELETE FROM civicrm_pdf_donation_receipt_templates WHERE id = %1";
          $sqlParams = array(
              '1' => array($delete, 'Integer')
          );
          CRM_Core_DAO::executeQuery($query, $sqlParams);

          CRM_Core_Session::setStatus(E::ts('Contribution PDF receipt template has been successfully deleted!'), '', 'success');

          CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/pdf-contribution-receipt/templates'));
      }

      /*
       * Load template list
       */
      $query = "SELECT * FROM civicrm_pdf_donation_receipt_templates";
      $templatesTable = CRM_Core_DAO::executeQuery($query);
      $templates = $templatesTable->fetchAll();
      $this->assign('templates', $templates);

      parent::run();
  }

}
