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
		
	// must be logged in
	gatekeeper();
	
	// must have security token 
	action_gatekeeper();
	
	// get input
	$title 				= get_input('title');
	$description 		= get_input('description');
	$tags 				= string_to_tag_array(get_input('tags'));
	$due_date			= strtotime(get_input('due_date'));
	$assignees			= get_input('assignee_guids');
	$container_guid 	= get_input('container_guid');	
	
	if (get_input('return_required', false)) {
		$return_required = true;
	} else {
		$return_required = false;
	}
	
	$rubric_select		= get_input('rubric_select');
	$rubric_guid		= get_input('rubric_guid');
	$access_level		= get_input('access_level');

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
	
	$todo = new ElggObject();
	$todo->subtype 		= "todo";
	$todo->title 		= $title;
	$todo->description 	= $description;
	$todo->access_id 	= $access_level; 
	$todo->tags 		= $tags;
	$todo->due_date		= $due_date;
	//$todo->assignees	= serialize($assignees); // Store the array of guids just in case.. No point.
	$todo->return_required = $return_required;
	$todo->container_guid = $container_guid;
	
	if ($rubric_select) 
		$todo->rubric_guid = $rubric_guid;
	
	// Before saving, check permissions
	if (!can_write_to_container($todo->owner_guid, $todo->container_guid)) {
		register_error(elgg_echo("todo:error:permission"));		
		forward($_SERVER['HTTP_REFERER']);
	}
	
	// Save and assign users
	if (!$todo->save() || !assign_users_to_todo($assignees, $todo->getGUID())) {
		set_context($context);
		register_error(elgg_echo("todo:error:create"));		
		forward($_SERVER['HTTP_REFERER']);
	}
	
	add_to_river('river/object/todo/create', 'create', get_loggedin_userid(), $todo->getGUID());	
	
	// Clear Cached info
	clear_todo_cached_data();

	// Save successful, forward to index
	system_message(elgg_echo('todo:success:create'));
	forward($todo->getURL());
?>