<?php
/**
 * Todo manually assign a todo to a user
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

$assignee = get_loggedin_user();

$todo_guid = get_input('todo_guid');
$todo = get_entity($todo_guid);

// If we've got a todo, sign the user up and accept the todo
if (elgg_instanceof($todo, 'object', 'todo')) {

	$success = true;
	$success &= assign_user_to_todo($assignee->getGUID(), $todo_guid);
	$success &= user_accept_todo($assignee->getGUID(), $todo_guid);
		
	if ($success) {
		// Success message
		system_message(elgg_echo("todo:success:signup"));
		forward($_SERVER['HTTP_REFERER']);
	}
}
register_error(elgg_echo("todo:error:signup"));		
forward($_SERVER['HTTP_REFERER']);
