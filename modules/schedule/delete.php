<?php

require(INCLUDES_DIR.'modules/schedule.php');

if(!$schedule_id) {    
    force_page('schedule', 'day', 'warning_msg=Please go back and select a schedule to delete');
    exit;
}
  
// Delete the schedule
delete_schedule($db, $schedule_id);