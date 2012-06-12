<?php
/**
 * Todo ajax list view
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

$type = get_input('type', 'all');
$filter_priority = get_input('filter_priority', null);
$status = get_input('status', 'incomplete');
$sort_order = get_input('sort_order', 'DESC');
$container_guid = get_input('u', elgg_get_logged_in_user_guid()); 

// Set up secondary menu nav items
elgg_register_menu_item('todo-dashboard-secondary', array(
	'name' => 'todo_incomplete',
	'text' => $type == 'owned' ? elgg_echo('todo:label:statusincomplete') : elgg_echo('todo:label:incomplete'),
	'class' => 'todo-ajax-filter',
	'item_class' => 'todo-ajax-filter-item',
	'selected' => $status === 'incomplete',
	'href' => "ajax/view/todo/list?type={$type}&status=incomplete&u={$container_guid}",
	'priority' => 1
));

elgg_register_menu_item('todo-dashboard-secondary', array(
	'name' => 'todo_complete',
	'text' => elgg_echo('todo:label:complete'),
	'class' => 'todo-ajax-filter',
	'item_class' => 'todo-ajax-filter-item',
	'selected' => $status === 'complete',
	'href' => "ajax/view/todo/list?type={$type}&status=complete&u={$container_guid}",
	'priority' => 2
));

if ($type == 'assigned' && submissions_gatekeeper($container_guid)) {
	elgg_register_menu_item('todo-dashboard-secondary', array(
		'name' => 'todo_submissions',
		'text' => elgg_echo('todo:label:submissions'),
		'class' => 'todo-ajax-filter',
		'item_class' => 'todo-ajax-filter-item',
		'selected' => $status === 'submissions',
		'href' => "ajax/view/todo/list?type={$type}&status=submissions&u={$container_guid}",
		'priority' => 3
	));	
}

if (!$filter_priority) {
	elgg_register_menu_item('todo-sort-menu', array(
		'name' => 'todo_sort_asc',
		'text' => elgg_echo('todo:label:sortasc'),
		'class' => 'todo-ajax-sort',
		'selected' => $sort_order === 'ASC',
		'href' => "ajax/view/todo/list?type={$type}&status={$status}&sort_order=ASC&u={$container_guid}$",
	));

	elgg_register_menu_item('todo-sort-menu', array(
		'name' => 'todo_sort_desc',
		'text' => elgg_echo('todo:label:sortdesc'),
		'class' => 'todo-ajax-sort',
		'selected' => $sort_order === 'DESC',
		'href' => "ajax/view/todo/list?type={$type}&status={$status}&sort_order=DESC&u={$container_guid}", 
	));
}

if ($status == 'incomplete') {
	$filter_input = elgg_view('input/dropdown', array(
		'id' => 'todo-filter-due',
		'class' => 'todo-filter',
		'options_values' => array(
			0 => elgg_echo('all'),
			TODO_PRIORITY_HIGH => elgg_echo("todo:label:pastdue"),
			TODO_PRIORITY_TODAY => elgg_echo("todo:label:today"),
			TODO_PRIORITY_MEDIUM => elgg_echo("todo:label:nextweek"),
			TODO_PRIORITY_LOW => elgg_echo("todo:label:future"),
		),
		'value' => $filter_priority,
	));

	$filter_label = elgg_echo('todo:label:show');
	$filter_url = "ajax/view/todo/list?type={$type}&status={$status}&u={$container_guid}";
	$filter_link = "<a href='{$filter_url}' style='display: none;'></a>";
	$filter_text = "<label>$filter_label:</label>&nbsp;$filter_input $filter_link";

	elgg_register_menu_item('todo-sort-menu', array(
		'name' => 'todo_sort_priority',
		'text' => $filter_text,
		'href' => FALSE, 
	));
}

$sort_menu = elgg_view_menu('todo-sort-menu', array(
	'class' => 'elgg-menu-hz elgg-menu-todo-sort',
));

$secondary_menu = elgg_view_menu('todo-dashboard-secondary', array(
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
));

echo $secondary_menu;

// List todos
if ($status != 'submissions') {
	
	$options = array(
		'context' => $type,
		'status' => $status,
		'sort_order' => $sort_order,
		'container_guid' => $container_guid,
		'list' => TRUE,
	);

	$today = strtotime(date("F j, Y"));
	$next_week = strtotime("+7 days", $today);
	
	switch ($filter_priority) {
		case TODO_PRIORITY_HIGH;
			$options['due_date'] = $today;
			$options['due_operand'] = '<';
			break;
		case TODO_PRIORITY_TODAY;
			$options['due_date'] = $today;
			$options['due_operand'] = '=';
			break;
		case TODO_PRIORITY_MEDIUM;
			$options['due_start'] = $today;
			$options['due_end'] = $next_week;
			break;
		case TODO_PRIORITY_LOW;
			$options['due_date'] = $next_week;
			$options['due_operand'] = '>';
			break;
		default:
			$options['due_date'] = FALSE;
			break;
	}	

	echo "<div id='todo-dashboard-content' class='todo-dashboard-content-pagination-helper'>";
	echo $sort_menu;
	echo get_todos($options);
} else {
	echo "<div id='todo-dashboard-content'>";
	if (submissions_gatekeeper($container_guid)) {
		echo "<div class='todo-user-submissions-content'>";
		echo elgg_view('todo/user_submissions', array(
			'user_guid' => $container_guid,
		));
		echo "</div>";
	} else {
		echo elgg_echo('todo:error:access');
	}

}

echo "</div>";	
