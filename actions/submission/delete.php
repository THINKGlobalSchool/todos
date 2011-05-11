<?php
/**
 * Todo Delete Submission Action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$guid = get_input('guid');
$submission = get_entity($guid);

if (elgg_instanceof($submission, 'object', 'todosubmission') && $submission->canEdit()) {
	// Get todo guid
	$todo_guid = $submission->todo_guid;
	
	// Remove the submission complete relationship stating that the user has completed the todo
	remove_entity_relationship($submission->owner_guid, COMPLETED_RELATIONSHIP, $todo_guid);
	
	if ($submission->delete()) {
		// This will check and set the complete flag on the todo
		update_todo_complete($todo_guid);
		
		system_message(elgg_echo('todo:success:submissiondelete'));
		forward("todo/view/$todo_guid");
	} else {
		register_error(elgg_echo('todo:error:submissiondelete'));
	}
} else {
	register_error(elgg_echo('todo:error:invalid'));
}

forward(REFERER);
