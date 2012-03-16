<?php
/**
 * User submissions view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

$user_guid = get_input('user_guid', elgg_extract('user_guid', $vars, NULL));
$group_guid = get_input('group_guid', elgg_extract('user_guid', $vars, NULL));

// Make sure we can view the list
if (!submissions_gatekeeper($user_guid, $group_guid)) {
	echo elgg_echo('todo:error:access');
	return;
}


$user = get_entity($user_guid);

if (!elgg_instanceof($user, 'user')) {
	echo elgg_echo('todo:error:invaliduser');
	return;
}

$view_vars = array('user_guid' => $user_guid);

// Check for a group
if (elgg_instanceof(get_entity($group_guid), 'group')) {
	$view_vars['group_guid'] = $group_guid;
	$view_vars['limit'] = 15;
}

$view_vars['filter_return'] = 1; // Default to return

// Create submissions module				
$module = elgg_view('modules/genericmodule', array(
	'view' => 'todo/submissions',
	'module_id' => 'todo-user-submissions-module',
	'view_vars' => $view_vars, 
));

// Register filter menu items
elgg_register_menu_item('todo-submission-sort-menu', array(
	'name' => 'todo_user_submissions_return_filter',
	'text' => elgg_echo('todo:label:show') . ": " . elgg_view('input/dropdown', array(
		'name' => 'todo_user_submission_return_dropdown',
		'options_values' => array(
			'all' => elgg_echo('all'),
			1 => elgg_echo('todo:label:return'),
			0 => 'No&nbsp;' . elgg_echo('todo:label:return'),
		),
		'value' => 1, // Return selected by default
		'class' => 'todo-user-submission-return-dropdown',
	)),
	'class' => '',
	'href' => FALSE,
	'selected' => FALSE,
	'priority' => 1,
));

elgg_register_menu_item('todo-submission-sort-menu', array(
	'name' => 'todo_user_submissions_sort',
	'text' => elgg_echo('todo:label:sortasc'),
	'class' => 'todo-user-submissions-sort',
	'selected' => FALSE,
	'href' => "#ASC",
	'priority' => 2,
));

elgg_register_menu_item('todo-submission-sort-menu', array(
	'name' => 'todo_user_submissions_date_range',
	'text' => elgg_echo('todo:label:date') . ": " . elgg_view('input/text', array(
		'name' => 'todo_user_submissions_date_input',
		'class' => 'todo-user-submissions-date-input',
		'readonly' => 'READONLY',
	)),
	'class' => 'todo-user-submissions-date-range',
	'selected' => FALSE,
	'href' => FALSE,
	'priority' => 3,
));

$filter_menu = elgg_view_menu('todo-submission-sort-menu', array(
	'class' => 'elgg-menu-hz elgg-menu-submissions-sort',
));

$js = <<<JAVASCRIPT
	<script>
		// Destroy/reload modules
		elgg.modules.genericmodule.destroy();
		elgg.modules.genericmodule.init();
		
		// Init daterangepicker
		elgg.todo.initDateRangePicker('input.todo-user-submissions-date-input');
	</script>
JAVASCRIPT;

$content = <<<HTML
	<div id='todo-user-submissions-filter-menu'>
		$filter_menu
	</div>
	$module
	$js
HTML;

echo $content;