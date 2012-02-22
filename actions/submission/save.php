<?php
/**
 * Todo Create Action
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
$content = get_input('submission_content');
	
$todo = get_entity($todo_guid);
$user = elgg_get_logged_in_user_entity();

$submission = new ElggObject();
$submission->title = sprintf(elgg_echo('todo:label:submissiontitleprefix'), $todo->title);
$submission->subtype = "todosubmission";
$submission->description = $description;
$submission->content = serialize($content);
$submission->owner_id = $user->getGUID();
$submission->todo_guid = $todo_guid;

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
add_to_river('river/object/todosubmission/create', 'create', elgg_get_logged_in_user_guid(), $submission->getGUID());	

// Notify todo owner
global $CONFIG;
notify_user(
	$todo->owner_guid, 
	$CONFIG->site->guid,
	elgg_echo('todo:email:subjectsubmission', array($user->name, $todo->title)), 
	elgg_echo('todo:email:bodysubmission', array($user->name, $todo->title, $todo->getURL(), $submission->getURL()))
);


// Save successful, forward to index
if ($success) {
	system_message(elgg_echo('todo:success:savesubmission'));
} else {
	register_error(elgg_echo('todo:error:savesubmission'));
}
