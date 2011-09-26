<?php

$type = get_input('type', 'all');
$status = get_input('status', 'incomplete');
$sort_order = get_input('sort_order', 'DESC');
$container_guid = get_input('u', elgg_get_logged_in_user_guid()); 

// Set up secondary menu nav items
elgg_register_menu_item('todo-dashboard-secondary', array(
	'name' => 'todo_incomplete',
	'text' => elgg_echo('todo:label:incomplete'),
	'class' => 'todo-ajax-list-complete',
	'item_class' => 'todo-ajax-list-complete-item',
	'selected' => $status === 'incomplete',
	'href' => "ajax/view/todo/list?type={$type}&status=incomplete&u={$container_guid}",
	'priority' => 1
));

elgg_register_menu_item('todo-dashboard-secondary', array(
	'name' => 'todo_complete',
	'text' => elgg_echo('todo:label:complete'),
	'class' => 'todo-ajax-list-complete',
	'item_class' => 'todo-ajax-list-complete-item',
	'selected' => $status === 'complete',
	'href' => "ajax/view/todo/list?type={$type}&status=complete&u={$container_guid}",
	'priority' => 2
));

elgg_register_menu_item('todo-sort-menu', array(
	'name' => 'todo_sort_asc',
	'text' => elgg_echo('todo:label:sortasc'),
	'class' => 'todo-ajax-sort',
	'selected' => $sort_order === 'ASC',
	'href' => "ajax/view/todo/list?type={$type}&status={$status}&sort_order=ASC&u={$container_guid}",
));

elgg_register_menu_item('todo-sort-menu', array(
	'name' => 'todo_sort_desc',
	'text' => elgg_echo('todo:label:sortdesc'),
	'class' => 'todo-ajax-sort',
	'selected' => $sort_order === 'DESC',
	'href' => "ajax/view/todo/list?type={$type}&status={$status}&sort_order=DESC&u={$container_guid}", 
));

$sort_menu = elgg_view_menu('todo-sort-menu', array(
	'class' => 'elgg-menu-hz elgg-menu-todo-sort',
));

$secondary_menu = elgg_view_menu('todo-dashboard-secondary', array(
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz elgg-menu-filter elgg-menu-filter-default'
));

if ($type != 'owned') {
	echo $secondary_menu;
}

echo $sort_menu;

echo "<div id='todo-dashboard-content'>";
echo list_todos(array(
	'context' => $type,
	'status' => $status,
	'sort_order' => $sort_order,
	'container_guid' => $container_guid,
));
echo "</div>";