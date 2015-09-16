<?php
// Test script for weekly accepted corn
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

// No time limit.. could be a while
set_time_limit(0);

$ia = elgg_get_ignore_access();

elgg_set_ignore_access(TRUE);


// Get student role
$student_role_guid = elgg_get_plugin_setting('studentrole', 'todos');

$student_role = get_entity($student_role_guid);

echo "<pre>";
echo "<h1>Weekly accepted cron</h1>";

$date = date("F j, Y");

$email_subject = elgg_echo('todo:email:subjectunaccepteddigest', array($date));

if (elgg_instanceof($student_role, 'object', 'role')) {
	echo "Student role name: {$student_role->title}\r\n\r\n";

	// Get role members
	$role_members = $student_role->getMembers(0, 0, FALSE, TRUE);

	// Loop over members
	foreach($role_members as $member) {
		$todos = get_unaccepted_todos($member->guid);

		foreach ($todos as $todo) {
			$todo_list .= "{$todo->title}\r\n{$todo->getURL()}\r\n\r\n";
		}

		$email_body = elgg_echo('todo:email:bodyunaccepteddigest', array($member->name, $todo_list));

		echo "\r\n\r\n======================================================================\r\n\r\n";

		echo "Subject: {$email_subject}\r\n\r\n";

		echo "Body:\r\n\r\n{$email_body}";

		echo "\r\n======================================================================\r\n\r\n";

		//notify_user($member->guid, elgg_get_site_entity()->guid, $email_subject, $email_body, array(), "email");

	}
} else {
	echo "Invalid role.";
}

echo "</pre>";

elgg_set_ignore_access($ia);