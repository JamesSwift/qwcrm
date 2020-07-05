<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

// Check if we have a supplier_id
if(!isset(\CMSApplication::$VAR['supplier_id']) || !\CMSApplication::$VAR['supplier_id']) {
    $this->app->system->variables->systemMessagesWrite('danger', _gettext("No Supplier ID supplied."));
    $this->app->system->page->forcePage('supplier', 'search');
}

// Update Supplier Status
if(isset(\CMSApplication::$VAR['change_status'])){
    $this->app->system->general->updateStatus(\CMSApplication::$VAR['supplier_id'], \CMSApplication::$VAR['assign_status']);    
    $this->app->system->page->forcePage('supplier', 'status&supplier_id='.\CMSApplication::$VAR['supplier_id']);
}

// Build the page with the current status from the database
$this->app->smarty->assign('allowed_to_change_status',     false       );
$this->app->smarty->assign('supplier_status',              $this->app->components->supplier->getRecord(\CMSApplication::$VAR['supplier_id'], 'status')             );
$this->app->smarty->assign('supplier_statuses',            $this->app->components->supplier->getStatuses() );
$this->app->smarty->assign('allowed_to_cancel',            false      );
$this->app->smarty->assign('allowed_to_delete',            $this->app->components->supplier->checkRecordAllowsDelete(\CMSApplication::$VAR['supplier_id'])              );
$this->app->smarty->assign('supplier_selectable_statuses',     $this->app->components->supplier->getStatuses(true) );