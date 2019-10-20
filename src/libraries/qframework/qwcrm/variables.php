<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

################################################
#  Load and set the System's global variables  #
################################################

function load_system_variables() {
    
    $smarty = \QFactory::getSmarty();
    
    if(!defined('QWCRM_SETUP')) {
        $company_details = get_company_details();
    }

    /* Acquire variables from classes examples */
    // $user->login_user_id;                                    // This is a public variable defined in the class
    // \QFactory::getUser()->login_user_id;                      // Static method to get variable
    // \QFactory::getConfig()->get('sef')                        // This is a variable stored in the registry
    // $config = \QFactory::getConfig();  | $config->get('sef')  // Get the config into a variable and then you can call the config settings
    // $QConfig->sef                                            // Only works for the QConfig class I made in the root

    ##########################################################
    #   Assign Global PHP Variables                          #
    ##########################################################
   
    // If there are DATABASE ERRORS, they will present here (white screen) when verify QWcrm function is not on
    if(!defined('QWCRM_SETUP')) {
        define('DATE_FORMAT',   $company_details['date_format']);
        define('QW_TAX_SYSTEM', $company_details['tax_system'] );
    }

    ##########################################################################
    #   Assign variables into Smarty for use by component templates          #
    ##########################################################################

    // QWcrm System Folders
    $smarty->assign('base_path',                QWCRM_BASE_PATH             );      // set base path, useful for javascript links i.e. 404.tpl
    $smarty->assign('media_dir',                QW_MEDIA_DIR                );      // set media directory

    // QWcrm Theme Directory Template Variables
    $smarty->assign('theme_dir',                THEME_DIR                   );      // set theme directory
    $smarty->assign('theme_images_dir',         THEME_IMAGES_DIR            );      // set theme images directory
    $smarty->assign('theme_css_dir',            THEME_CSS_DIR               );      // set theme CSS directory
    $smarty->assign('theme_js_dir',             THEME_JS_DIR                );      // set theme JS directory

    // QWcrm Theme Directory Template Smarty File Include Path Variables
    $smarty->assign('theme_js_dir_finc',        THEME_JS_DIR_FINC           );
    
    // This assigns framework globals to smarty and also prevents undefined variable errors (mainly for the menu)
    isset(\QFactory::$VAR['user_id'])          ? $smarty->assign('user_id', \QFactory::$VAR['user_id'])               : $smarty->assign('user_id', null);
    isset(\QFactory::$VAR['employee_id'])      ? $smarty->assign('employee_id', \QFactory::$VAR['employee_id'])       : $smarty->assign('employee_id', null);
    isset(\QFactory::$VAR['client_id'])        ? $smarty->assign('client_id', \QFactory::$VAR['client_id'])           : $smarty->assign('client_id', null);
    isset(\QFactory::$VAR['workorder_id'])     ? $smarty->assign('workorder_id', \QFactory::$VAR['workorder_id'])     : $smarty->assign('workorder_id', null);
    isset(\QFactory::$VAR['schedule_id'])      ? $smarty->assign('schedule_id', \QFactory::$VAR['schedule_id'])       : $smarty->assign('schedule_id', null);
    isset(\QFactory::$VAR['invoice_id'])       ? $smarty->assign('invoice_id', \QFactory::$VAR['invoice_id'])         : $smarty->assign('invoice_id', null);
    isset(\QFactory::$VAR['voucher_id'])       ? $smarty->assign('voucher_id', \QFactory::$VAR['voucher_id'])         : $smarty->assign('voucher_id', null); 
    isset(\QFactory::$VAR['payment_id'])       ? $smarty->assign('payment_id', \QFactory::$VAR['payment_id'])         : $smarty->assign('payment_id', null);
    isset(\QFactory::$VAR['refund_id'])        ? $smarty->assign('refund_id', \QFactory::$VAR['refund_id'])           : $smarty->assign('refund_id', null);
    isset(\QFactory::$VAR['expense_id'])       ? $smarty->assign('expense_id', \QFactory::$VAR['expense_id'])         : $smarty->assign('expense_id', null);    
    isset(\QFactory::$VAR['otherincome_id'])   ? $smarty->assign('otherincome_id', \QFactory::$VAR['otherincome_id']) : $smarty->assign('otherincome_id', null);      
    isset(\QFactory::$VAR['supplier_id'])      ? $smarty->assign('supplier_id', \QFactory::$VAR['supplier_id'])       : $smarty->assign('supplier_id', null);    
   
    // Used throughout the site
    if(!defined('QWCRM_SETUP')) {
        $smarty->assign('currency_sym',  $company_details['currency_symbol']     );
        $smarty->assign('company_logo',  QW_MEDIA_DIR . $company_details['logo'] );
        $smarty->assign('qw_tax_system', QW_TAX_SYSTEM                           ); 
        $smarty->assign('date_format',   DATE_FORMAT                             );
    }
    
    #############################
    #        Exit Function      #
    #############################
    
    return;
    
}

######################################
#  Set the Message Smarty Variables  #
######################################

function smarty_set_system_messages() {
    
    $smarty = \QFactory::getSmarty();
    
    // Build Information Message (Green)
    \QFactory::$VAR['information_msg'] = isset(\QFactory::$VAR['information_msg']) ? \QFactory::$VAR['information_msg'] : null;
    $smarty->assign('information_msg', \QFactory::$VAR['information_msg']);

    // Build Warning Message (Red)
    \QFactory::$VAR['warning_msg'] = isset(\QFactory::$VAR['warning_msg']) ? \QFactory::$VAR['warning_msg'] : null;
    $smarty->assign('warning_msg', \QFactory::$VAR['warning_msg']);
    
    return;
    
}

#####################################
#  Set the User's Smarty Variables  #  // Empty if not logged in or installing (except for usergroup)
#####################################

function smarty_set_user_variables() {
    
    $smarty = \QFactory::getSmarty();
    
    if(!defined('QWCRM_SETUP')) {
    
        $user = \QFactory::getUser();    
    
        $smarty->assign('login_user_id',            $user->login_user_id          );
        $smarty->assign('login_username',           $user->login_username         );
        $smarty->assign('login_usergroup_id',       $user->login_usergroup_id     );
        $smarty->assign('login_display_name',       $user->login_display_name     );
        $smarty->assign('login_token',              $user->login_token            );
        $smarty->assign('login_is_employee',        $user->login_is_employee      );
        $smarty->assign('login_client_id',          $user->login_client_id        );
    
    }
    
    return;
    
}