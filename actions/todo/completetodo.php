<?php
/**
 * Todo manual complete todo action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
// Logged in check
gatekeeper();

$todo_guid = get_input('todo_guid');
$todo = get_entity($todo_guid);
	
if (elgg_instanceof($todo, 'object', 'todo')) {
	
	$todo->manual_complete = true;
	if ($todo->save()) {
		// Grab the todo's assignees and mark each as having accepted the todo
		$assignees = get_todo_assignees($todo_guid);
		foreach ($assignees as $assignee) {
			user_accept_todo($assignee->getGUID(), $todo_guid);
		}
		
		// This will check and set the complete flag on the todo
		update_todo_complete($todo_guid);
		
		// Success message
		system_message(elgg_echo("todo:success:flagcomplete"));
		forward($_SERVER['HTTP_REFERER']);
	}
}

register_error(elgg_echo("todo:error:flagcomplete"));		
forward($_SERVER['HTTP_REFERER']);
