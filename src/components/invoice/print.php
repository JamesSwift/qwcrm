<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

// Check if we have an invoice_id
if(!isset(\CMSApplication::$VAR['invoice_id']) || !\CMSApplication::$VAR['invoice_id']) {
    $this->app->system->variables->systemMessagesWrite('danger', _gettext("No Invoice ID supplied."));
    $this->app->system->page->forcePage('invoice', 'search');
}

// Check there is a print content and print type set
if(!isset(\CMSApplication::$VAR['print_content'], \CMSApplication::$VAR['print_type']) || !\CMSApplication::$VAR['print_content'] || !\CMSApplication::$VAR['print_type']) {
    $this->app->system->variables->systemMessagesWrite('danger', _gettext("Some or all of the Printing Options are not set."));
    $this->app->system->page->forcePage('invoice', 'search');
}

// Get Record Details
$invoice_details = $this->app->components->invoice->getRecord(\CMSApplication::$VAR['invoice_id']);
$client_details = $this->app->components->client->getRecord($invoice_details['client_id']);

// Only show payment instruction if bank_transfer|cheque|PayPal is enabled, these are the only valid instructions you can put on an invoice
$payment_methods = $this->app->components->payment->getMethods('receive', true);
$display_payment_instructions = false;
foreach ($payment_methods as $key => $value) {
    if(
        ($value['method_key'] == 'bank_transfer' && $value['enabled']) ||
        ($value['method_key'] == 'cheque' && $value['enabled']) ||
        ($value['method_key'] == 'paypal' && $value['enabled'])
    ) {
        $display_payment_instructions = true;
    }
}

// Details
$this->app->smarty->assign('company_details',                  $this->app->components->company->getRecord()                                      );
$this->app->smarty->assign('client_details',                   $client_details                                            );
$this->app->smarty->assign('workorder_details',                $this->app->components->workorder->getRecord($invoice_details['workorder_id'])    );
$this->app->smarty->assign('invoice_details',                  $invoice_details                                           );

// Prefill Items
$this->app->smarty->assign('vat_tax_codes',                    $this->app->components->company->getVatTaxCodes(false)                                                               );

// Invoice Items
$this->app->smarty->assign('labour_items',                     $this->app->components->invoice->getLabourItems(\CMSApplication::$VAR['invoice_id'])               );
$this->app->smarty->assign('parts_items',                      $this->app->components->invoice->getPartsItems(\CMSApplication::$VAR['invoice_id'])                );
$this->app->smarty->assign('display_vouchers',                 $this->app->components->voucher->getRecords('voucher_id', 'DESC', false, '25', null, null, null, null, null, null, null, \CMSApplication::$VAR['invoice_id']) );

// Sub Totals
$this->app->smarty->assign('labour_items_subtotals',          $this->app->components->invoice->getLabourItemsSubtotals(\CMSApplication::$VAR['invoice_id'])                                                          );
$this->app->smarty->assign('parts_items_subtotals',           $this->app->components->invoice->getPartsItemsSubtotals(\CMSApplication::$VAR['invoice_id'])                                                           );
$this->app->smarty->assign('voucher_subtotals',               $this->app->components->voucher->getInvoiceVouchersSubtotals(\CMSApplication::$VAR['invoice_id'])                                                       );

// Payment Details
$this->app->smarty->assign('payment_options',                  $this->app->components->payment->getOptions()                                      );
$this->app->smarty->assign('payment_methods',                  $payment_methods                                           );

// Misc
$this->app->smarty->assign('display_payment_instructions',     $display_payment_instructions                              );
$this->app->smarty->assign('employee_display_name',            $this->app->components->user->getRecord($invoice_details['employee_id'], 'display_name')  );
$this->app->smarty->assign('invoice_statuses',                 $this->app->components->invoice->getStatuses()                                     );

// Invoice Print Routine
if(\CMSApplication::$VAR['print_content'] == 'invoice')
{    
    // Build the PDF filename
    $pdf_filename = _gettext("Invoice").'-'.\CMSApplication::$VAR['invoice_id'].'.pdf';
    
    // Print HTML Invoice
    if (\CMSApplication::$VAR['print_type'] == 'print_html')
    {        
        // Log activity
        $record = _gettext("Invoice").' '.\CMSApplication::$VAR['invoice_id'].' '._gettext("has been printed as html.");
        $this->app->system->general->writeRecordToActivityLog($record, $invoice_details['employee_id'], $invoice_details['client_id'], $invoice_details['workorder_id'], $invoice_details['invoice_id']);
        
        // Assign the correct version of this page
        $this->app->smarty->assign('print_content', \CMSApplication::$VAR['print_content']);
        
    }
    
    // Print PDF Invoice
    if (\CMSApplication::$VAR['print_type'] == 'print_pdf')
    {        
        // Get Print Invoice as HTML into a variable
        $pdf_template = $this->app->smarty->fetch('invoice/printing/print_invoice.tpl');
        
        // Log activity
        $record = _gettext("Invoice").' '.\CMSApplication::$VAR['invoice_id'].' '._gettext("has been printed as a PDF.");
        $this->app->system->general->writeRecordToActivityLog($record, $invoice_details['employee_id'], $invoice_details['client_id'], $invoice_details['workorder_id'], $invoice_details['invoice_id']);
        
        // Output PDF in brower
        $this->app->system->pdf->mpdfOutputBrowser($pdf_filename, $pdf_template);
        
        // End all other processing
        die();        
    }        
        
    // Email PDF Invoice
    if(\CMSApplication::$VAR['print_type'] == 'email_pdf')
    {                
        // Get Print Invoice as HTML into a variable
        $pdf_template = $this->app->smarty->fetch('invoice/printing/print_invoice.tpl');
        
        // Get the PDF in a variable
        $pdf_as_string = $this->app->system->pdf->mpdfOutputVariable($pdf_template);
                
        // Build and Send email
        if($pdf_as_string)
        {        
            // Build the PDF Attachment
            $attachments = array();
            $attachment['data'] = $pdf_as_string;
            $attachment['filename'] = $pdf_filename;
            $attachment['contentType'] = 'application/pdf';
            $attachments[] = $attachment;

            // Build the message body        
            $body = $this->app->system->email->getEmailMessageBody('email_msg_invoice', $client_details);

            // Log activity
            $record = _gettext("Invoice").' '.\CMSApplication::$VAR['invoice_id'].' '._gettext("has been emailed as a PDF.");
            $this->app->system->general->writeRecordToActivityLog($record, $invoice_details['employee_id'], $invoice_details['client_id'], $invoice_details['workorder_id'], $invoice_details['invoice_id']);

            // Email the PDF        
            $this->app->system->email->send($client_details['email'], _gettext("Invoice").' '.\CMSApplication::$VAR['invoice_id'], $body, $client_details['display_name'], $attachments, $invoice_details['employee_id'], $invoice_details['client_id'], $invoice_details['workorder_id'], \CMSApplication::$VAR['invoice_id']);           
        }        
        // End all other processing
        die();        
    }
    
    // Download PDF Invoice
    if (\CMSApplication::$VAR['print_type'] == 'download_pdf')
    {        
        // Get Print Invoice as HTML into a variable
        $pdf_template = $this->app->smarty->fetch('invoice/printing/print_invoice.tpl');
        
        // Log activity
        $record = _gettext("Invoice").' '.\CMSApplication::$VAR['invoice_id'].' '._gettext("has been dowloaded as a PDF.");
        $this->app->system->general->writeRecordToActivityLog($record, $invoice_details['employee_id'], $invoice_details['client_id'], $invoice_details['workorder_id'], $invoice_details['invoice_id']);
        
        // Output PDF in brower
        $this->app->system->pdf->mpdfOutputFile($pdf_filename, $pdf_template);
        
        // End all other processing
        die();        
    }   
    
}

// Client Envelope Print Routine
if(\CMSApplication::$VAR['print_content'] == 'client_envelope')
{    
    // Print HTML Client Envelope
    if (\CMSApplication::$VAR['print_type'] == 'print_html')
    {        
        // Log activity
        $record = _gettext("Address Envelope").' '._gettext("for").' '.$client_details['display_name'].' '._gettext("has been printed as html.");
        $this->app->system->general->writeRecordToActivityLog($record, $invoice_details['employee_id'], $invoice_details['client_id'], $invoice_details['workorder_id'], $invoice_details['invoice_id']);
        
        // Assign the correct version of this page
        $this->app->smarty->assign('print_content', \CMSApplication::$VAR['print_content']);
        
    }    
}