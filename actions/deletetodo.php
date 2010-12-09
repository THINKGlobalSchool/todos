<?php
	/**
	 * Todo Delete Action
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
		
	// get input
	
	$guid = get_input('todo_guid');

	$todo = get_entity($guid);	

	$container_guid = $todo->container_guid;
	
	$candelete = $todo->canEdit();
	
	if ($todo->getSubtype() == "todo" && $candelete) {
		
		// Disable it
		$success = $todo->disable();
		
		if ($success) {
			// Success message
			system_message(elgg_echo("todo:success:delete"));
			
		} else {
			register_error(elgg_echo("todo:error:delete"));
		}
		
		// Forward
		forward("pg/todo/owned/" . get_entity($container_guid)->username);
		//forward($_SERVER['HTTP_REFERER']);
	}
?>