<?php	
	// include the Elgg engine
	include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php";
	
	$hash = get_input('t');
	$username = get_input('user');

	if (check_todo_user_hash($hash, get_user_by_username($username))) {
		// Ignore access for scripts
		elgg_set_ignore_access(true);
		
		$user = get_user_by_username($username);
		$todos = get_users_todos($user->getGUID());
		
		$date_format_str = "Ymd";
		$time_format_str = "Ymd\THi00";
		$ical_events = array();
		
		
		foreach($todos as $todo) {
			$ical_events[] = array('dtstart' => date($date_format_str, $todo->due_date),				// Start date
								   'dtend' => date($date_format_str, $todo->due_date),				// End date
								   'dtstamp' => date($time_format_str,$todo->time_created),			// Created
								   'uid' => md5($todo->time_created . $username), 				// Unique id, time created hashed with username
								   'created' => date($time_format_str, $todo->time_created), 		// Created
								   'last-modified' =>  date($time_format_str, $todo->time_updated),	// Last modified
								   'summary' => $todo->title, 									// Short summary
								   'descrtiption' => $todo->description);						// Full description
		}
		
		$filename = "SpotTodoExport.ics";
		
		header("Content-Type: text/calendar");
		header("Content-Disposition: attachment; filename=$filename");		
?>
BEGIN:VCALENDAR
PRODID:-//THINK Global School//Spot Todo Export//EN
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:PUBLISH
X-WR-TIMEZONE:UTC
X-WR-CALDESC:SpotTodoExport
<?php
		foreach ($ical_events as $event) {
			?>
BEGIN:VEVENT
DTSTART;VALUE=DATE:<?php echo $event['dtstart'] . "\n"; ?>
DTEND;VALUE=DATE:<?php echo $event['dtend']. "\n"; ?>
DTSTAMP:<?php echo $event['dtstamp'] . "Z" . "\n"; ?>
UID:<?php echo $event['uid']. "\n"; ?>
CLASS:PUBLIC
CREATED:<?php echo $event['created']. "\n"; ?>
LAST-MODIFIED:<?php echo $event['last-modified']. "\n"; ?>
SEQUENCE:1
STATUS:CONFIRMED
SUMMARY:<?php echo $event['summary']. "\n"; ?>
TRANSP:OPAQUE
END:VEVENT<?php echo "\n";
		}
?>
END:VCALENDAR
<?php
	}
?>

