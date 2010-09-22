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
								   'dtend' => date($date_format_str, $todo->due_date),					// End date
								   'dtstamp' => date($time_format_str,$todo->time_created),				// Created
								   'uid' => md5($todo->time_created . $username), 						// Unique id, time created hashed with username
								   'created' => date($time_format_str, $todo->time_created), 			// Created
								   'last-modified' =>  date($time_format_str, $todo->time_updated),		// Last modified
								   'summary' => "To Do: " . $todo->title, 											// Short summary
								  // 'description' => str_replace("\r\n", "=0D=0A=", strip_tags($todo->description)));	// Full description, CFLF
								   'description' => '');
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
X-WR-CALNAME:THINK Spot To Do's
X-WR-TIMEZONE:UTC
X-WR-CALDESC:Displays To Do's from Spot on your Google Calendar
<?php
		foreach ($ical_events as $event) {
			?>
BEGIN:VEVENT
DTSTART;VALUE=DATE:<?php echo $event['dtstart'] . "\r\n"; ?>
DTEND;VALUE=DATE:<?php echo $event['dtend']. "\r\n"; ?>
DTSTAMP:<?php echo $event['dtstamp'] . "Z" . "\r\n"; ?>
UID:<?php echo $event['uid']. "\r\n"; ?>
CLASS:PUBLIC
CREATED:<?php echo $event['created']. "\r\n"; ?>
LAST-MODIFIED:<?php echo $event['last-modified']. "\r\n"; ?>
SEQUENCE:1
STATUS:CONFIRMED
SUMMARY:<?php echo $event['summary']. "\r\n"; ?>
DESCRIPTION;ENCODING=QUOTED-PRINTABLE: <?php echo $event['description'] . "\r\n"; ?>
TRANSP:OPAQUE
END:VEVENT<?php echo "\r\n";
		}
?>
END:VCALENDAR
<?php
	}
?>
