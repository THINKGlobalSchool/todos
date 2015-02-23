<?php
/**
 * Submission Create Action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// get input
$description = get_input('submission_description');
$todo_guid = get_input('todo_guid');
$content = get_input('submission_content', FALSE);
	
$todo = get_entity($todo_guid);
$user = elgg_get_logged_in_user_entity();

// Make sure we don't create more than one submission per todo
if (has_user_submitted($user->guid, $todo_guid)) {
	register_error(elgg_echo('todo:error:duplicatesubmission'));
	forward(REFERER);
}

// Make sure we can't submit to a manually completed (closed) todo
if ($todo->manual_complete) {
	register_error(elgg_echo('todo:error:closedsubmission'));
	forward(REFERER);
}

$submission = new ElggObject();
$submission->title = sprintf(elgg_echo('todo:label:submissiontitleprefix'), $todo->title);
$submission->subtype = "todosubmission";
$submission->description = $description;
$submission->owner_id = $user->getGUID();
$submission->todo_guid = $todo_guid;

// Set content
if ($content) {
	$submission->content = serialize($content);
} else {
	$submission->content = 0;
}

// NOTE: Access ID and ACL's handled by an event listener

// Save
if (!$submission->save()) {
	register_error(elgg_echo('todo:error:savesubmission'));
}

// This states that: 'Submission' is 'submitted' to 'Todo' 
$success = add_entity_relationship($submission->getGUID(), SUBMISSION_RELATIONSHIP, $todo_guid);

// Add a relationship stating that the user has completed the todo
add_entity_relationship($submission->owner_guid, COMPLETED_RELATIONSHIP, $submission->todo_guid);

// Accept the todo when completing (if not already accepted)
user_accept_todo($user->getGUID(), $todo_guid);

// River
elgg_create_river_item(array(
	'view' => 'river/object/todosubmission/create',
	'action_type' => 'create',
	'subject_guid' => $user->guid,
	'object_guid' => $submission->guid
));

// Notify todo owner
global $CONFIG;

if (!elgg_get_plugin_user_setting('suppress_complete', $todo->owner_guid, 'todos')) {
	notify_user(
		$todo->owner_guid, 
		$CONFIG->site->guid,
		elgg_echo('todo:email:subjectsubmission', array($user->name, $todo->title)), 
		elgg_echo('todo:email:bodysubmission', array($user->name, $todo->title, $todo->getURL(), $submission->getURL()))
	);
}

// Save successful, forward to index
if ($success) {
	system_message(elgg_echo('todo:success:savesubmission'));
} else {
	register_error(elgg_echo('todo:error:savesubmission'));
}
