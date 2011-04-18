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
$title 				= get_input('title');
$description 		= get_input('description');
$tags 				= string_to_tag_array(get_input('tags'));
$due_date			= strtotime(get_input('due_date'));
$assignees			= get_input('assignee_guids');
$container_guid 	= get_input('container_guid');	
$status 			= get_input('status');


// Sticky form
elgg_make_sticky_form('todo_post_forms');

// If user clicks 'save and new' 
$forward_new		= get_input('submit_and_new', 0);
	
if (get_input('return_required', false)) {
	$return_required = true;
} else {
	$return_required = false;
}

$rubric_select		= get_input('rubric_select');
$rubric_guid		= get_input('rubric_guid');
$access_level		= get_input('access_level');

	
// Check values
if ($status == TODO_STATUS_PUBLISHED && (empty($title) || empty($due_date))) {
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
$todo->return_required = $return_required;
$todo->container_guid = $container_guid;
$todo->status = $status;

$todo->time_published = ($status == TODO_STATUS_PUBLISHED ? time() : null);

if ($rubric_select) {
	$todo->rubric_guid = $rubric_guid;
}

// Before saving, check permissions
if (!can_write_to_container($todo->owner_guid, $todo->container_guid)) {
	register_error(elgg_echo("todo:error:permission"));		
	forward($_SERVER['HTTP_REFERER']);
}
	
// Save and assign users
if (!$todo->save() || !assign_users_to_todo($assignees, $todo->getGUID())) {
	elgg_set_context($context);
	register_error(elgg_echo("todo:error:create"));		
	forward($_SERVER['HTTP_REFERER']);
}

// Don't notify or add todo to the river unless its published
if ($status == TODO_STATUS_PUBLISHED) {
	add_to_river('river/object/todo/create', 'create', elgg_get_logged_in_user_guid(), $todo->getGUID());	
	notify_todo_users_assigned($todo);
}

elgg_clear_sticky_form('todo_post_forms');

// Save successful, forward
system_message(elgg_echo('todo:success:create'));
if ($forward_new) {
	forward(elgg_get_site_url() . 'todo/createtodo');
} else {
	forward($todo->getURL());
}
