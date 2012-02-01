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
$suggested_tags 	= string_to_tag_array(get_input('suggested_tags'));
$due_date			= strtotime(get_input('due_date'));
$assignees			= get_input('members');
$container_guid 	= get_input('container_guid');	
$status 			= get_input('status');
$guid 				= get_input('guid');

// Sticky form
elgg_make_sticky_form('todo_edit');

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

/*
var_dump($title);
var_dump($description);
var_dump($tags);
var_dump($due_date);
var_dump($assignees);
var_dump($container_guid);
var_dump($status);
var_dump($rubric_select);
var_dump($rubric_guid);
var_dump($access_level);
var_dump($guid);
die;
*/

// Check values
if ($status == TODO_STATUS_PUBLISHED && (empty($title) || empty($due_date))) {
	register_error(elgg_echo('todo:error:requiredfields'));
	forward(REFERER);
}

if ($guid) {
	$entity = get_entity($guid);
	if (elgg_instanceof($entity, 'object', 'todo') && $entity->canEdit()) {
		$todo = $entity;
		
		// Get previous status for notifications
		$previous_status = $todo->status;
		$todo->time_published = (($previous_status == TODO_STATUS_DRAFT && $status == TODO_STATUS_PUBLISHED) ? time() : null);
		
		// If editing, and assignees only is selected we need to set the access id 
		// to the existing access collection id
		if ($access_level == TODO_ACCESS_LEVEL_ASSIGNEES_ONLY) {
			$todo->access_id = $todo->assignee_acl;
		} else {
			$todo->access_id = $access_level;
		}
		
	} else {
		register_error(elgg_echo('todo:error:edit'));
		forward(get_input('forward', REFERER));
	}
	
} else {
	$todo = new ElggObject();
	$todo->subtype 		= "todo";
	$todo->container_guid = $container_guid;
	$todo->time_published = ($status == TODO_STATUS_PUBLISHED ? time() : null);
	$todo->access_id 	= $access_level; 
}

$todo->title 		= $title;
$todo->description 	= $description;
$todo->tags 		= $tags;
$todo->suggested_tags = $suggested_tags;
$todo->due_date		= $due_date;
$todo->return_required = $return_required;
$todo->status = $status;
$todo->rubric_guid = $rubric_guid;


// Before saving, check permissions
if (!can_write_to_container($todo->owner_guid, $todo->container_guid)) {
	register_error(elgg_echo("todo:error:permission"));		
	forward(REFERER);
}
	
// Save and assign users
if (!$todo->save() || !assign_users_to_todo($assignees, $todo->getGUID())) {
	elgg_set_context($context);
	register_error(elgg_echo("todo:error:create"));		
	forward(REFERER);
}

if ($guid) { // Existing
	// Remove from river if setting back to a draft
	if ($previous_status == TODO_STATUS_DRAFT && $status == TODO_STATUS_PUBLISHED) {
		add_to_river('river/object/todo/create', 'create', elgg_get_logged_in_user_guid(), $todo->getGUID());	
		notify_todo_users_assigned($todo);
	} else if ($previous_status == TODO_STATUS_PUBLISHED && $status == TODO_STATUS_DRAFT) {
		// Remove from river if being set back to draft from published;
		elgg_delete_river(array('object_guid' => $todo->getGUID()));
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
} else { // New
	// Don't notify or add todo to the river unless its published
	if ($status == TODO_STATUS_PUBLISHED) {
		add_to_river('river/object/todo/create', 'create', elgg_get_logged_in_user_guid(), $todo->getGUID());	
		notify_todo_users_assigned($todo);
	}
}

elgg_clear_sticky_form('todo_edit');

// Save successful, forward
system_message(elgg_echo('todo:success:save'));
if ($forward_new) {
	forward(elgg_get_site_url() . 'todo/add/' . $container_guid);
} else {
	forward($todo->getURL());
}
