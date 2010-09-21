<?php
	/**
	 * Todo Edit Action
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	// Only admins can delete
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$guid 				= get_input('todo_guid');
	$title 				= get_input('title');
	$description 		= get_input('description');
	$tags 				= string_to_tag_array(get_input('tags'));
	$due_date			= strtotime(get_input('due_date'));
	$assignees			= get_input('assignee_guids');
	$return_required	= get_input('return_required');
	$rubric_select		= get_input('rubric_select');
	$rubric_guid		= get_input('rubric_guid');
	$access_level		= get_input('access_level');
	$container_guid 	= get_input('container_guid');
	$status				= get_input('status');
			
	$todo = get_entity($guid);
	
	if (!can_write_to_container(get_loggedin_userid(), $container_guid)) {
		register_error(elgg_echo("todo:error:permission"));		
		forward($_SERVER['HTTP_REFERER']);
	}
	
	$can_edit = $todo->canEdit(); 
	
	if ($todo && $todo->getSubtype() == "todo" && $can_edit) {
		
		// Get previous status for notifications
		$previous_status = $todo->status;
		
		// Cache to session
		$_SESSION['user']->is_todo_cached = true;
		$_SESSION['user']->todo_title = $title;
		$_SESSION['user']->todo_description = $description;
		$_SESSION['user']->todo_tags = $tags;
		$_SESSION['user']->todo_due_date = $due_date;
		$_SESSION['user']->todo_assignees = $assignees;
		$_SESSION['user']->todo_return_required = $return_required;
		$_SESSION['user']->todo_rubric_select = $rubric_select;
		$_SESSION['user']->todo_rubric_guid = $rubric_guid;
		$_SESSION['user']->todo_access_level = $access_level;

		// Check values
		if (empty($title) || empty($due_date)) {
			register_error(elgg_echo('todo:error:requiredfields'));
			forward($_SERVER['HTTP_REFERER']);
		}
		
		$todo->title 		= $title;
		$todo->description 	= $description;
		$todo->tags 		= $tags;
		$todo->due_date		= $due_date;
		$todo->return_required = $return_required;
		$todo->status = $status;
		
		$todo->time_published = (($previous_status == TODO_STATUS_DRAFT && $status == TODO_STATUS_PUBLISHED) ? time() : null);
		
		if ($access_level == TODO_ACCESS_LEVEL_ASSIGNEES_ONLY) {
			$todo->access_id = $todo->assignee_acl;
		} else {
			$todo->access_id = $access_level;
		}
		

		if ($rubric_select) 
			$todo->rubric_guid = $rubric_guid;
		else 
			$todo->rubric_guid = null;
				
		// Save and assign users
		if (!$todo->save() || !assign_users_to_todo($assignees, $todo->getGUID())) {
			register_error(elgg_echo("todo:error:create"));		
			forward($_SERVER['HTTP_REFERER']);
		}
			
		// If the todo was previously a draft and has been changed to published, notify all users and add to river
		if ($previous_status == TODO_STATUS_DRAFT && $status == TODO_STATUS_PUBLISHED) {
			add_to_river('river/object/todo/create', 'create', get_loggedin_userid(), $todo->getGUID());	
			notify_todo_users_assigned($todo);
		} else if ($previous_status == TODO_STATUS_PUBLISHED && $status == TODO_STATUS_DRAFT) {
			// Remove from river if being set back to draft from published
			remove_from_river_by_object($todo->getGUID());
		} 
		
		// If we have new assignees, notify them if status is published
		if ($assignees && $status = TODO_STATUS_PUBLISHED) {
			$owner = get_entity($todo->container_guid);
			foreach ($assignees as $assignee) {
				notify_user($assignee,
							$todo->container_guid,
							elgg_echo('todo:email:subjectassign'), 
							sprintf(elgg_echo('todo:email:bodyassign'), 
							$owner->name, 
							$todo->title, 
							$todo->getURL())
				);
			}
		}
		
		// Clear cached info
		clear_todo_cached_data();

		// Save successful, forward to index
		system_message(elgg_echo('todo:success:edit'));
		forward($todo->getURL());	
	}
	
	register_error(elgg_echo("todo:error:edit"));		
	forward($_SERVER['HTTP_REFERER']);

?>