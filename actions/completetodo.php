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
	
	// Start engine as this action is triggered via ajax
	require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/engine/start.php');
	
	// Logged in check
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();

	$todo_guid = get_input('todo_guid');
	$todo = get_entity($todo_guid);
		
	if ($todo && $todo->getSubtype() == "todo") {
		
		$todo->manual_complete = true;
		if ($todo->save()) {
			// Grab the todo's assignees and mark each as having accepted the todo
			$assignees = get_todo_assignees($todo_guid);
			foreach ($assignees as $assignee) {
				user_accept_todo($assignee->getGUID(), $todo_guid);
			}
			
			// Success message
			system_message(elgg_echo("todo:success:flagcomplete"));
			forward($_SERVER['HTTP_REFERER']);
		}
	}
	
	register_error(elgg_echo("todo:error:flagcomplete"));		
	forward($_SERVER['HTTP_REFERER']);

?>