<?php
/**
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

defined('_QWEXEC') or die;

// check if we have a client_note_id
if(!isset(\CMSApplication::$VAR['client_note_id']) || !\CMSApplication::$VAR['client_note_id']) {
    systemMessagesWrite('danger', _gettext("No Client Note ID supplied."));
    force_page('client', 'search');
}

// If record submitted for updating
if(isset(\CMSApplication::$VAR['submit'])) {
               
    update_client_note(\CMSApplication::$VAR['client_note_id'], \CMSApplication::$VAR['note']);
    force_page('client', 'details&client_id='.get_client_note_details(\CMSApplication::$VAR['client_note_id'], 'client_id'));   
    
} else {    
        
    $smarty->assign('client_note_details', get_client_note_details(\CMSApplication::$VAR['client_note_id']));
    
}


