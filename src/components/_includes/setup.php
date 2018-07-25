<?php

/*
 * @package   QWcrm
 * @author    Jon Brown https://quantumwarp.com/
 * @copyright Copyright (C) 2016 - 2017 Jon Brown, All rights reserved.
 * @license   GNU/GPLv3 or later; https://www.gnu.org/licenses/gpl.html
 */

/*
 * Mandatory Code - Code that is run upon the file being loaded
 * Display Functions - Code that is used to primarily display records - linked tables
 * New/Insert Functions - Creation of new records
 * Get Functions - Grabs specific records/fields ready for update - no table linking
 * Update Functions - For updating records/fields
 * Close Functions - Closing Work Orders code
 * Delete Functions - Deleting Work Orders
 * Other Functions - All other functions not covered above
 */

defined('_QWEXEC') or die;

/** Mandatory Code **/

/** Display Functions **/

/** Insert Functions **/

/** Get Functions **/

/** Update Functions **/

/** Close Functions **/

/** Delete Functions **/

/** Other Functions **/

/** Common **/

#########################################################
#       update a value in a specified record            #
#########################################################

function update_record_value($select_table, $select_column, $record_identifier, $record_column, $record_new_value) {
    
    $db = QFactory::getDbo();    
    global $executed_sql_results;
    global $setup_error_flag;
    
    $sql = "UPDATE $select_table SET
            $record_column         =". $db->qstr( $record_new_value )."                      
            WHERE $select_column   =". $db->qstr( $record_identifier  );

    if(!$rs = $db->execute($sql)) { 
        
        // Set the setup global error flag
        $setup_error_flag = true;
        
        // Log message
        $record = _gettext("Failed to update the value").' '._gettext("for the record").' `'.$record_identifier.'` '._gettext("to").' `'.$record_new_value.'` '._gettext("in the columm").' `'.$record_column.'` '._gettext("from the table").' `'.$select_table.'` ';

        // Output message via smarty
        $executed_sql_results .= '<div style="color: red">'.$record.'</div>';
        $executed_sql_results .= '<div>&nbsp;</div>';
        
        // Log message to setup log        
        write_record_to_setup_log('correction', $record, $db->ErrorMsg(), $sql);
        
        return false;
        
    } else {
        
        // Log message
        $record = _gettext("Successfully updated the value").' '._gettext("for the record").' `'.$record_identifier.'` '._gettext("to").' `'.$record_new_value.'` '._gettext("in the columm").' `'.$record_column.'` '._gettext("from the table").' `'.$select_table.'` ';
                
        // Output message via smarty - to reduce onscreen output i have disabled success output, it is still logged
        //$executed_sql_results .= '<div style="color: green">'.$record.'</div>';
        //$executed_sql_results .= '<div>&nbsp;</div>';
        
        // Log message to setup log        
        write_record_to_setup_log('correction', $record);
        
        return true;
        
        
    }    
    
}

#########################################################
#   update all matching values in a column to new value #
#########################################################

function update_column_values($table, $column, $current_value, $new_value) {
    
    $db = QFactory::getDbo();    
    global $executed_sql_results;
    global $setup_error_flag;
    
    if($current_value === '*') {
        
        $sql = "UPDATE $table SET
                $column         =". $db->qstr( $new_value       );
        
    } else {
        
        $sql = "UPDATE $table SET
                $column         =". $db->qstr( $new_value       )."                      
                WHERE $column   =". $db->qstr( $current_value   );
        
    }

    if(!$rs = $db->execute($sql)) { 
        
        // Set the setup global error flag
        $setup_error_flag = true;
        
        // Log message
        $record = _gettext("Failed to update the values").' `'.$current_value.'` '._gettext("to").' `'.$new_value.'` '._gettext("in the columm").' `'.$column.'` '._gettext("from the table").' `'.$table.'` ';

        // Output message via smarty
        $executed_sql_results .= '<div style="color: red">'.$record.'</div>';
        $executed_sql_results .= '<div>&nbsp;</div>';        
        
        // Log message to setup log        
        write_record_to_setup_log('correction', $record, $db->ErrorMsg(), $sql);
        
        return false;
        
    } else {        
                
        // Affected Rows
        if(!$affected_rows = $db->affected_rows()) { $affected_rows = '0'; }
        
        // Log message
        $record = _gettext("Successfully updated the values").' `'.$current_value.'` '._gettext("to").' `'.$new_value.'` '._gettext("in the columm").' `'.$column.'` '._gettext("from the the table").' `'.$table.'` - '._gettext("Records Processed").': '.$affected_rows;
                
        // Output message via smarty
        $executed_sql_results .= '<div style="color: green">'.$record.'</div>';
        $executed_sql_results .= '<div>&nbsp;</div>';
        
        // Log message to setup log        
        write_record_to_setup_log('correction', $record);
        
        return true;
        
        
    }    
    
}

############################################
#   Execute SQL File (preg_match method)   # // this imports a phpMyAdmin .sql exported file
############################################

// https://stackoverflow.com/questions/19751354/how-to-import-sql-file-in-mysql-database-using-php

function execute_sql_file($sql_file) {
    
    $db = QFactory::getDbo();    
    global $executed_sql_results;
    global $setup_error_flag;    
    
    // Load the SQL file into memory as string
    $sql_file = file_get_contents($sql_file);
    
    // Replace database prefix placeholder with required prefix
    $sql_file = str_replace('#__', PRFX, $sql_file);    
    
    // Remove comment lines starting with /*
    $sql_file = preg_replace('/^\/\*.*\n/m', '', $sql_file);
        
    // Remove comment lines starting with --
    $sql_file = preg_replace('/^--.*\n/m', '', $sql_file);
    
    // Psrse the SQL commands
    preg_match_all('/^[A-Z].*;\n/msU', $sql_file, $sql_statements);
    
    // Loop through preg_match() result
    foreach ($sql_statements['0'] as $sql) {       
        
        // Get rule name for output
        preg_match('/(^SET.*$|^.*`.*`)/U', $sql, $query_name);
        
       // Perform the query
        if(!$rs = $db->Execute($sql)) {
            
            // Set the setup global error flag
            $setup_error_flag = true;
            
            // Set the local error flag
            $error_flag = true;
            
            // Log message
            $record = _gettext("Error performing SQL query").' : '. $query_name['0'];
            
            // Output message via smarty
            $executed_sql_results .= '<div style="color: red">'.$record.'</div>';
            
            // Log message to setup log            
            write_record_to_setup_log('install', $record, $db->ErrorMsg(), $sql);
            
            
        } else {
            
            // Log message
            $record = _gettext("Performed SQL query successfully").' : '. $query_name['0'];
            
            // Output message via smarty
            $executed_sql_results .= '<div style="color: green">'.$record.'</div>';
            
            // Log message to setup log            
            write_record_to_setup_log('install', $record);

        }

    }

    // Closing result statement
    if($error_flag) {
        
        // Log message
        $record = _gettext("One or more SQL rule has failed. Check the logs.");
        
        // Output message via smarty
        $executed_sql_results .= '<div style="color: red;"><strong>'.$record.'</strong></div>';
        
        // Log message to setup log        
        write_record_to_setup_log('install', $record);
        
        return false;
        
    } else {
        
        // Log message
        $record = _gettext("All SQL rules have run successfully.");
        
        // Output message via smarty
        $executed_sql_results .= '<div style="color: green;"><strong>'.$record.'</strong></div>';
        
        // Log message to setup log        
        write_record_to_setup_log('install', $record);
        
        return true;
        
    }           
        
}

############################################
#   Execute SQL File (line by line)        #  //  file() loads line by line, good for large imports - not currently used
############################################

// https://stackoverflow.com/questions/19751354/how-to-import-sql-file-in-mysql-database-using-php

function execute_sql_file_lines($sql_file) {
    
    $db = QFactory::getDbo();
    global $executed_sql_results;
    global $setup_error_flag;    
    
    // Temporary variable, used to store current query
    $sql = '';
    
    // Read in entire file (will be line by because of below)
    $lines = file($sql_file);
    
    // Loop through each line  - file() loads each line in one by one
    foreach ($lines as $line)
    {        
        // Skip it if the line is empty
        if ($line == '') {
            continue;
        }
        
        // Skip it if it's a comment ( -- or /* )
        if(substr($line, 0, 2) == '--' || substr($line,0,2) == '/*') {
            continue;            
        }
        
        // Replace database prefix placeholder with required prefix
        $line = str_replace('#__', PRFX, $line);

        // Add this line to the current segment
        $sql .= $line;
        
        // If it has a semicolon at the end, it's the end of the query
        if (substr(trim($line), -1, 1) == ';')
        {            
            // Get rule name for output
            preg_match('/(^SET.*$|^.*`.*`)/U', $sql, $query_name);

            // Perform the query
            if(!$rs = $db->Execute($sql)) {

                // Set the setup global error flag
                $setup_error_flag = true;
            
                // Set the local error flag
                $error_flag = true;

                // Log message
                $record = _gettext("Error performing SQL query").' : '. $query_name['0'];
                
                // Output message via smarty
                $executed_sql_results .= '<div style="color: red">'.$record.'</div>'; 

                // Log message to setup log                
                write_record_to_setup_log('upgrade', $record, $db->ErrorMsg(), $sql);                              

            } else {

                // Log message
                $record = _gettext("Performed SQL query successfully").' : '. $query_name['0'];

                // Output message via smarty
                $executed_sql_results .= '<div style="color: green">'.$record.'</div>';
                
                // Log message to setup log                
                write_record_to_setup_log('upgrade', $record);

            }            
                        
            // Reset templine variable to empty ready for the next line
            $sql = '';
            
        }        
        
    } 
    
    // Closing result statement
    if($error_flag) {

        // Log message
        $record = _gettext("One or more SQL rule has failed. Check the logs.");
                
        // Output message via smarty
        $executed_sql_results .= '<div style="color: red;">'.$record.'</div>';
        
        // Log message to setup log        
        write_record_to_setup_log('upgrade', $record);
        
        return false;

    } else {

        // Log message
        $record = _gettext("All SQL rules have run successfully.");
                
        // Output message via smarty
        $executed_sql_results .= '<div style="color: green;">'.$record.'</div>';
        
        // Log message to setup log        
        write_record_to_setup_log('upgrade', $record);
        
        return true;

    }
        
}

############################################
#  Write a record to the Setup Log         #  // Cannot be turned off - install/migrate/upgrade
############################################

function write_record_to_setup_log($setup_type, $record, $database_error = null, $sql_query = null) {
    
    // Install and migrate does not have username or login_user_id available
    if(defined('QWCRM_SETUP') && QWCRM_SETUP == 'install') {
        $username = '-';
        $login_user_id = '-';
    } else {
        $username = QFactory::getUser()->login_username;
        $login_user_id = QFactory::getUser()->login_user_id;
    }
    
    // prepare database error for the log
    $database_error = prepare_error_data('database_error', $database_error);   
    
    // prepare SQL statement for the log
    $sql_query = prepare_error_data('sql_query_for_log', $sql_query);    
    
    // Build log entry - perhaps use the apache time stamp below
    $log_entry = $_SERVER['REMOTE_ADDR'].','.$username.','.date("[d/M/Y:H:i:s O]", time()).','.$login_user_id.','.QWCRM_VERSION.','.$setup_type.',"'.$record.'","'.$database_error.'","'.$sql_query.'"'."\r\n";
    
    // Write log entry  
    if(!$fp = fopen(SETUP_LOG, 'a')) {        
        force_error_page('file', __FILE__, __FUNCTION__, '', '', _gettext("Could not open the Setup Log to save the record."));
    }
    
    fwrite($fp, $log_entry);
    fclose($fp);    
    
    return;
    
}

############################################
#  Check the database connection works     #  // new version of ADOdb might not need error_reporting(0)
############################################

function check_database_connection_details($db_host, $db_user, $db_pass, $db_name) {
    
    $db = QFactory::getDbo();
    $smarty = QFactory::getSmarty();
    
    // Get current PHP error reporting level
    $reporting_level = error_reporting();
    
    // Disable PHP error reporting (works globally)
    error_reporting(0);
    
    // Create ADOdb database connection - and collect exception if it occurs
    try
    {        
        $db->Connect($db_host, $db_user, $db_pass, $db_name);
    }    
    
    catch (Exception $e)
    {
        
        // Re-Enable PHP error reporting
        error_reporting($reporting_level);
        
        //echo $e->msg;
        //var_dump($e);
        //adodb_backtrace($e->gettrace());
        $smarty->assign('warning_msg', $e->msg);
        
        return false;
              
    }
    
    // Re-Enable PHP error reporting
    error_reporting($reporting_level);
    
    // Return the connection status
    if(!$db->isConnected()) {           
        
        $smarty->assign('warning_msg', prepare_error_data('database_connection_error', $db->ErrorMsg()));
        return false;     
        
    } else {  
        
        return true;  
        
    }
    
}

############################################
#         Submit config settings           #
############################################

function submit_qwcrm_config_settings($VAR) {
    
    // clear uneeded variables
    unset($VAR['page']);
    unset($VAR['submit']);
    unset($VAR['stage']);
    unset($VAR['theme']);
    
    update_qwcrm_config($VAR);
    
}
     
############################################
#  Generate Random Database prefix         #
############################################

function generate_database_prefix($not_this_prefix = null) {
    
    $acceptedChars = 'abcdefghijklmnopqrstuvwxyz';  // Lowercase to allow for Windows and Apache setups
    $max_offset = strlen($acceptedChars)-1;
    $prefix = '';
    
    for($i=0; $i < 4; $i++) {
        $prefix .= $acceptedChars{mt_rand(0, $max_offset)};
    }
    
    $prefix .= '_';    
    
    // This is to prevent using the MyITCRM prefix
    if($not_this_prefix) {
        if($prefix == $not_this_prefix) {
            $prefix = generate_database_prefix($not_this_prefix);
        }
    }
    
    return $prefix;
    
}

/** Install **/

############################################
#   Set workorder start number             #
############################################

function set_workorder_start_number($start_number) {
    
    $db = QFactory::getDbo();
    
    $sql = "ALTER TABLE ".PRFX."workorder_records auto_increment =".$db->qstr($start_number);

    $db->execute($sql);    
    
    return;
    
}

############################################
#   Set invoice start number               #
############################################

function set_invoice_start_number($start_number) {
    
    $db = QFactory::getDbo();
    
    $sql = "ALTER TABLE ".PRFX."invoice_records auto_increment =".$db->qstr($start_number);

    $db->execute($sql);   
    
    return;
    
}

############################################
#   Install database                       # // this imports a phpMyAdmin .sql exported file (preg_match method)
############################################

function install_database() {
    
    $smarty = QFactory::getSmarty();
    global $executed_sql_results;
    global $setup_error_flag;  
    
    // Run the install.sql
    execute_sql_file(SETUP_DIR.'install/install.sql');
    
    /* Final stuff */
    
    // Final statement
    if($setup_error_flag) {
        
        // Log message
        $record = _gettext("The database installation process failed, check the logs.");
        
        // Output message via smarty
        $executed_sql_results .= '<div>&nbsp;</div>';
        $executed_sql_results .= '<div style="color: red;"><strong>'.$record.'</strong></div>';
        
        // Log message to setup log        
        write_record_to_setup_log('install', $record);
        
    } else {
        
        // Log message
        $record = _gettext("The database installation process was successful.");
        
        // Output message via smarty
        $executed_sql_results .= '<div>&nbsp;</div>';
        $executed_sql_results .= '<div style="color: green;"><strong>'.$record.'</strong></div>';
        
        // Log message to setup log        
        write_record_to_setup_log('install', $record);
        
    }    

    // Return reflecting the installation status
    if($setup_error_flag) {
        
        /* installation failed */
        
        // Set setup_error_flag used in smarty templates
        $smarty->assign('setup_error_flag', true);
        
        
        return false;
        
    } else {
        
        /* installation successful */
        
        return true;
        
    }
   
}

/** Migrate **/

################################################
#   migrate data from myitcrm (insert method)  #    // build 1 SQL statement and then execute - this can also be used to migrate from other systems
################################################

function migrate_table($qwcrm_table, $myitcrm_table, $column_mappings) {
    
    $db = QFactory::getDbo();
    global $executed_sql_results;
    global $setup_error_flag;
    
    // Add division to seperate table migration function results
    $executed_sql_results .= '<div>&nbsp;</div>';
    
    // Log message
    $record = _gettext("Beginning the migration of MyITCRM data into the QWcrm table").': `'.$qwcrm_table.'`';       
                
    // Result message
    $executed_sql_results .= '<div><strong><span style="color: green">'.$record.'</span></strong></div>';
    
    // Log message to setup log                
    write_record_to_setup_log('migrate', $record);        
    
   /* load the records from MyITCRM */
    
    $sql = "SELECT * FROM $myitcrm_table";
    
    if(!$rs = $db->execute($sql)) {
        
        // set error flag
        $error_flag = true; 
        
        // Log message
        $record = _gettext("Error reading the MyITCRM table").' `'.$myitcrm_table.'` - SQL: '.$sql.' - SQL Error: '.$db->ErrorMsg();        
                
        // Result message
        $executed_sql_results .= '<div><span style="color: red">'.$record.'</span></div>';
        
        // Log message to setup log                
        write_record_to_setup_log('migrate', $record);        
                
        // output error, could not load table so all of this table was skipped
        return false;
    
    } else {
        
        /* Load each single records and insert into QWcrm */ 
        
        // Record counters
        $records_processed  = 0;
        $records_failed     = 0;
        $records_successful = 0;
        
        // Loop through the MyITCRM records (single record, single insert)
        while(!$rs->EOF) {               
                    
            $myitcrm_record = $rs->GetRowAssoc();
                    
            /* Build the 'INSERT' part of the SQL statement */
            
            $insert_sql = "INSERT INTO `$qwcrm_table` (";
            foreach($column_mappings as $qwcrm_column => $myitcrm_column) {
                $insert_sql .= "`$qwcrm_column`, ";            
            }
            $insert_sql = rtrim($insert_sql, ', ');           // remove the last ', '        
            $insert_sql .= ") VALUES" . "\n";
            
            /* Build 'VALUES' part of the SQL statement by mapping the MyITCRM record data to the QWcrm values */
            
            $values_sql = '(';
            foreach($column_mappings as $qwcrm_column => $myitcrm_column) {
                
                // Skip looking for data in MyITCRM record if there is no corresponding field
                if($myitcrm_column == '') {
                    $values_sql .= "'', ";
                    continue;                    
                }
                
                foreach($myitcrm_record as $myitcrm_record_column => $myitcrm_record_val) {
                    
                    if($myitcrm_column == $myitcrm_record_column) {
                        
                        // if the value is null set it to '' - This is a fix specific to MyITCRM database becvause it is dirty
                        if($myitcrm_record_val === null) { $myitcrm_record_val = ''; }
                        
                        //$values_sql .= "'$myitcrm_record_val', ";
                        $values_sql .= $db->qstr($myitcrm_record_val).', ';
                        break;
                        
                    }    
                
                }                         
            
            }
            
            // Close the 'VALUES' SQL statement
            $values_sql = rtrim($values_sql, ', ');
            $values_sql .= ");";                
                
            /* Build and execute statement */
        
            // combine the 'INSERT' and 'VALUES' sections
            $sql = $insert_sql.$values_sql;

            // insert the migrated record into qwcrm
            if(!$db->execute($sql)) {  
                
                /* Fail */
                
                // set error flag
                $error_flag = true;
                
                // Advance the records_failed counter
                ++$records_failed;
                
                // Log message
                $record = _gettext("Error migrating a MyITCRM record into QWcrm");
                
                // Result message
                $executed_sql_results .= '<div><span style="color: red">'.$record.' - SQL Error: '.$db->ErrorMsg().'</span></div>';                
                
                // Log message to setup log                
                write_record_to_setup_log('migrate', $record, $db->ErrorMsg(), $sql);                
                
                
                
            } else {
                 
                // Advance the records_successful counter
                ++$records_successful;
                
                // NO logging, otherwise log file would be huge
                
                /* success  
             
                // Log message
                $record = _gettext("Successfully migrated a MyITCRM record into QWcrm");
                
                // Result message
                $executed_sql_results .= '<div><span style="color: green">'.$record.'</span></div>';
                                
                // Log message to setup log                
                write_record_to_setup_log('migrate', $record);
               
                */                
                
            }
            
            // Advance the records_processed counter
            ++$records_processed;
            
            // Advance the INSERT loop to the next record            
            $rs->MoveNext();
        
        }// EOF While Loop
        
        // Output Record counters        
        $executed_sql_results .= '<div><span style="color: blue">'._gettext("MyITCRM Records Processed").': '.$records_processed.'</span></div>';
        $executed_sql_results .= '<div><span style="color: red">'._gettext("Records Failed To Migrate").': '.$records_failed.'</span></div>';
        $executed_sql_results .= '<div><span style="color: green">'._gettext("Records Successfuly Migrated").': '.$records_successful.'</span></div>';        
        
        // if there has been an error
        if($error_flag) {
            
            // Set the setup global error flag
            $setup_error_flag = true;
            
            // Log message
            $record = _gettext("Error migrating some records into QWcrm table").': `'.$qwcrm_table.'`';
            $record_additional = ' - '._gettext("MyITCRM Records Processed").': '.$records_processed.' - '._gettext("Records Failed To Migrate").': '.$records_failed.' - '._gettext("Records Successfuly Migrated").': '.$records_successful;
            
            // Result message
            $executed_sql_results .= '<div><strong><span style="color: red">'.$record.'</span></strong></div>';
            
            // Add division to seperate table migration function results
            $executed_sql_results .= '<div>&nbsp;</div>';

            // Log message to setup log                
            write_record_to_setup_log('migrate', $record.$record_additional);
                
            return false;
        
        // if all ran successfully
        } else {
            
            // Log message
            $record = _gettext("Successfully migrated all records into QWcrm table").': `'.$qwcrm_table.'`';
            $record_additional = ' - '._gettext("MyITCRM Records Processed").': '.$records_processed.' - '._gettext("Records Failed To Migrate").': '.$records_failed.' - '._gettext("Records Successfuly Migrated").': '.$records_successful;
            
            // Result message
            $executed_sql_results .= '<div><strong><span style="color: green">'.$record.'</span></strong></div>';
            
            // Add division to seperate table migration function results
            $executed_sql_results .= '<div>&nbsp;</div>';

            // Log message to setup log                
            write_record_to_setup_log('migrate', $record.$record_additional);
            
            return true;
            
        }             
    
    }

}

/** Upgrade **/

############################################
#   Upgrade database                       #
############################################

function upgrade_database() {

    $db = QFactory::getDbo();
    
    // not done yet
       
}