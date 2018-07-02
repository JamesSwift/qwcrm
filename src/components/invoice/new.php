<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

require(INCLUDES_DIR.'customer.php');
require(INCLUDES_DIR.'invoice.php');
require(INCLUDES_DIR.'workorder.php');

// Create an invoice for the supplied workorder
if(isset($VAR['workorder_id']) && $VAR['workorder_id'] && !get_workorder_details($VAR['workorder_id'], 'invoice_id')) {

    // Get Customer_id from the workorder    
    $VAR['customer_id'] = get_workorder_details($VAR['workorder_id'], 'customer_id');
    
    // Create the invoice and return the new invoice_id
    $VAR['invoice_id'] = insert_invoice($VAR['customer_id'], $VAR['workorder_id'], get_customer_details($VAR['customer_id'], 'discount_rate'));
    
    // Update the workorder with the new invoice_id
    update_workorder_invoice_id($VAR['workorder_id'], $VAR['invoice_id']);

    // Load the newly created invoice edit page
    force_page('invoice', 'edit&invoice_id='.$VAR['invoice_id']);
    
} 

// Invoice only
if((isset($VAR['customer_id'], $VAR['invoice_type']) && $VAR['customer_id'] && $VAR['invoice_type'] == 'invoice-only')) {
    
    // Create the invoice and return the new invoice_id
    $VAR['invoice_id'] = insert_invoice($VAR['customer_id'], '', get_customer_details($VAR['customer_id'], 'discount_rate'));

    // Load the newly created invoice edit page
    force_page('invoice', 'edit&invoice_id='.$VAR['invoice_id']);
}    
  
// Fallback Error Control 
force_page('workorder', 'search', 'warning_msg='._gettext("You cannot create an invoice by the method you just tried, report to admins."));
