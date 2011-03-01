<?php
/**
 * Todo To Do's assigned to me
 * 
 * @package Todo
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// include the Elgg engine
include_once dirname(dirname(dirname(dirname(__FILE__)))) . "/engine/start.php"; 

// Logged in users only
gatekeeper();
group_gatekeeper();

global $CONFIG;
	
// if username or owner_guid was not set as input variable, we need to set page owner
// Get the current page's owner 
$page_owner = page_owner_entity();
if (!$page_owner) {
	$page_owner_guid = get_loggedin_userid();
	if ($page_owner_guid) {
		set_page_owner($page_owner_guid);
		$page_owner = page_owner_entity();
	}
}

$limit = get_input("limit", 10);
$offset = get_input("offset", 0);

$status = get_input('status', 'incomplete');

if (!in_array($status, array('complete', 'incomplete'))) {
	$status = 'incomplete';
}
	
if ($page_owner->getGUID() != get_loggedin_userid()) {
	elgg_push_breadcrumb(sprintf(elgg_echo('todo:title:ownedtodos'), $page_owner->name), "{$CONFIG->site->url}pg/todo/owned/" . $page_owner->username);
} else {
	elgg_push_breadcrumb(elgg_echo('todo:title:assignedtodos'), "{$CONFIG->site->url}pg/todo/{$page_owner->username}");
}

// Get all assigned todos
$assigned_entities = get_users_todos($page_owner->getGUID());
	
// Build list based on status
if ($status == 'complete') {
	elgg_push_breadcrumb(elgg_echo('todo:label:complete'), "{$CONFIG->site->url}pg/todo/{$page_owner->username}?status=complete");
	foreach ($assigned_entities as $entity) {
		if (has_user_submitted($page_owner->getGUID(), $entity->getGUID())) {
			$entities[] = $entity;
		}
	}
	sort_todos_by_due_date($entities);
	
	$list .= elgg_view_entity_list(array_slice($entities, $offset, $limit), count($entities), $offset, $limit, false, false, true);
	
} else if ($status == 'incomplete') {	
	elgg_push_breadcrumb(elgg_echo('todo:label:incomplete'), "{$CONFIG->site->url}pg/todo/{$page_owner->username}?status=incomplete");	
	foreach ($assigned_entities as $entity) {
		if (!has_user_submitted($page_owner->getGUID(), $entity->getGUID())) {
			$entities[] = $entity;
		}
	}
	
	$today = strtotime(date("F j, Y"));
	$next_week = strtotime("+7 days", $today);
	
	if ($past_entities = get_todos_due_before($entities, $today)) {
		$list .= elgg_view('todo/todoheader', array('value' => elgg_echo("todo:label:pastdue"), 'priority' => TODO_PRIORITY_HIGH));
		sort_todos_by_due_date($past_entities);
		$list .= elgg_view_entity_list($past_entities, count($past_entities), 0, 9999, false, false, false);
	}
			
	if ($nextweek_entities = get_todos_due_between($entities, $today, $next_week)) {
		$list .= elgg_view('todo/todoheader', array('value' => elgg_echo("todo:label:nextweek"), 'priority' => TODO_PRIORITY_MEDIUM));
		sort_todos_by_due_date($nextweek_entities);
		$list .= elgg_view_entity_list($nextweek_entities, count($nextweek_entities), 0, 9999, false, false, false);
	}
	
	if ($future_entities = get_todos_due_after($entities, $next_week)) {
		$list .= elgg_view('todo/todoheader', array('value' => elgg_echo("todo:label:future"), 'priority' => TODO_PRIORITY_LOW));
		sort_todos_by_due_date($future_entities);
		$list .= elgg_view_entity_list($future_entities, count($future_entities), 0, 9999, false, false, false);
	}
}

// Start building content
$content .= elgg_view('navigation/breadcrumbs');

if ($page_owner instanceof ElggGroup || $page_owner->getGUID() != get_loggedin_userid()) {
	$title = sprintf(elgg_echo('todo:title:ownedtodos'), $page_owner->name);
	$tabs = array(
		'assigned' => array(
			'title' => 'Assigned to ' . $page_owner->name,
			'url' => $CONFIG->wwwroot . 'pg/todo/' . $page_owner->username,
			'selected' => true,
		),
		'owned' => array(
			'title' => 'Assigned by ' . $page_owner->name,
			'url' => $CONFIG->wwwroot . 'pg/todo/owned/' . $page_owner->username,
			'selected' => false,
		)
	);
					
	$content .= elgg_view('page_elements/content_header', array('tabs' => $tabs, 'type' => 'todo', 'new_link' => $CONFIG->url . $new_link));
} else {
	$title = elgg_echo('todo:title:assignedtodos');
	$content .= get_todo_content_header('assigned', $new_link = "pg/todo/createtodo/");
}	
	

$content .= elgg_view('todo/nav_showbycomplete', array('return_url' => 'pg/todo'));

if ($list) {
	$content .= $list;
} else {
	$content .= elgg_view('todo/noresults');
}

// layout the sidebar and main column using the default sidebar
$body = elgg_view_layout('one_column_with_sidebar', $content, '');

// create the complete html page and send to browser
echo elgg_view_page($title, $body);
