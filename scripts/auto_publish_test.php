<?php
// Test auto publish to dos
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php");
admin_gatekeeper();

// No time limit.. could be a while
set_time_limit(0);

// Grab all todos with auto publish

// Todo options
$options = array(
	'type' => 'object',
	'subtype' => 'todo',
	'metadata_name_value_pairs' => array(array(
		'name' => 'status',
		'value' => TODO_STATUS_DRAFT
	), array(
		'name' => 'auto_publish',
		'value' => 'on'
	)),
	'limit' => 0
);

$todos = elgg_get_entities_from_metadata($options);

if ($ddate = get_input('ddate', FALSE)) {
	$offset_today = strtotime(date("Ymd", $ddate)) + todo_get_submission_timezone_offset();

	var_dump($offset_today);
} else {
	$offset_today = strtotime('today midnight') + todo_get_submission_timezone_offset();
}

foreach ($todos as $todo) {
	$offset_publish = $todo->publish_date + todo_get_submission_timezone_offset();

	if ($offset_today == $offset_publish) {
		save_todo(array(
			'status' => TODO_STATUS_PUBLISHED
		), $todo->guid);
	}
}

echo "</pre>";