<?php
/**
 * Todo Create Action
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 * 
 */
		
// Get inputs
$title 				= get_input('title');
$description 		= get_input('description');
$tags 				= string_to_tag_array(get_input('tags'));
$suggested_tags 	= string_to_tag_array(get_input('suggested_tags'));
$due_date			= strtotime(get_input('due_date'));
$start_date			= strtotime(get_input('start_date'));
$assignees			= get_input('members');
$container_guid 	= get_input('container_guid');	
$status 			= get_input('status');
$guid 				= get_input('guid');
$rubric_select		= get_input('rubric_select');
$rubric_guid		= get_input('rubric_guid');
$access_level		= get_input('access_level');
$category           = get_input('category');
$auto_publish       = get_input('auto_publish');
$publish_date       = strtotime(get_input('publish_date'));

// Sticky form
elgg_make_sticky_form('todo_edit');

// If user clicks 'save and new' 
$forward_new = get_input('submit_and_new', 0);
	
if (get_input('return_required', FALSE)) {
	$return_required = TRUE;
} else {
	$suggested_tags = NULL;
	$return_required = FALSE;
}

if (get_input('grade_required', FALSE)) {
	$grade_required = TRUE;
	$grade_total = get_input('grade_total', FALSE);
} else {
	$grade_required = FALSE;
	$grade_total = NULL;
	$rubric_select = NULL;
	$rubric_guid = NULL;
}

// Check values
if ($status == TODO_STATUS_PUBLISHED && (empty($title) || empty($due_date))) {
	register_error(elgg_echo('todo:error:requiredfields'));
	forward(REFERER);
}

// Save the todo
$result = save_todo(array(
	'title' => $title,
	'description' => $description,
	'tags' => $tags,
	'suggested_tags' => $suggested_tags,
	'due_date' => $due_date,
	'start_date' => $start_date,
	'assignees' => $assignees,
	'container_guid' => $container_guid,
	'status' => $status,
	'rubric_guid' => $rubric_guid,
	'access_id' => $access_level,
	'category' => $category,
	'publish_date' => $publish_date,
	'auto_publish' => $auto_publish
), $guid);

// Check for successful save
if (!$result['status']) {
	register_error($result['error']);
	forward(REFERER);
}

$todo = get_entity($result['guid']);

elgg_clear_sticky_form('todo_edit');

// Save successful, forward
system_message(elgg_echo('todo:success:save'));
if ($forward_new) {
	forward(elgg_get_site_url() . 'todo/add/' . $container_guid);
} else {
	forward($todo->getURL());
}
