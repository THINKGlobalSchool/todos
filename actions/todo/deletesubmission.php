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

// get input
$guid = get_input('submission_guid');

$submission = get_entity($guid);
$todo_guid = $submission->todo_guid;

$candelete = $submission->canEdit();

if (elgg_instanceof($submission, 'object', 'todosubmission') && $candelete) {
	
	
	// Remove the submission complete relationship stating that the user has completed the todo
	remove_entity_relationship($submission->owner_guid, COMPLETED_RELATIONSHIP, $submission->todo_guid);
	
	// Delete it!
	$rowsaffected = $submission->delete();
	
	// This will check and set the complete flag on the todo
	update_todo_complete($todo_guid);
	
	if ($rowsaffected > 0) {
		// Success message
		system_message(elgg_echo("todo:success:submissiondelete"));
		
	} else {
		register_error(elgg_echo("todo:error:submissiondelete"));
	}
	
	// Forward
	forward("pg/todo/viewtodo/$todo_guid");
}
